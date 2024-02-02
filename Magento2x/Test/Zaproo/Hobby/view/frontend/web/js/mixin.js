define([
    'jquery'
], function ($) {
    'use strict';

    return function () {
        $(document).ready(function () {
            $.ajax({
                url: '/hobby/customer/index',
                method: 'GET',
                dataType: 'json'
            }).done(function (data) {
                $('#customerHobby').html(data.hobby);
            });
        });
    };
});