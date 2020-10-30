<?php

namespace Concrete\Package\BrandCentralConnector\Controller\Dialog\BrandCentralConnector;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\ExternalFileProvider\ExternalFileProviderFactory;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration\BrandCentralConfiguration;
use Exception;

class AssetFileSelector extends UserInterface
{
    protected $viewPath = '/dialogs/brand_central_connector/asset_file_selector';

    /**
     * @param null $externalFileProviderId
     * @param null $assetId
     * @throws Exception
     */
    public function view($externalFileProviderId = null, $assetId = null)
    {
        /** @var ExternalFileProviderFactory $externalFileProviderFactory */
        $externalFileProviderFactory = $this->app->make(ExternalFileProviderFactory::class);
        $externalFileProvider = $externalFileProviderFactory->fetchByID((int)$externalFileProviderId);

        if ($this->request->query->has ("externalFileProviderUploadDirectoryId")) {
            $externalFileProviderUploadDirectoryId = $this->request->query->get("externalFileProviderUploadDirectoryId");
        } else {
            throw new Exception(t("You need to submit a valid upload directory id."));
        }

        if ($externalFileProvider instanceof ExternalFileProvider) {
            /** @var BrandCentralConfiguration $config */
            $config = $externalFileProvider->getConfigurationObject();

            $assetDetails = $config->getAssetDetails($assetId);

            $this->set('externalFileProviderId', (int)$externalFileProviderId);
            $this->set('externalFileProviderUploadDirectoryId', (int)$externalFileProviderUploadDirectoryId);
            $this->set('assetDetails', $assetDetails);
        } else {
            throw new Exception(t("The given external file provider doesn't exists."));
        }
    }

    public function submit()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);

        $errorList = new ErrorList();
        $response = new EditResponse();
        $response->setTitle(t("Successfully imported"));
        $response->setMessage(t("The asset file has been successfully imported to your file manager."));

        $formValidator->setData($this->request->request->all());

        $formValidator->addRequiredToken("select_asset_file");
        $formValidator->addRequired("externalFileProviderId");
        $formValidator->addRequired("externalFileProviderUploadDirectoryId");
        $formValidator->addRequired("remoteFileId");

        if ($formValidator->test()) {
            /** @var ExternalFileProviderFactory $externalFileProviderFactory */
            $externalFileProviderFactory = $this->app->make(ExternalFileProviderFactory::class);

            $externalFileProvider = $externalFileProviderFactory->fetchByID((int)$this->request->request->get("externalFileProviderId"));

            if ($externalFileProvider instanceof ExternalFileProvider) {
                try {
                    /** @var BrandCentralConfiguration $config */
                    $config = $externalFileProvider->getConfigurationObject();
                    $importedFile = $config->importFile(
                        (int)$this->request->request->get("remoteFileId"),
                        (int)$this->request->request->get("externalFileProviderUploadDirectoryId")
                    );

                    if ($importedFile instanceof Version) {
                        $response->setAdditionalDataAttribute("importedFileId", $importedFile->getFileID());
                    } else {
                        $errorList->add(t("There was en error while importing the file."));
                    }

                } catch (Exception $err) {
                    // Whoops. There was an error while importing the file...
                    $errorList->add($err);
                }
            } else {
                $errorList->add(t("The given external file provider doesn't exists."));
            }
        } else {
            $errorList = $formValidator->getError();
        }

        $response->setError($errorList);

        return $responseFactory->json($response);
    }

    public function canAccess()
    {
        $folder = $this->app->make(Filesystem::class)->getRootFolder();

        if ($folder === null) {
            $folder = new FileFolder();
        }

        $permissionChecker = new Checker($folder);

        /** @noinspection PhpUnhandledExceptionInspection */
        return $permissionChecker->getResponseObject()->validate("add_file");
    }

}
