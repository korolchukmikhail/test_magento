define([
    'jquery',
    'ko',
    'uiComponent',
    "Magento_Customer/js/customer-data"
], function ($, ko, Component, CustomerData) {
    'use strict';

    return Component.extend({
        selectOptions: ko.observableArray([]),
        selected: ko.observable(),

        initialize: function () {
            this._super();

            this.selectOptions(this.options);
            this.selected(this.selectedOption);
        },

        submitForm: function () {
            console.log('Selected Option:', this.selected());

            $.ajax({
                url: this.url,
                method: 'POST',
                dataType: 'json',
                data: { "hobby": this.selected() },
                success: function (data) {
                    console.log(data);
                },
                error: function () {
                    console.error('Failed to fetch custom options');
                }
            });

            CustomerData.reload(['customer']);

            return false;
        }
    });
});