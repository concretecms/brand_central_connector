<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Routing\Router;

/**
 * @var Router $router
 * Base path: /ccm/system/file
 * Namespace: \Concrete\Package\BrandCentralConnector\Controller\Backend
 */

$router->all('/chooser/assets', 'File\Chooser::getAssets');
$router->all('/chooser/assets/{assetId}', 'File\Chooser::getAssets');
