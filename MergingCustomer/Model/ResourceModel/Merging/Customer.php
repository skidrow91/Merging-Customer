<?php

namespace Axl\MergingCustomer\Model\ResourceModel\Merging;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('merging_customer', 'entity_id');
    }
}
