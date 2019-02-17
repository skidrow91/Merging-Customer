<?php

namespace Axl\MergingCustomer\Block\Adminhtml\Customer\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;

class Js extends \Magento\Framework\View\Element\Template
{

    protected $_coreRegistry;
    protected $_customerRepository;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerRepository = $customerRepository;
        parent::__construct($context, $data);
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getCustomerEmail()
    {
        if ($customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            $customerRepository = $this->_customerRepository->getById($customerId);
            if ($customerRepository->getId()) {
                return $customerRepository->getEmail();
            }
        }
        return "";
    }
}
