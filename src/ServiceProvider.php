<?php

namespace Concrete5\BrandCentralConnector;

use Concrete\Core\Foundation\Psr4ClassLoader;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Html\Service\Html;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Router;
use Concrete\Core\View\View;
use Concrete5\BrandCentralConnector\Routing\RouteList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->initializeFileStorageType();
        $this->initializeRoutes();
        $this->initializeJavaScriptComponent();
    }

    public function initializeRoutes()
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        /** @var RouteList $routeList */
        $routeList = $this->app->make(RouteList::class);
        $routeList->loadRoutes($router);
    }

    private function initializeJavaScriptComponent()
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->app->make(EventDispatcherInterface::class);
        $eventDispatcher->addListener("on_before_render", function () {
            $page = Page::getCurrentPage();
            if (is_object($page) && ($page->isSystemPage() || $page->isEditMode())) {
                /** @var Html $htmlHelper */
                $htmlHelper = $this->app->make(Html::class);
                View::getInstance()->addFooterItem($htmlHelper->javascript("brand-central-connector.js", 'brand_central_connector'));
            }
        });
    }

    private function initializeFileStorageType()
    {

        $loader = new Psr4ClassLoader();

        $loader->addPrefix('\\Concrete5\\BrandCentralConnector\\File\\ExternalFileProvider\\Configuration', 'brand_central_connector' . '/src/File/ExternalFileProvider/Configuration/');

        $loader->addPrefix(
            '\\Concrete\\Package\\BrandCentralConnector\\Core\\File\\ExternalFileProvider\\Configuration',
            'brand_central_connector' . '/src/File/ExternalFileProvider/Configuration/'
        );

        $loader->addPrefix(
            '\\Concrete\\Package\\BrandCentralConnector\\Src\\File\\ExternalFileProvider\\Configuration',
            'brand_central_connector' . '/src/File/ExternalFileProvider/Configuration/'
        );

        $loader->addPrefix(
            '\\Concrete\\Package\\BrandCentralConnector\\File\\ExternalFileProvider\\Configuration',
            'brand_central_connector' . '/src/File/ExternalFileProvider/Configuration/'
        );

        $loader->register();

        $this->app->bind(
            'Concrete\Package\BrandCentralConnector\Src\File\ExternalFileProvider\Configuration\BrandCentralConfiguration',
            'Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration\BrandCentralConfiguration'
        );

        $this->app->bind(
            'Concrete\Package\BrandCentralConnector\Core\File\ExternalFileProvider\Configuration\BrandCentralConfiguration',
            'Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration\BrandCentralConfiguration'
        );

        $this->app->bind(
            'Concrete\Package\BrandCentralConnector\File\ExternalFileProvider\Configuration\BrandCentralConfiguration',
            'Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration\BrandCentralConfiguration'
        );
    }

}
