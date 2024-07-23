<?php
namespace Razoyo\CarProfile\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CarInfo extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('customer_carinfo', 'id'); // 'customer_carinfo' is the table name, 'id' is the primary key
    }
}
