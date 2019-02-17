<?php

namespace Axl\MergingCustomer\Model\ResourceModel\Merging\Customer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Axl\MergingCustomer\Model\Merging\Customer',
            'Axl\MergingCustomer\Model\ResourceModel\Merging\Customer'
        );
    }

    /**
     * Add customer id filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerIdFilter($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        return $this;
    }
}
