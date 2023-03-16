(function($, window, document, undefined) {

    'use strict';

    jQuery(document).ready(function($) {

        if($('.wdap_timeslot_timepicker').length>0){
            $('.wdap_timeslot_timepicker').ptTimeSelect();
        }


        $(".wdap_check_key").click(function() {
            $('.wdap_maps_preview').html("...");
            var wdap_maps_key = $("input[name='wdap_googleapikey']").val();
            var address = 'london';
            $.get("https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=" + wdap_maps_key, function(data) {
                if (data.status == 'OK') {
                    $('.wdap_maps_preview').html("Perfect!");
                } else {
                    $('.wdap_maps_preview').html(data.error_message);
                }

            });

        });


        let wdap_enable_debug_form = $('#wdap_enable_debug_form');
     
        wdap_enable_debug_form.on('submit', function(e) { 
          
            e.preventDefault();  
            
            $('.wdap-error').remove();
            
            let formerror = false;
            
            $('#customer_email_address').removeClass('wdap-field-error');
            
            if($('#customer_email_address').val() == ''){
                formerror = true;
                $('#customer_email_address').addClass('wdap-field-error');
                return false;
            }
            
            var emailinput = $('#customer_email_address').val();
           
            var emailFormat = emailinput.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
            
            if( emailFormat === false ){ formerror = true; $('#customer_email_address').addClass('wdap-field-error'); return false; }
               
            
            //Validations passed, proceed request now.
               
            if(!formerror) {
                
                $('.fc-backend-loader').show();
                
                let ajaxurl = wdap_backend.ajax_url;
                jQuery.ajax( {
                    type : 'post',
                    dataType: 'json',
                    url : 'https://shop.woodeliveryarea.com/wp-admin/admin-ajax.php',
                    data : wdap_enable_debug_form.serialize(),
                } )

                
                .done( function( data ) { $('.fc-backend-loader').hide(); } )
                
                .fail( function( reason ) { $('.fc-backend-loader').hide(); } )
                
                // Promise finished:

                
                .then( function( data ) {
    
                    if(typeof data.success !== undefined && data.success == true) {
                        
                        jQuery.ajax( {
                            url :  ajaxurl,
                            type : 'post',
                            dataType: 'json',
                            data: { 
                                action: 'wdapenabledebug',
                                nonce: wdap_backend.nonce,
                                customer_subscription_data : data.customer_subscription_data
                                
                            }
                        } )
                        
                        .done( function( data ) {
                             window.location.reload();
                             $('.fc-backend-loader').hide(); 
                            
                             
                        } )
                        .fail( function( reason ) {
                              $('.fc-backend-loader').hide(); 
                        } );
                        
                    }else{
                        
                        $('.fc-backend-loader').after('<div class="fc-12 fc-msg fc-danger fade in py-2 wdap-error">'+data.result+'</div>');
                        
                    }
                    
                } );
            
                
            }
                            
            return false;
            
        });


        $(document).on("click", ".fc-disable-rating-notice .notice-dismiss", function(e) {

            var userID = $(this).parent().attr('data-userID');
            var data = {
                action: 'wdap_ajax_call',
                operation: 'wdap_update_notice',
                userID: userID,
                noncevalue: wdap_backend.nonce,
            }

            jQuery.ajax({
                type: "POST",
                url: wdap_backend.ajax_url,
                dataType: "json",
                data: data,
                success: function(response) {
                   
                },
                error: function(error) {
                    console.log(error.responseText);

                }

            });

        });


        if (typeof google !== typeof undefined) {

            var autocomplete;

            function initialize() {

                autocomplete = new google.maps.places.Autocomplete(
                    (document.getElementById('wdap_store_address')), {
                        types: ['geocode']
                    });
                google.maps.event.addListener(autocomplete, 'place_changed', function() {

                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    } else {
                        var placename = place.name + ', ' + place.formatted_address;

                        //Get postal code / zipcode
                        for (var i = 0; i < place.address_components.length; i++) {

                            for (var j = 0; j < place.address_components[i].types.length; j++) {

                                if (place.address_components[i].types[j] == "postal_code") {
                                    var place_zipcode = place.address_components[i].long_name;
                                }
                                if (place.address_components[i].types[j] == "country") {
                                    var place_country_name = place.address_components[i].short_name;
                                }

                            }
                        }

                        var address = {
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng(),
                            placename: $('#wdap_store_address').val()
                        };

                        if (typeof place_zipcode != typeof undefined) {
                            var autosuggest_zip_obj = {
                                placezipcode: place_zipcode
                            };
                            $.extend(address, autosuggest_zip_obj);
                        }
                        if (typeof place_country_name != typeof undefined) {
                            var place_country_name_obj = {
                                place_country_name: place_country_name
                            };
                            $.extend(address, place_country_name_obj);
                        }
                        $('#store_address_json').val(JSON.stringify(address));

                    }
                });
            }

            if ($('#wdap_store_address').length > 0) {

                initialize();
            }



        }

        //geocoading zipcodes
        $('body').on('click','input[name="fc-geocoding"]',function(e){
            
            e.preventDefault();
            $(this).hide();
            $(this).parent().parent().find('.fcdoc-loader').show(); 
            var new_locations = $(this).parent().parent().find('.fc-location-new-set');
            /* Start Geocoding */
            var source_csv_records = JSON.parse($(this).parent().parent().find('.fc-location-data-set').val());
            var final_records = [];
            var delay= 1000; //0.5 second
            var geocoder = new google.maps.Geocoder();
            var object_count = 0;
            $.each(source_csv_records,function(id,address) {
                object_count++;
            });
            var count = 1;
            var new_object_count = 0;
            $.each(source_csv_records,function(id,address) {
                if(address !== '') {
                   
                    setTimeout(function() {
                    geocoder.geocode( { 'address': address}, function(results, status) {

                        if (results != null && results.length > 0) {
                            count++;
                            var lat = results[0].geometry.location.lat() ? results[0].geometry.location.lat() : '' ;
                            var lng = results[0].geometry.location.lng() ? results[0].geometry.location.lng() : '' ;
                            var formated_address = results[0].formatted_address ? results[0].formatted_address : '' ;
                            var country_address = results[0].address_components ? results[0].address_components : '' ;
                            var country = [];
                            
                            $.each(country_address,function(num,country_name) {
                                country[num] = country_name.short_name;
                            });

                            var current_record_output = '{"id":"'+id+'","latitude": "'+lat+'", "longitude": "'+lng+'", "formated_address": "'+formated_address+'", "country": "'+country+'"}';
                            var current_record_output_obj = JSON.parse(current_record_output);
                            final_records.push(current_record_output_obj);
                            $(new_locations).val(JSON.stringify(final_records));
                        }else if( status == 'ZERO_RESULTS' || status == 'INVALID_REQUEST'){
                            var current_record_output = '{"id":"'+id+'", "result": "0"}';
                            var current_record_output_obj = JSON.parse(current_record_output);
                            final_records.push(current_record_output_obj);
                            $(new_locations).val(JSON.stringify(final_records));

                        }else{

                            if(status == 'OVER_QUERY_LIMIT') {
                                return false;
                            }else{
                                var current_record_output = '{"id":"'+id+'", "result": "0"}';
                                var current_record_output_obj = JSON.parse(current_record_output);
                                final_records.push(current_record_output_obj);
                                $(new_locations).val(JSON.stringify(final_records));
                            }
                        }
                           
                    });
                    }, delay);
                    new_object_count++;
                    delay += 500;
                }else{

                    var current_record_output = '{"id":"'+id+'", "result": "0"}';
                    var current_record_output_obj = JSON.parse(current_record_output);
                    final_records.push(current_record_output_obj);
                    $(new_locations).val(JSON.stringify(final_records));
                }
                
            });
    
            setTimeout(function() {
                $('.fcdoc-loader').hide();
                $('.fc-geocoding').hide();
                $('.wpgmp-status').html('<div class="fc-msg fc-success">Geocoding '+count+' process is completed. Click below to save new changes.</div>').show();
                $('.fc-geocoding-updates').show();
    
            },delay+2000);
    
        });

        if (jQuery('.form_product_list').length > 0)
            $(".form_product_list").select2({ dropdownCssClass:'wdap_standard_design' });

        if (jQuery('.wdap_select_collections').length > 0)

            $(".wdap_select_collections").select2({
                placeholder: "Select Collections",
                allowClear: true
            });

        $(".check_availability").click(function(event) {
            event.preventDefault();
        });

        var myOptions = {
            defaultColor: false,
            change: function(event, ui) {

                var theColor = ui.color.toString();
                if (this.id == "form_button_color") {
                    $(".wdap_product_availity_form button").css("color", theColor);
                }
                if (this.id == "form_button_bgcolor") {
                    $(".wdap_product_availity_form button").css("background", theColor);
                }

            },
            clear: function() {},
            hide: true,
            palettes: true
        };
        if ($(".scolor").length > 0)
            $('.scolor').wpColorPicker(myOptions);
        $('#pac-input, #wdap_store_address').keypress(function(e) {
            var key = e.which;
            if (key == 13) // the enter key code
                return false;

        });


        $('.my-color-field').hide();

        /*terget form*/

        $('.switch_onoffs').change(function() {
            var target = $(this).data('target');
            if ($(this).attr('type') == 'radio') {
                if (($(this).is(":checked")) && ($(this).val() == "Selected Products") || ($(this).val() == "All Products") || ($(this).val() == "redirect_url") || ($(this).val() == "selected_categories") || ($(this).val() == "all_products_excluding_some")) {

                    if ($(this).val() == 'selected_categories') {
                        $('.wdappage_listing_selected_categories').parent().parent('.fc-form-group.hiderow').show();
                    } else {
                        $('.wdappage_listing_selected_categories').parent().parent('.fc-form-group.hiderow').hide();
                    }

                    if ($(this).val() == 'Selected Products') {
                        $('.wdappage_listing_wdap_select_product').parent().parent('.fc-form-group ').show();
                    } else {
                        $('.wdappage_listing_wdap_select_product').parent().parent('.fc-form-group ').hide();
                    }

                    if ($(this).val() == 'all_products_excluding_some') {
                        $('.wdappage_listing_all_products_excluding_some').parent().parent('.fc-form-group ').show();
                    } else {
                        $('.wdappage_listing_all_products_excluding_some').parent().parent('.fc-form-group ').hide();
                    }


                } else {
                    $(target).closest('.fc-form-group ').hide();
                    if ($(target).hasClass('switch_onoffs')) {
                        $(target).attr('checked', false);
                        $(target).trigger("change");
                    }
                }
            }

            if($(this).attr('type')=='checkbox'){

                 if ($(this).val() == 'category_page' && $(this).is(":checked")  ) {
                        $('.exclude_form_categories_excludecategories').parent().parent('.fc-form-group.hiderow').show();
                    } else {
                        $('.exclude_form_categories_excludecategories').parent().parent('.fc-form-group.hiderow').hide();
                    }

            }

        });
        $.each($('.switch_onoffs'), function(index, element) {
            if (true == $(this).is(":checked")) {
                $(this).trigger("change");
            }
        });

        $(".cancel_import").click(function() {
            var wdap_bid = confirm("Do you want to cancel import process?.");
            if (wdap_bid == true) {
                $(this).closest("form").find("input[name='operation']").val("cancel_import");
                $(this).closest("form").submit();
                return true;
            } else {
                return false;
            }
        });


        $(".wdap_check_backup").click(function() {
            var wdap_bid = confirm("Import woocommerce delivery area pro collection database from import file and delete all existing collections ?");
            if (wdap_bid) {
                var bkid = $(this).data("backup");
                $(this).closest("form").find("input[name='row_id']").val(bkid);
                $(this).closest("form").find("input[name='operation']").val("import_backup");
                $(this).closest("form").submit();
                return true;
            } else {
                return false;
            }
        });

        $(".copy_to_clipboard").click(function () {
            copy_to_clipboard('wdap_referrer')
        });


    });

    $.fn.deliver_form = function(options, forms) {

        return this;
    };

    function copy_to_clipboard(id) {
        var copyText = document.getElementById(id);
        copyText.select();
        navigator.clipboard.writeText(copyText.value);
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = wdap_backend.referrer_copied;
    }


})(jQuery, window, document);
