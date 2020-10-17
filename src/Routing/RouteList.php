<?php

namespace Concrete5\BrandCentralConnector\Routing;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setNamespace('Concrete\Package\BrandCentralConnector\Controller\Backend')
            ->setPrefix('/ccm/system/file')
            ->routes('actions/assets.php', 'brand_central_connector');
    }
}
