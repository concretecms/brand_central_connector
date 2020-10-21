<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Routing\Router;

/**
 * @var Router $router
 * Base path: /ccm/brand_central_connector
 * Namespace: \Concrete\Package\BrandCentralConnector\Controller\Dialog\BrandCentralConnector
 */

$router->all('/select_asset_file/submit', 'AssetFileSelector::submit');
$router->all('/select_asset_file/{externalFileProviderId}/{assetId}', 'AssetFileSelector::view');
