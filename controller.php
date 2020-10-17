<?php

namespace Concrete\Package\BrandCentralConnector;

use Concrete\Core\Package\Package;
use Concrete5\BrandCentralConnector\ServiceProvider;

class Controller extends Package
{
    protected $appVersionRequired = '9.0.0a3';
    protected $pkgVersion = '0.1.1';
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

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("install.xml");
    }

    public function install()
    {
        parent::install();
        $this->installContentFile("install.xml");
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }
}