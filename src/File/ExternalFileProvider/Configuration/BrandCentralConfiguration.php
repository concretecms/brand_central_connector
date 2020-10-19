<?php

namespace Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\ExternalFileProvider\ExternalFileEntry;
use Concrete\Core\File\ExternalFileProvider\ExternalFileList;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\File\ExternalFileProvider\Configuration\ConfigurationInterface;
use Concrete\Core\File\ExternalFileProvider\Configuration\Configuration;
use Concrete5\BrandCentralConnector\AssetDetails;

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
     * @param $assetId
     * @return AssetDetails
     */
    public function getAssetDetails($assetId)
    {
        // @todo: fetch asset details from endpoint

        $assetDetails = new AssetDetails();

        $assetDetails->setTitle("Asset Details " . $assetId);
        $assetDetails->setDescription("lorem ipsum");
        $assetDetails->setThumbnailUrl("http://placehold.it/200x200");

        $assetFiles = [];

        for ($i = 1; $i <= 5; $i++) {
            $assetFiles[$i] = "File " . $i;
        }

        $assetDetails->setFiles($assetFiles);

        return $assetDetails;
    }

    /**
     * @param $fileId
     * @return Version|null
     */
    public function importFile($fileId)
    {
        // @todo: fetch file data from endpoint and upload it to the file manager, then return the file version

        //throw new \Exception(t("Error while importing the file..."));

        $file = File::getByID(1);

        return $file->getApprovedVersion();
    }

    public function searchFiles($keyword, $selectedFileType)
    {
        $externalFileList = new ExternalFileList();

        // @todo: fetch files from endpoint

        for ($i = 1; $i <= 10; $i++) {
            $tempFile = new ExternalFileEntry();
            $tempFile->setFID($i);
            $tempFile->setTitle("Test " . $i);
            $tempFile->setThumbnailUrl("http://placehold.it/200x200");
            $tempFile->setFvDateAdded(new \DateTime());

            $externalFileList->addFile($tempFile);
        }

        return $externalFileList;
    }

    public function supportFileTypes()
    {
        return true;
    }

    public function getFileTypes()
    {
        // @todo: fetch asset types from endpoint

        return [
            'logos' => "Logos",
            'templates' => "Templates",
            'other' => "Other"
        ];
    }

    /**
     * This external file provider use a custom js import handler to display the select asset file popup.
     *
     * @see: js/brand-central-connector.js
     *
     * @return bool
     */
    public function hasCustomImportHandler()
    {
        return true;
    }
}