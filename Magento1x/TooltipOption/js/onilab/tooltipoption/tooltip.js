var jQuery12 = jQuery.noConflict(true);

jQuery12(document).ready(function(){
    jQuery12(".bundle_tooltip_icon").each(function(){
        var tooltipElement = jQuery12(this);
        tooltipElement.tooltipster({
            content: function() {
                return tooltipElement.next('.bundle_tooltip_content').html();
            }
        });
    });
});