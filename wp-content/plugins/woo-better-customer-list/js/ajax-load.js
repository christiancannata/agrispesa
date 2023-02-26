jQuery(document).ready(function($) {

    jQuery( ".blzbcluserot" ).each(function() {
        var blzbcluserot = jQuery( this );
        var blzbcldata = {
            'action': 'blz_bcl_caluot',
            'blzbcluserid': jQuery( this ).data( "user" )
        };
        jQuery.post(blz_bcl_ajax_object.ajax_url, blzbcldata, function(response) {
            blzbcluserot.html(response);
        });
    });

    jQuery( ".blzbcluseros" ).each(function() {
        var blzbcluseros = jQuery( this );
        var blzbcldata = {
            'action': 'blz_bcl_caluos',
            'blzbcluserid': jQuery( this ).data( "user" )
        };
        jQuery.post(blz_bcl_ajax_object.ajax_url, blzbcldata, function(response) {
            blzbcluseros.html(response);
        });
    });
});
