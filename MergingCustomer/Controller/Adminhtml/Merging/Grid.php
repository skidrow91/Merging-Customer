<?php
namespace Axl\MergingCustomer\Controller\Adminhtml\Merging;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
