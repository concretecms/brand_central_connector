<?php

namespace Concrete5\BrandCentralConnector;

use Concrete\Core\File\Component\Chooser\ChooserConfigurationInterface;
use Concrete\Core\File\Component\Chooser\DefaultConfiguration;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Routing\Router;
use Concrete5\BrandCentralConnector\File\Component\Chooser\Option\BrandCentralOption;
use Concrete5\BrandCentralConnector\Routing\RouteList;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->initializeRoutes();
        $this->initializeFileChoosers();
    }

    private function initializeRoutes()
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        /** @var RouteList $routeList */
        $routeList = $this->app->make(RouteList::class);
        $routeList->loadRoutes($router);
    }

    private function initializeFileChoosers()
    {
        /** @var DefaultConfiguration $chooserConfiguration */
        $chooserConfiguration = $this->app->make(ChooserConfigurationInterface::class);
        $chooserConfiguration->addChooser(new BrandCentralOption());
    }
}
