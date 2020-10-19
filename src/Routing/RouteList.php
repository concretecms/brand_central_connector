<?php

namespace Concrete5\BrandCentralConnector\Routing;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setNamespace('Concrete\Package\BrandCentralConnector\Controller\Dialog\BrandCentralConnector')
            ->setPrefix('/ccm/brand_central_connector')
            ->routes('brand_central_connector.php', 'brand_central_connector');
    }
}
