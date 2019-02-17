<?php

namespace Axl\MergingCustomer\Controller\Adminhtml\Merging;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Axl\MergingCustomer\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Axl\MergingCustomer\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getPostValue('merging_customer');
        if ($params) {
            $results = $this->helper->merge($params['from'], $params['customer_id']);
            return $this->resultJsonFactory->create()->setData($results);
        }
        return $this->_redirect($this->getUrl('customer/index/'));
    }

}
