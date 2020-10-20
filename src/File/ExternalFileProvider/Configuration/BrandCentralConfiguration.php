<?php

namespace Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\ExternalFileProvider\ExternalFileEntry;
use Concrete\Core\File\ExternalFileProvider\ExternalFileList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\File\ExternalFileProvider\Configuration\ConfigurationInterface;
use Concrete\Core\File\ExternalFileProvider\Configuration\Configuration;
use Concrete\Core\Http\Response;
use Concrete\Core\Support\Facade\Application;
use Concrete5\BrandCentralConnector\AssetDetails;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use Exception;

class BrandCentralConfiguration extends Configuration implements ConfigurationInterface
{
    public $endpoint;
    public $clientId;
    public $clientSecret;

    protected $formValidation;

    public function __construct(
        Validation $formValidation
    )
    {
        $this->formValidation = $formValidation;
    }

    public function loadFromRequest(Request $request)
    {
        $data = $request->request->all();

        $this->endpoint = $data['endpoint'];
        $this->clientId = $data['clientId'];
        $this->clientSecret = $data['clientSecret'];
    }

    public function validateRequest(Request $request)
    {
        $data = $request->request->all();

        $this->formValidation->setData($data);

        $this->formValidation->addRequired("endpoint");
        $this->formValidation->addRequired("clientId");
        $this->formValidation->addRequired("clientSecret");

        $this->formValidation->test();

        return $this->formValidation->getError();
    }

    /**
     * Perform an api request to the endpoint with OAUTH2-authentication.
     *
     * @param string $path
     * @param array $queryParams
     * @return array|null
     * @throws Exception
     */
    private function doRequest($path, $queryParams = [])
    {
        if (filter_var($this->endpoint, FILTER_VALIDATE_URL)) {
            $reAuthClient = new Client([
                'base_uri' => rtrim($this->endpoint, "/") . '/oauth/2.0/token',
            ]);

            $reAuthConfig = [
                "client_id" => $this->clientId,
                "client_secret" => $this->clientSecret
            ];

            $grantType = new ClientCredentials($reAuthClient, $reAuthConfig);
            $oAuth = new OAuth2Middleware($grantType);

            $stack = HandlerStack::create();
            $stack->push($oAuth);

            $client = new Client([
                'handler' => $stack,
                'auth' => 'oauth',
            ]);

            $queryString = http_build_query($queryParams);

            try {
                $url = trim($this->endpoint, "/") . "/" . ltrim($path, "/") . (strlen($queryString) > 0 ? "?" . $queryString : "");

                $response = $client->get($url);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    /** @noinspection PhpComposerExtensionStubsInspection */
                    $json = json_decode($response->getBody(), true);

                    if (isset($json["error"])) {
                        foreach ($json["errors"] as $error) {
                            throw new Exception($error);
                        }
                    }

                    return $json;
                } else {
                    throw new Exception(t("Invalid status code."));
                }
            } catch (ClientException $exception) {
                /** @noinspection PhpComposerExtensionStubsInspection */
                $json = json_decode($exception->getResponse()->getBody()->getContents(), true);

                if (isset($json["error"])) {
                    foreach ($json["errors"] as $error) {
                        throw new Exception($error);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param $assetId
     * @return AssetDetails
     * @throws Exception
     */
    public function getAssetDetails($assetId)
    {
        $data = $this->doRequest("/public_api/v1/assets/" . $assetId);

        $assetDetails = new AssetDetails();

        if (is_array($data)) {

            $assetDetails->setTitle($data["name"]);
            $assetDetails->setDescription($data["desc"]);
            $assetDetails->setThumbnailUrl($data["thumbnail"]);

            if (is_array($data["files"])) {
                $assetFiles = [];

                foreach ($data["files"] as $file) {
                    $assetFiles[$file["id"]] = $file["filename"];
                }

                $assetDetails->setFiles($assetFiles);
            }
        }

        return $assetDetails;
    }

    /**
     * @param $fileId
     * @return Version|null
     * @throws Exception
     */
    public function importFile($fileId)
    {
        $data = $this->doRequest("/public_api/v1/assets/get_file/" . $fileId);

        $fileVersion = null;

        if (is_array($data)) {
            $downloadUrl = $data["downloadUrl"];

            $app = Application::getFacadeApplication();
            /** @var VolatileDirectory $volatileDirectory */
            $volatileDirectory = $app->make(VolatileDirectory::class);
            /** @var FileImporter $fi */
            $fi = $app->make(FileImporter::class);

            $client = new Client();
            $response = $client->get($downloadUrl);

            if ($response->getStatusCode() !== 200) {
                throw new Exception(t(/*i18n: %1$s is an URL, %2$s is an error message*/ 'There was an error downloading "%1$s": %2$s', $downloadUrl, $response->getReasonPhrase() . ' (' . $response->getStatusCode() . ')'));
            }

            $matches = null;
            $filename = null;

            if (preg_match('/^[^#?]+[\\/]([-\w%]+\.[-\w%]+)($|\?|#)/', $downloadUrl, $matches)) {
                // got a filename (with extension)... use it
                $filename = $matches[1];
            } else {
                foreach ($response->getHeader('Content-Type') as $contentType) {
                    if (!empty($contentType)) {
                        list($mimeType) = explode(';', $contentType, 2);
                        $mimeType = trim($mimeType);
                        $extension = $app->make('helper/mime')->mimeToExtension($mimeType);

                        if ($extension === false) {
                            throw new Exception(t('Unknown mime-type: %s', h($mimeType)));
                        }

                        $filename = date('Y-m-d_H-i_') . mt_rand(100, 999) . '.' . $extension;
                    } else {
                        throw new Exception(t(/*i18n: %s is an URL*/ 'Could not determine the name of the file at %s', $downloadUrl));
                    }
                }
            }

            $fullFilename = $volatileDirectory->getPath() . '/' . $filename;
            // write the downloaded file to a temporary location on disk
            $handle = fopen($fullFilename, 'wb');
            fwrite($handle, $response->getBody());
            fclose($handle);

            $fileVersion = $fi->importLocalFile($fullFilename);
        }

        return $fileVersion;
    }

    /**
     * @param string $keyword
     * @param string $selectedFileType
     * @return ExternalFileList
     * @throws Exception
     */
    public function searchFiles($keyword, $selectedFileType)
    {
        $externalFileList = new ExternalFileList();

        $results = $this->doRequest("/public_api/v1/assets/search", [
            "keywords" => $keyword,
            "fileType" => $selectedFileType
        ]);

        if (is_array($results)) {
            foreach ($results as $result) {
                $tempFile = new ExternalFileEntry();
                $tempFile->setFID($result["id"]);
                $tempFile->setThumbnailUrl($result["thumbnail"]);
                $tempFile->setTitle($result["name"]);

                $externalFileList->addFile($tempFile);
            }
        }

        return $externalFileList;
    }

    public function supportFileTypes()
    {
        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getFileTypes()
    {
        $data = $this->doRequest("/public_api/v1/file_types");

        $fileTypes = [];

        if (is_array($data)) {
            foreach ($data as $item) {
                $fileTypes[$item["key"]] = $item["value"];
            }
        }

        return $fileTypes;
    }

    /**
     * This external file provider use a custom js import handler to display the select asset file popup.
     *
     * @return bool
     * @see: js/brand-central-connector.js
     *
     */
    public function hasCustomImportHandler()
    {
        return true;
    }
}