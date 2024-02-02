define([
    "jquery"
], function($){
    "use strict";
 
    return function main(config, element) {
        $.ajax({
            url: '/hobby/customer/index',
            type: "GET",
            dataType: 'json'
        }).done(function (data) {
            $('#hobbyMessage').html(data.hobby);
            return true;
        });
 
    };
});