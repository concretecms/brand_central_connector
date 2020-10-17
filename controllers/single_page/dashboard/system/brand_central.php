<?php

namespace Concrete\Package\BrandCentralConnector\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Url;

class BrandCentral extends DashboardPageController
{
    public function view()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        return $responseFactory->redirect(Url::to("/dashboard/system/brand_central/settings"));
    }
}