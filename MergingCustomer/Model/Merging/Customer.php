<?php

namespace Axl\MergingCustomer\Model\Merging;

class Customer extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'axl_merging_customer';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Axl\MergingCustomer\Model\ResourceModel\Merging\Customer');
    }

}
