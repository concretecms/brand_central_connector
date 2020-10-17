<?php
namespace Concrete5\BrandCentralConnector\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;

class BrandCentralOption implements ChooserOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'brand-central';
    }

    public function getTitle(): string
    {
        return t('BrandCentral');
    }

}