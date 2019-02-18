<?php

namespace Axl\MergingCustomer\Controller\Adminhtml\Merging;

class GetCustomerEmail extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            return $this->_redirect('*/*/index');
        }
        $email = $this->getRequest()->getParam('email');
        $masterEmail = $this->getRequest()->getParam('master_customer');
        $results = [];
        if ($email) {
            $customerEmails = [];
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('email', ['like' => '%' . $email . '%']);
            if ($masterEmail){
                $collection->addFieldToFilter('email', ['neq' => $masterEmail]);
            }
            if (count($collection)) {
                foreach ($collection as $row) {
                    $customerEmails[] = $row->getEmail();
                }
            }
            $resultPage = $this->resultPageFactory->create();
            $block = $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setData('customer_emails', $customerEmails)
                ->setTemplate('Axl_MergingCustomer::renderer/customer/email.phtml');
            $results['html'] = $block->toHtml();
        }
        return $this->resultJsonFactory->create()->setData($results);
    }

}
