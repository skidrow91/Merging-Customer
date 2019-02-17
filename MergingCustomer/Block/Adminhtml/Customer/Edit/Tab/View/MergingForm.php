<?php

namespace Axl\MergingCustomer\Block\Adminhtml\Customer\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;

class MergingForm extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $systemStore;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    private $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Exto\StoreCredit\Model\StoreCredit $storeCredit
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->customerFactory = $customerFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('merging_customer_form');
        $this->setTitle(__('Merging Customer'));
    }

    /**
     * Get current customer id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
         * @var \Magento\Framework\Data\Form $form
         */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'merging_customer_form',
                    'action' => $this->getUrl(
                        'exto_store_credit/customer/balanceupdate',
                        ['_current' => true]
                    ),
                    'method' => 'post',
                ],
            ]
        );

        $form->setHtmlIdPrefix('merging_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Merging Customer'), 'class' => 'fieldset-wide']
        );
        $fieldset->addType(
            'customer_email',
            '\Axl\MergingCustomer\Block\Adminhtml\Customer\Edit\Renderer\Customer\Email'
        );

        // if ($this->_authorization->isAllowed('Exto_StoreCredit::balance_update')) {
            $fieldset->addField(
                'from',
                'customer_email',
                ['name' => 'from',
                    // 'class' => 'validate-number',
                    'label' => __('From'),
                    'title' => __('From'),
                    'required' => true,
                    // 'note' => __('Please enter customer email. Ex: abc@gmail.com'),
                ]
            );

            $fieldset->addField(
                'merging',
                'hidden',
                ['name' => 'from',
                    'label' => __('From'),
                    'title' => __('From')
                ]
            );

            $customer = $this->_getCustomer();
            $fieldset->addField(
                'customer_master_email',
                'label',
                [
                    'name' => 'customer_master_email',
                    'value' => $customer->getEmail(),
                    'label' => __('To'),
                ]
            );

            $fieldset->addField(
                'merge_customer',
                'button',
                [
                    'name' => 'merge-customer',
                    'value' => __('Merge Customer'),
                ]
            );
        // }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function _getCustomer() {
        $customer = $this->customerFactory->create()->load($this->getCustomerId());
        return $customer;
    }
}
