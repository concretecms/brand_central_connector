<?php

namespace Concrete\Package\BrandCentralConnector;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\ExternalFileProvider\Type\Type;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete5\BrandCentralConnector\ServiceProvider;

class Controller extends Package
{
    protected $appVersionRequired = '9.0.0a3';
    protected $pkgVersion = '0.1.4';
    protected $pkgHandle = 'brand_central_connector';
    protected $pkgDescription = '';
    protected $pkgAutoloaderRegistries = ['src' => 'Concrete5\BrandCentralConnector'];

    public function getPackageName()
    {
        return t('Brand Central Connector');
    }

    public function getPackageDescription()
    {
        return t('Connect a remote brand central site to your file manager.');
    }

    private function installOrUpdate()
    {
        // Install brand central external file provider

        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        $pkg = $packageService->getByHandle($this->getPackageHandle());

        $externalFileProvider = Type::getByHandle("brand_central");

        if (!$externalFileProvider instanceof Type) {
            Type::add('brand_central', t('Brand Central'), $pkg);
        }

        // Create BrandCentral folder
        $filesystem = new Filesystem();
        $folderName = t("BrandCentral");
        $rootFolder = $filesystem->getRootFolder();
        $folder = FileFolder::getNodeByName($folderName);

        if (!$folder instanceof FileFolder) {
            $createdFolder = $filesystem->addFolder($rootFolder, $folderName);
            /** @var Repository $config */
            $config = $this->app->make(Repository::class);
            $config->save("brand_central_connector.target_upload_directory_id", $createdFolder->getTreeNodeID());
        }

        //Add File Attribute
        $this->installContentFile("install.xml");
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installOrUpdate();
    }

    public function install()
    {
        parent::install();
        $this->installOrUpdate();
    }

    public function on_start()
    {
        if (file_exists($this->getPackagePath() . "/vendor/autoload.php")) {
            require_once($this->getPackagePath() . "/vendor/autoload.php");
        }

        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }
}