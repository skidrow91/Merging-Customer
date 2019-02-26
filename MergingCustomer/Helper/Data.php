<?php

namespace Axl\MergingCustomer\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Magento\Sales\Api\OrderAddressRepositoryInterface
     */
    protected $orderAddressInterface;
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;
    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $wishlistCollection;
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $wlItemCollection;
    /**
     * @var \Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory
     */
    protected $productAlertPriceCollection;
    /**
     * @var \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory
     */
    protected $productAlertStockCollection;
    /**
     * @var \Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory
     */
    protected $paymentTokenCollection;
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexer;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var \Axl\MergingCustomer\Model\Merging\CustomerFactory
     */
    protected $mergingCustomer;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected $customer = null;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected $oldCustomer = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressInterface
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistCollection
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wlItemCollection
     * @param \Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory $productAlertPriceCollection
     * @param \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $productAlertStockCollection
     * @param \Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory $paymentTokenCollection
     * @param \Magento\Indexer\Model\IndexerFactory $indexer
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Axl\MergingCustomer\Model\Merging\CustomerFactory $mergingCustomer
     * @param \Magento\Framework\App\ResourceConnection $connection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressInterface,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistCollection,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wlItemCollection,
        \Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory $productAlertPriceCollection,
        \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $productAlertStockCollection,
        \Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory $paymentTokenCollection,
        \Magento\Indexer\Model\IndexerFactory $indexer,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Axl\MergingCustomer\Model\Merging\CustomerFactory $mergingCustomer,
        \Magento\Framework\App\ResourceConnection $connection
    ) {
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->customerRepository = $customerRepository;
        $this->orderAddressInterface = $orderAddressInterface;
        $this->addressRepository = $addressRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->wishlistCollection = $wishlistCollection;
        $this->wishlistFactory = $wishlistFactory;
        $this->wlItemCollection = $wlItemCollection;
        $this->productAlertPriceCollection = $productAlertPriceCollection;
        $this->productAlertStockCollection = $productAlertStockCollection;
        $this->paymentTokenCollection = $paymentTokenCollection;
        $this->indexer = $indexer;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->mergingCustomer = $mergingCustomer;
        $this->connection = $connection;
    }

    public function merge($from, $to)
    {
        $this->initCustomer($from, $to);
        $results = $this->mergeAddress();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeOrder();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeQuote();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeShipment();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeReviewing();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeWistlist();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeProductAlertPrice();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeProductAlertStock();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->mergeVaultToken();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->insertRecording();
        if (!$results['success']) {
            return $results;
        }

        $results = $this->deleteCustomer();
        if (!$results['success']) {
            return $results;
        }

        $this->reindex();

        return [
            'success' => 1,
            'message' => __('Customer #%1 has been merged to customer #%2', $this->oldCustomer->getFirstname() . ' ' . $this->oldCustomer->getLastname(), $this->customer->getFirstname() . ' ' . $this->customer->getLastname()),
        ];
    }

    protected function initCustomer($from, $to)
    {
        if (!$this->customer) {
            $this->customer = $this->customerRepository->getById($to);
        }
        if (!$this->oldCustomer) {
            $this->oldCustomer = $this->customerRepository->get($from);
        }
    }

    protected function mergeAddress()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('parent_id', $this->oldCustomer->getId(), 'eq')->create();
                $addresses = $this->addressRepository->getList($searchCriteriaBuilder);
                foreach ($addresses->getItems() as $address) {
                    $address->setCustomerId($this->customer->getId());
                    $address->setIsDefaultShipping(false);
                    $address->setIsDefaultBilling(false);
                    $this->addressRepository->save($address);
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeOrder()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('customer_id', $this->oldCustomer->getId(), 'eq')->create();
                $orders = $this->orderRepository->getList($searchCriteriaBuilder);
                foreach ($orders->getItems() as $order) {
                    $order->setCustomerId($this->customer->getId());
                    $order->setCustomerGroupId($this->customer->getGroupId());
                    $order->setCustomerEmail($this->customer->getEmail());
                    $order->setCustomerFirstname($this->customer->getFirstname());
                    $order->setCustomerLastname($this->customer->getLastname());
                    $order->setCustomerMiddlename($this->customer->getMiddlename());
                    $order->setCustomerPrefix($this->customer->getPrefix());
                    $order->setCustomerSuffix($this->customer->getSuffix());
                    $order->setCustomerTaxvat($this->customer->getTaxvat());
                    $order->setCustomerGender($this->customer->getGender());
                    $this->orderRepository->save($order);
                    $billingAddress = $this->orderAddressInterface->get($order->getBillingAddressId());
                    $billingAddress->setEmail($this->customer->getEmail());
                    $this->orderAddressInterface->save($billingAddress);
                    $shippingAddress = $this->orderAddressInterface->get($order->getShippingAddressId());
                    $shippingAddress->setEmail($this->customer->getEmail());
                    $this->orderAddressInterface->save($shippingAddress);
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeShipment()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('customer_id', $this->oldCustomer->getId(), 'eq')->create();
                $shipments = $this->shipmentRepository->getList($searchCriteriaBuilder);
                foreach ($shipments->getItems() as $shipment) {
                    $shipment->setCustomerId($this->customer->getId());
                    $this->shipmentRepository->save($shipment);
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeQuote()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('customer_id', $this->oldCustomer->getId(), 'eq')->create();
                $carts = $this->cartRepository->getList($searchCriteriaBuilder);
                foreach ($carts->getItems() as $cart) {
                    $cart->setCustomer($this->customer);
                    $this->cartRepository->save($cart);
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeReviewing()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $connection = $this->connection->getConnection();
                $tableName = $connection->getTableName('review_detail');
                $sql = sprintf("Update %s Set customer_id = %d where customer_id = %d", $tableName, $this->customer->getId(), $this->oldCustomer->getId());
                $connection->query($sql);
                $tableName = $connection->getTableName('rating_option_vote');
                $sql = sprintf("Update %s Set customer_id = %d where customer_id = %d", $tableName, $this->customer->getId(), $this->oldCustomer->getId());
                $connection->query($sql);
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeWistlist()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $collection = $this->wishlistCollection->create()
                    ->filterByCustomerId($this->oldCustomer->getId());
                if ($collection->count()) {
                    $masterWishlist = $this->wishlistCollection->create()
                        ->filterByCustomerId($this->customer->getId())
                        ->getFirstItem();
                    if (!$masterWishlist->getId()) {
                        $masterWishlist = $this->wishlistFactory->create();
                        $masterWishlist->loadByCustomerId($this->customer->getId(), true);
                    }
                    foreach ($collection as $wishlist) {
                        $items = $wishlist->getItemCollection();
                        foreach ($items as $item) {
                            $wlItem = $this->wlItemCollection->create()
                                ->addFieldToFilter('product_id', $item->getProductId())
                                ->addFieldToFilter('wishlist_id', $masterWishlist->getId())
                                ->addFieldToFilter('store_id', $item->getStoreId())
                                ->getLastItem();
                            if ($wlItem->getId()) {
                                $qty = $item->getQty();
                                $wlItem->setQty($wlItem->getQty() + $qty);
                                $wlItem->save();
                                $item->delete();
                            } else {
                                $item->setWishlistId($masterWishlist->getId());
                                $item->save();
                            }
                        }
                    }
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeProductAlertPrice()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $collection = $this->productAlertPriceCollection->create()
                    ->addFieldToFilter('customer_id', $this->oldCustomer->getId());
                foreach ($collection as $item) {
                    $masterCollection = $this->productAlertPriceCollection->create()
                        ->addFieldToFilter('customer_id', $this->customer->getId())
                        ->addFieldToFilter('product_id', $item->getProductId());
                    if ($masterCollection->count()) {
                        $item->delete();
                    } else {
                        $item->setCustomerId($this->customer->getId());
                        $item->save();
                    }
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeProductAlertStock()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $collection = $this->productAlertStockCollection->create()
                    ->addFieldToFilter('customer_id', $this->oldCustomer->getId());
                foreach ($collection as $item) {
                    $masterCollection = $this->productAlertStockCollection->create()
                        ->addFieldToFilter('customer_id', $this->customer->getId())
                        ->addFieldToFilter('product_id', $item->getProductId());
                    if ($masterCollection->count()) {
                        $item->delete();
                    } else {
                        $item->setCustomerId($this->customer->getId());
                        $item->save();
                    }
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function mergeVaultToken()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $collection = $this->paymentTokenCollection->create()
                    ->addFieldToFilter('customer_id', $this->oldCustomer->getId());
                foreach ($collection as $item) {
                    $item->setCustomerId($this->customer->getId());
                    $item->save();
                }
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function deleteCustomer()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $this->customerRepository->delete($this->oldCustomer);
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }

    protected function reindex()
    {
        $indexer = $this->indexer->create()->load('customer_grid');
        $indexer->reindexAll();
    }

    protected function insertRecording()
    {
        $results = [
            'success' => 1,
            'message' => '',
        ];
        if ($this->customer->getId() && $this->oldCustomer->getId()) {
            try {
                $mergingCustomer = $this->mergingCustomer->create();
                $mergingCustomer->setCustomerId($this->customer->getId());
                $mergingCustomer->setFromCustomer($this->oldCustomer->getEmail());
                $mergingCustomer->setToCustomer($this->customer->getEmail());
                $mergingCustomer->save();
            } catch (\Exception $ex) {
                $results['success'] = 0;
                $results['message'] = $ex->getMessage();
            }
        }
        return $results;
    }
}
