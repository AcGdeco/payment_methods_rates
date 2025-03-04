require([
    'jquery',
    'Magento_Checkout/js/model/quote'
], function ($,quote) {
    'use strict';

    quote.paymentMethod.subscribe(function(){$("#payment_form_cc_pagbank_paymentmagento_cc select").val("0")}, null, 'change');
});