<?php
namespace Razoyo\CarProfile\Model;

use Magento\Framework\Model\AbstractModel;

class CarInfo extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Razoyo\CarProfile\Model\ResourceModel\CarInfo::class);
    }
}
