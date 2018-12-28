/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (ko, Component, quote, totals) {
        "use strict";
        var productionState = window.checkoutConfig.codilar_gst.production_state;
        var moduleStatus = window.checkoutConfig.codilar_gst.status;
        var shippingGstStatus = window.checkoutConfig.codilar_gst.shipping_status;
        productionState = productionState.replace(/\s/g,'');
        productionState = productionState.toLowerCase();
        var isTaxDisplayedInGrandTotal = window.checkoutConfig.includeTaxInGrandTotal;
        var isFullTaxSummaryDisplayed = window.checkoutConfig.isFullTaxSummaryDisplayed;
        var isZeroTaxDisplayed = window.checkoutConfig.isZeroTaxDisplayed;
        return Component.extend({
            defaults: {
                isTaxDisplayedInGrandTotal: isTaxDisplayedInGrandTotal,
                notCalculatedMessage: 'Not yet calculated',
                template: 'Codilar_Gst/checkout/summary/tax'
            },
            totals: quote.getTotals(),
            isFullTaxSummaryDisplayed: isFullTaxSummaryDisplayed,
            ifShowValue: function() {
                if (this.isFullMode() && this.getPureValue() == 0) {
                    return isZeroTaxDisplayed;
                }
                return true;
            },
            ifShowDetails: function() {
                if (!this.isFullMode()) {
                    return false;
                }
                return this.getPureValue() > 0 && isFullTaxSummaryDisplayed;
            },
            getPureValue: function() {
                var amount = 0;
                if (this.totals()) {
                    var taxTotal = totals.getSegment('tax');
                    if (taxTotal) {
                        amount = taxTotal.value;
                    }
                }
                return amount;
            },
            isCalculated: function() {
                return this.totals() && this.isFullMode() && null != totals.getSegment('tax');
            },
            getValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                var amount = totals.getSegment('tax').value;
                return this.getFormattedPrice(amount);
            },
            formatPrice: function(amount) {
                return this.getFormattedPrice(amount);

            },
            getModuleStatus: function() {
                if(moduleStatus=="1"){
                    return true;
                }
                return false;
            },
            getShippingGstStatus: function() {
                var shippingTaxAmount = quote.totals().shipping_tax_amount;
                if(shippingGstStatus=="1" && shippingTaxAmount>0 ){
                    return true;
                }
                return false;
            },
            getShippingAddress: function() {
                if(quote.shippingAddress()){
                    return true;
                }
                else{
                    return false;
                }
            },
            checkState: function() {
                var quoteState = quote.shippingAddress().region;
                if(!quote.shippingAddress() || !quoteState){
                    return false;
                }
                quoteState = quoteState.replace(/\s/g,'');
                quoteState = quoteState.toLowerCase();
                if(productionState==quoteState){
                    return true;
                }
                console.log("true");
                return false;
            },
            getSameStateTitle: function() {
                return "SGST";
            },
            getCentralStateTitle: function() {
                return "CGST";
            },
            getInterStateTitle: function() {
                return "IGST";
            },
            getShippingGstTitle: function() {
                return "Shipping GST";
            },
            getShippingTaxAmount: function() {
                var shippingTaxAmount = quote.totals().shipping_tax_amount;
                return shippingTaxAmount;
            },
            getInterStateAmount: function() {

                var taxAmount = quote.totals().tax_amount;
                if(shippingGstStatus=="1"){
                    var taxAmount = taxAmount - quote.totals().shipping_tax_amount;
                }
                return taxAmount;
            },
            getSameStateAmount: function() {

                var taxAmount = quote.totals().tax_amount;
                if(shippingGstStatus=="1"){
                    var taxAmount = taxAmount - quote.totals().shipping_tax_amount;
                }
                return taxAmount/2;
            },
            getDetails: function() {
                var taxSegment = totals.getSegment('tax');
                if (taxSegment && taxSegment.extension_attributes) {
                    return taxSegment.extension_attributes.tax_grandtotal_details;
                }
                return [];
            }
        });
    }
);
