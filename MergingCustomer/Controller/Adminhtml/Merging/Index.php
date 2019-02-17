<?php

namespace Axl\MergingCustomer\Controller\Adminhtml\Merging;

class Index extends \Magento\Customer\Controller\Adminhtml\Index
{

    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }

}
