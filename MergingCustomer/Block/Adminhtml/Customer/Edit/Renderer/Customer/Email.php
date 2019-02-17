<?php

namespace Axl\MergingCustomer\Block\Adminhtml\Customer\Edit\Renderer\Customer;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;

class Email extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function getElementHtml()
    {
        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setData('customer_emails', [])
                ->setTemplate('Axl_MergingCustomer::renderer/customer/email.phtml');
                $results['html'] = $block->toHtml();
        return $block->toHtml();
    }
}
