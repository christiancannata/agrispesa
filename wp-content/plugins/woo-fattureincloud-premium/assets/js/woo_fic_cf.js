jQuery(document).ready(function($){


    // Set the country code (That will display the CF)
    var countryCode = 'IT';
    var a = '#billing_cod_fisc_field';
    var b = '#billing_pec_email_field';
    var c = '#billing_codice_destinatario_field';
    var d = '#billing_partita_iva_field';
    var e = '#billing_fattura_wc_custom_checkout_field';
    var f = '#billing_phone_field';

    jQuery('#billing_first_name_field').show(function(){
                
        jQuery('span[class^="optional"]', this).remove();

        if (jQuery('.required',this).length == 0) { 
            jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
        }
        
        jQuery(this).addClass("validate-required");
        jQuery(this).addClass("woocommerce-validated");
   
    }); 

    jQuery('#billing_last_name_field').show(function(){
                
        jQuery('span[class^="optional"]', this).remove();
        
        if (jQuery('.required',this).length == 0) {
            jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
        }
        
        jQuery(this).addClass("validate-required");
        jQuery(this).addClass("woocommerce-validated");
   
    }); 

    /*
    jQuery('#billing_company_field').hide(function(){
        jQuery('.required',this).remove();
        jQuery(this).removeClass("validate-required");
        jQuery(this).removeClass("woocommerce-validated");
        jQuery("#billing_company").removeAttr('value');
        
    });
    */

    
    if($('#woorichiestafattura').is(':visible')) {
        // Code
   
    jQuery(a).hide();
    jQuery(b).hide();
    jQuery(c).hide();
    jQuery(d).hide();
 
    }



    jQuery('select#billing_country').change(function()
    {
       selectedCountry = $('select#billing_country').val();
         
       if( selectedCountry == countryCode )
        {
            
            jQuery('#billing_cod_fisc_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
            
            });
            
            
            jQuery('#billing_pec_email_field').show(function(){
            });

            jQuery('#billing_partita_iva_field').show(function(){
            });

            jQuery('#billing_codice_destinatario_field').show(function(){
            });

            jQuery('#billing_fattura_wc_custom_checkout_field').show(function() {
            });

           
        }
        else if ( selectedCountry !== countryCode) 
        {
            jQuery('#billing_cod_fisc_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
            });

            jQuery('#billing_pec_email_field').hide(function(){
            });     
    
            jQuery('#billing_codice_destinatario_field').hide(function(){
            });
           
        }

    });


        var a = '#billing_cod_fisc_field';
        var b = '#billing_pec_email_field';
        var c = '#billing_codice_destinatario_field';
        var d = '#billing_partita_iva_field';
        var e = '#billing_first_name_field';
        var f = '#billing_last_name_field';
        var g = '#billing_partita_iva';
        

    jQuery('select#woorichiestafattura').change( function(){
        //if( !jQuery(this).is(':checked') ) {

            selectedinvoice = $('select#woorichiestafattura').val();
         
            if( selectedinvoice == 'woorichiestafattura_ricevuta') {

           

            jQuery(b).hide();
            jQuery(c).hide();
            jQuery(d).hide();
            jQuery('#billing_first_name_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) { 
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
           
            }); 

            jQuery('#billing_last_name_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();

                if (jQuery('.required',this).length == 0) { 
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
           
            });
            
            jQuery('#billing_company_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_company").removeAttr('value');

                
            });

            
            jQuery('#billing_cod_fisc_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_cod_fisc").removeAttr('value');
                
                
            });

            // Hide PIVA field
            jQuery('#billing_partita_iva_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_partita_iva").removeAttr('value');
            });


            
            //jQuery("#billing_cod_fisc").attr("value", "");
                        
                
        } else if( selectedinvoice == 'woorichiestafattura_fattura') {


            // Hide CF field
            jQuery('#billing_cod_fisc_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_cod_fisc").removeAttr('value');
            });
            
   
            jQuery('#billing_partita_iva_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
           
            }); 
            
            jQuery('#billing_company_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");

            }); 

            // Hide First Name field
            jQuery('#billing_first_name_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_first_name").removeAttr('value');
            });
            // Hide Last Name field
            jQuery('#billing_last_name_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_last_name").removeAttr('value');
            });
            
            jQuery(a).hide();
            jQuery(b).hide();
            jQuery(c).hide();
            jQuery(d).show();
            jQuery(g).show();
                        
        } else if( selectedinvoice == 'woorichiestafattura_fatturae') {


            jQuery('#billing_company_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");

            }); 

            // Hide CF field
            jQuery('#billing_cod_fisc_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_cod_fisc").removeAttr('value');
            });


            /*jQuery('#billing_cod_fisc_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }                
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
            */

            // Hide First Name field
            jQuery('#billing_first_name_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_first_name").removeAttr('value');
            });
            // Hide Last Name field
            jQuery('#billing_last_name_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_last_name").removeAttr('value');
            });
           
           // }); 

            jQuery('#billing_partita_iva_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
           
            }); 

            
            jQuery(b).show();
            jQuery(c).show();
            jQuery(d).show();
            jQuery(a).hide();
            jQuery(e).hide();
            jQuery(f).hide();
            jQuery(g).show();
                        
        } else if( selectedinvoice == 'woorichiestafattura_fatturae_privato') {


            jQuery(b).show();
            jQuery(c).hide();
            jQuery(d).hide();
            jQuery('#billing_first_name_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) { 
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
           
            }); 

            jQuery('#billing_last_name_field').show(function(){
                
                jQuery('span[class^="optional"]', this).remove();

                if (jQuery('.required',this).length == 0) { 
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");
           
            });
            
            jQuery('#billing_company_field').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_company").removeAttr('value');
                
            });



            jQuery('#billing_cod_fisc_field').show(function(){
                            
                jQuery('span[class^="optional"]', this).remove();
                if (jQuery('.required',this).length == 0) {
                    jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                }
                jQuery(this).addClass("validate-required");
                jQuery(this).addClass("woocommerce-validated");

            }); 


            // Hide PIVA field
            jQuery('#billing_partita_iva').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_partita_iva").removeAttr('value');
            });
            
            
                        
        } else if( selectedinvoice == 'woorichiestafattura_fattura_privato') {
            jQuery('#billing_cod_fisc_field').show(function(){
                

                jQuery(b).hide();
                jQuery(c).hide();
                jQuery(d).hide();
                jQuery('#billing_first_name_field').show(function(){
                    
                    jQuery('span[class^="optional"]', this).remove();
                    if (jQuery('.required',this).length == 0) { 
                        jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                    }
                    jQuery(this).addClass("validate-required");
                    jQuery(this).addClass("woocommerce-validated");
               
                }); 
    
                jQuery('#billing_last_name_field').show(function(){
                    
                    jQuery('span[class^="optional"]', this).remove();
    
                    if (jQuery('.required',this).length == 0) { 
                        jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                    }
                    jQuery(this).addClass("validate-required");
                    jQuery(this).addClass("woocommerce-validated");
               
                });
                
                jQuery('#billing_company_field').hide(function(){
                    jQuery('.required',this).remove();
                    jQuery(this).removeClass("validate-required");
                    jQuery(this).removeClass("woocommerce-validated");
                    jQuery("#billing_company").removeAttr('value');
                    
                });
    
                jQuery('#billing_cod_fisc_field').show(function(){
                    
                    jQuery('span[class^="optional"]', this).remove();
                    if (jQuery('.required',this).length == 0) {
                        jQuery(this).children('label').append( ' <abbr class="required" title="obbligatorio">*</abbr>' );
                    }                
                    jQuery(this).addClass("validate-required");
                    jQuery(this).addClass("woocommerce-validated");
                    jQuery("#billing_cod_fisc").val('');
                
                });

            // Hide PIVA field
            jQuery('#billing_partita_iva').hide(function(){
                jQuery('.required',this).remove();
                jQuery(this).removeClass("validate-required");
                jQuery(this).removeClass("woocommerce-validated");
                jQuery("#billing_partita_iva").removeAttr('value');
            });




            });
                        
        }








    });

        
});




