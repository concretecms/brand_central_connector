<?php

namespace Concrete\Package\BrandCentralConnector\Controller\Backend\File;

use Concrete\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class Chooser extends Controller
{
    public function getAssets()
    {
        // @todo: fetch assets from endpoint - perform a real api query

        return new JsonResponse([
            "data" => [
                [
                    "treeID" => 1,
                    "treeNodeID" => 2,
                    "resultsThumbnailImg" => "<i class=\"fa fa-folder\"></i>",
                    "lazy" => true,
                    "isFolder" => true,
                    "name" => "abc"
                ],
                [
                    "fvDateAdded" => "",
                    "fileName" => "",
                    "fID" => 2,
                    "resultsThumbnailImg" => ""
                ],
                [
                    "fvDateAdded" => "",
                    "fileName" => "",
                    "fID" => 2,
                    "resultsThumbnailImg" => ""
                ],
                [
                    "fvDateAdded" => "",
                    "fileName" => "",
                    "fID" => 2,
                    "resultsThumbnailImg" => ""
                ]
            ]
        ]);
    }
}
