<?php
namespace Razoyo\CarProfile\Model\ResourceModel\CarInfo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Razoyo\CarProfile\Model\CarInfo as CarInfoModel;
use Razoyo\CarProfile\Model\ResourceModel\CarInfo as CarInfoResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(CarInfoModel::class, CarInfoResourceModel::class);
    }
}
