<?php

namespace Concrete\Package\BrandCentralConnector;

use Concrete\Core\File\ExternalFileProvider\Type\Type;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete5\BrandCentralConnector\ServiceProvider;

class Controller extends Package
{
    protected $appVersionRequired = '9.0.0a3';
    protected $pkgVersion = '0.1.2';
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
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }
}