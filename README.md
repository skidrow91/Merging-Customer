# Merging Customer Magento 2


##### Features:

  - Merge 2 customers into one customer account and delete merging customer after complete. 
  - Transfers all orders (orders/shipments/invoices), quotes and related order data.
  - Transfers all customer addresses.
  - Transfers all product reviews, ratings.
  - Transfers all price alerts, stock alerts, wishlist, votes.
 
##### Install:
  - Copy directory Axl/MergingCustomer to app/code/
  - Run from terminal:
  ~~~~
        php bin/magento setup:upgrade
        php bin/magento setup:di:compile
        php bin/magento setup:static-content:deploy
        php bin/magento cache:clean
  ~~~~



