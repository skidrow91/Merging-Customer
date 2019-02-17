define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'mage/url'
],  function ($, Component, ko, fullScreenLoader, storage, urlBuilder) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Axl_MergingCustomer/email'
            },
            initialize: function () {
                this._super();
            },
        });
    }
);