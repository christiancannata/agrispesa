<?php


add_action( 'woocommerce_form_field_text','modify_billing_postcode_for_multicap', 10, 2 );
function modify_billing_postcode_for_multicap( $field, $key ){
	
	$cities = array('Bologna','Bari','Catania','Milano','Roma','Messina','Napoli','Palermo','Firenze','Pisa','Verbania','Trento','Alessandria','Ancona','Bergamo','Brescia','Cagliari','Ferrara','Foggia','Cesena','Forli','Genova','La Spezia','Livorno','Modena','Padova','Parma','Perugia','Pesaro','Pescara','Piacenza','Ravenna','Reggio Calabria','Reggio Emilia','Rimini','Salerno','Taranto','Trieste','Venezia','Verona','Torino');
	
    if ( is_checkout() && ( $key == 'billing_postcode') && in_array( WC()->customer->get_billing_city(), $cities ) ) {
    ?>
    <script type="text/javascript">
		function modifyBcap() {
			(function($){
            $('#modify-billing-cap').hide();
			$('#billing_postcode_field').hide();
			$('#billing_postcode2_field').show();
			})(jQuery);
         }
		
	</script>
    <?php
		
        $field .= '<input type="button" class="form-row form-row-wide" id="modify-billing-cap" onclick="modifyBcap()" value="Modifica C.A.P" />';
    }

    return $field;
}



add_action( 'woocommerce_form_field_text','modify_shipping_postcode_for_multicap', 10, 2 );
function modify_shipping_postcode_for_multicap( $field, $key ){
	
	$cities = array('Bologna','Bari','Catania','Milano','Roma','Messina','Napoli','Palermo','Firenze','Pisa','Verbania','Trento','Alessandria','Ancona','Bergamo','Brescia','Cagliari','Ferrara','Foggia','Cesena','Forli','Genova','La Spezia','Livorno','Modena','Padova','Parma','Perugia','Pesaro','Pescara','Piacenza','Ravenna','Reggio Calabria','Reggio Emilia','Rimini','Salerno','Taranto','Trieste','Venezia','Verona','Torino');
	
    if ( is_checkout() && ( $key == 'shipping_postcode') && in_array( WC()->customer->get_shipping_city(), $cities ) ) {
    ?>
    <script type="text/javascript">
		function modifyScap() {
			(function($){
            $('#modify-shipping-cap').hide();
			$('#shipping_postcode_field').hide();
			$('#shipping_postcode2_field').show();
			})(jQuery);
         }
	</script>
    <?php
		
        $field .= '<input type="button" class="form-row form-row-wide" id="modify-shipping-cap" onclick="modifyScap()" value="Modifica C.A.P" />';
    }

    return $field;
}



add_filter('woocommerce_default_address_fields','wc_change_postcodetwo_order');
function wc_change_postcodetwo_order($fields) {
	
	
	if( is_checkout() && ! is_wc_endpoint_url() ){
	        $fields['postcode2']['priority'] = 70;
        }
            return $fields;
        }



add_filter( 'woocommerce_checkout_fields', 'add_field_dropdown_postcode_checkout' );
function add_field_dropdown_postcode_checkout( $fields ) {
	
	 if( is_checkout() && ! is_wc_endpoint_url() ){

    $text_domain   = 'woocommerce';
    $option_cities = array();
    $italian_postcode  = array( '' => __('Seleziona il tuo cap', $text_domain) );


    foreach( italy_multi_postcode_settings() as $city => $postal_codes ) {
        $option_cities[$city] = $city;
		
            foreach( $postal_codes as $postal_code ) {
                $italian_postcode[$postal_code] = $postal_code;
            }
		
    }

    // 1.Opzioni Campi postcode

    $fields['billing']['billing_city']['options']     = $option_cities;
	$fields['shipping']['shipping_city']['options']     = $option_cities;
    $fields['billing']['billing_postcode2']['label'] = $fields['billing']['billing_postcode']['label'];
    $fields['shipping']['shipping_postcode2']['label'] = $fields['shipping']['shipping_postcode']['label'];

    // 2.Creazione campi Billing e Shipping Postcode
    $fields['billing']['billing_postcode2'] = array(
        'type'        => 'select',
        'label'       => __('Seleziona il C.A.P', $text_domain),
        'input_class' => array('city_to_select'),
        'options'     => $italian_postcode,
        'required'    => false,
        'default'     => '',
    );
	
	$fields['shipping']['shipping_postcode2'] = array(
        'type'        => 'select',
        'label'       => __('Seleziona il C.A.P', $text_domain),
        'input_class' => array('city_to_select'),
        'options'     => $italian_postcode,
        'required'    => false,
        'default'     => '',
    );

    return $fields;
}
}


add_action( 'wp_footer', 'custom_postcode_checkout_js_script' );
function custom_postcode_checkout_js_script() {

    if( is_checkout() && ! is_wc_endpoint_url() ):
	
	if( get_option( 'wcicapfwpro_readonlycap_field' ) == "yes" && get_option( 'wcicapfw_disable_zipcode_field' ) == "no") {
		
		
	 ?>
    <script type="text/javascript">
		
		
		
    (function($){
		var targetedCountry = 'IT',
            initialBCountry = '<?php echo WC()->customer->get_billing_country(); ?>',
            initialSCountry = '<?php echo WC()->customer->get_shipping_country(); ?>';

        function showHideFields( country, fieldset ) {
        if( country !== targetedCountry) {
			    $('#'+fieldset+'_postcode').prop('readOnly', false);
            }
		else {
                
			    $('#'+fieldset+'_postcode').prop('readOnly', true);				
			
            }
        }

 
        showHideFields(initialBCountry, 'billing');
        showHideFields(initialSCountry, 'shipping');
	
		
		$('body').on( 'change', 'select#billing_country', function(){
            showHideFields($(this).val(), 'billing');
			
        });
        $('body').on( 'change', 'select#shipping_country', function(){
            showHideFields($(this).val(), 'shipping');
        });
    })(jQuery);
    </script>
    <?php
		
		
		}
	
	
    ?>
    <script type="text/javascript">
		
		
		
    (function($){
		var targetedCountry = 'IT',
            initialBCountry = '<?php echo WC()->customer->get_billing_country(); ?>',
            initialSCountry = '<?php echo WC()->customer->get_shipping_country(); ?>';

        function showHideFields( country, fieldset ) {
        if( country !== targetedCountry) {
                $('#'+fieldset+'_postcode2_field').hide();
                $('#'+fieldset+'_postcode_field').show();
            }
		else {
                
			
			    $('body').on( 'change', 'select#billing_state', function(){
                $('#billing_postcode2_field').hide();
				$('#billing_postcode_field').show();
			
                });
               $('body').on( 'change', 'select#shipping_state', function(){
               $('#shipping_postcode2_field').hide();
			   $('#shipping_postcode_field').show();
                });
			
			
            }
        }

 
        showHideFields(initialBCountry, 'billing');
        showHideFields(initialSCountry, 'shipping');
	
		
		$('body').on( 'change', 'select#billing_country', function(){
            showHideFields($(this).val(), 'billing');
			
        });
        $('body').on( 'change', 'select#shipping_country', function(){
            showHideFields($(this).val(), 'shipping');
        });
    
    
        $('body').on( 'change', 'select#billing_postcode2', function(){
             $('input#billing_postcode').val($(this).val());
        });
		
        $('body').on( 'change', 'select#shipping_postcode2', function(){
             $('input#shipping_postcode').val($(this).val());
        });
    })(jQuery);
    </script>
    <?php
    endif;
}



add_action('wp_footer', 'empty_field_postcode_for_italy_multicap', 50);
function empty_field_postcode_for_italy_multicap() {
	
	if ( is_cart() ) :
    ?>
    <script type="text/javascript">
    jQuery( function($){
        var targetedCountry = 'IT',
        initialSCountry = '<?php echo WC()->customer->get_shipping_country(); ?>';
		
        $(document.body).on( 'change', 'select.city_select', function() {
	    var $container = $(this).closest('.form-row').parent(),
			$city = $container.find('#calc_shipping_city'),
            postcode = $city.find(':selected').data('postcode');

        if ( postcode == undefined && targetedCountry == initialSCountry ) {
            $container.find('#calc_shipping_postcode').val('');
        } 
        });
    });
    </script>
    <?php
    endif;
}




add_action('wp_footer', 'multi_postcode_js_script');
function multi_postcode_js_script() {
    if( is_checkout() && ! is_wc_endpoint_url() ) :

    $text_domain   = 'woocommerce';
	
	$select_default =  __('Select', 'woocommerce');

    // Prepara le opzioni per il dropdown
	
	foreach( italy_multi_postcode_settings()['Bologna'] as $Bologna ) {
        $Bologna_postcodes[$Bologna] = $Bologna;
    }
    foreach( italy_multi_postcode_settings()['Bari'] as $Bari ) {
        $Bari_postcodes[$Bari] = $Bari;
    }
	foreach( italy_multi_postcode_settings()['Catania'] as $Catania ) {
        $Catania_postcodes[$Catania] = $Catania;
    }
	
	foreach( italy_multi_postcode_settings()['Milano'] as $Milano ) {
        $Milano_postcodes[$Milano] = $Milano;
    }
	foreach( italy_multi_postcode_settings()['Roma'] as $Roma ) {
        $Roma_postcodes[$Roma] = $Roma;
    }	
	foreach( italy_multi_postcode_settings()['Messina'] as $Messina ) {
        $Messina_postcodes[$Messina] = $Messina;
    }
	foreach( italy_multi_postcode_settings()['Napoli'] as $Napoli ) {
        $Napoli_postcodes[$Napoli] = $Napoli;
    }
	foreach( italy_multi_postcode_settings()['Palermo'] as $Palermo ) {
        $Palermo_postcodes[$Palermo] = $Palermo;
    }
	foreach( italy_multi_postcode_settings()['Firenze'] as $Firenze ) {
        $Firenze_postcodes[$Firenze] = $Firenze;
    }
	foreach( italy_multi_postcode_settings()['Pisa'] as $Pisa ) {
        $Pisa_postcodes[$Pisa] = $Pisa;
    }
	foreach( italy_multi_postcode_settings()['Verbania'] as $Verbania ) {
        $Verbania_postcodes[$Verbania] = $Verbania;
    }
	foreach( italy_multi_postcode_settings()['Trento'] as $Trento ) {
        $Trento_postcodes[$Trento] = $Trento;
    }
	foreach( italy_multi_postcode_settings()['Alessandria'] as $Alessandria ) {
        $Alessandria_postcodes[$Alessandria] = $Alessandria;
    }
	foreach( italy_multi_postcode_settings()['Ancona'] as $Ancona ) {
        $Ancona_postcodes[$Ancona] = $Ancona;
    }
	foreach( italy_multi_postcode_settings()['Bergamo'] as $Bergamo ) {
        $Bergamo_postcodes[$Bergamo] = $Bergamo;
    }
	foreach( italy_multi_postcode_settings()['Brescia'] as $Brescia ) {
        $Brescia_postcodes[$Brescia] = $Brescia;
    }
	foreach( italy_multi_postcode_settings()['Cagliari'] as $Cagliari ) {
        $Cagliari_postcodes[$Cagliari] = $Cagliari;
    }
	foreach( italy_multi_postcode_settings()['Ferrara'] as $Ferrara ) {
        $Ferrara_postcodes[$Ferrara] = $Ferrara;
    }
	foreach( italy_multi_postcode_settings()['Foggia'] as $Foggia ) {
        $Foggia_postcodes[$Foggia] = $Foggia;
    }
	foreach( italy_multi_postcode_settings()['Cesena'] as $Cesena ) {
        $Cesena_postcodes[$Cesena] = $Cesena;
    }
	foreach( italy_multi_postcode_settings()['Forli'] as $Forli ) {
        $Forli_postcodes[$Forli] = $Forli;
    }
	foreach( italy_multi_postcode_settings()['Genova'] as $Genova ) {
        $Genova_postcodes[$Genova] = $Genova;
    }
	foreach( italy_multi_postcode_settings()['La Spezia'] as $La_Spezia ) {
        $La_Spezia_postcodes[$La_Spezia] = $La_Spezia;
    }
	foreach( italy_multi_postcode_settings()['Livorno'] as $Livorno ) {
        $Livorno_postcodes[$Livorno] = $Livorno;
    }
	foreach( italy_multi_postcode_settings()['Modena'] as $Modena ) {
        $Modena_postcodes[$Modena] = $Modena;
    }
	foreach( italy_multi_postcode_settings()['Padova'] as $Padova ) {
        $Padova_postcodes[$Padova] = $Padova;
    }
	foreach( italy_multi_postcode_settings()['Parma'] as $Parma ) {
        $Parma_postcodes[$Parma] = $Parma;
    }
	foreach( italy_multi_postcode_settings()['Perugia'] as $Perugia ) {
        $Perugia_postcodes[$Perugia] = $Perugia;
    }
	foreach( italy_multi_postcode_settings()['Pesaro'] as $Pesaro ) {
        $Pesaro_postcodes[$Pesaro] = $Pesaro;
    }
	foreach( italy_multi_postcode_settings()['Pescara'] as $Pescara ) {
        $Pescara_postcodes[$Pescara] = $Pescara;
    }
	foreach( italy_multi_postcode_settings()['Piacenza'] as $Piacenza ) {
        $Piacenza_postcodes[$Piacenza] = $Piacenza;
    }
	foreach( italy_multi_postcode_settings()['Ravenna'] as $Ravenna ) {
        $Ravenna_postcodes[$Ravenna] = $Ravenna;
    }
	foreach( italy_multi_postcode_settings()['Reggio Calabria'] as $Reggio_Calabria ) {
        $Reggio_Calabria_postcodes[$Reggio_Calabria] = $Reggio_Calabria;
    }
	foreach( italy_multi_postcode_settings()['Reggio Emilia'] as $Reggio_Emilia ) {
        $Reggio_Emilia_postcodes[$Reggio_Emilia] = $Reggio_Emilia;
    }
	foreach( italy_multi_postcode_settings()['Rimini'] as $Rimini ) {
        $Rimini_postcodes[$Rimini] = $Rimini;
    }
	foreach( italy_multi_postcode_settings()['Salerno'] as $Salerno ) {
        $Salerno_postcodes[$Salerno] = $Salerno;
    }
	foreach( italy_multi_postcode_settings()['Taranto'] as $Taranto ) {
        $Taranto_postcodes[$Taranto] = $Taranto;
    }
	foreach( italy_multi_postcode_settings()['Trieste'] as $Trieste ) {
        $Trieste_postcodes[$Trieste] = $Trieste;
    }
	foreach( italy_multi_postcode_settings()['Venezia'] as $Venezia ) {
        $Venezia_postcodes[$Venezia] = $Venezia;
    }
	foreach( italy_multi_postcode_settings()['Verona'] as $Verona ) {
        $Verona_postcodes[$Verona] = $Verona;
    }
	foreach( italy_multi_postcode_settings()['Torino'] as $Torino ) {
        $Torino_postcodes[$Torino] = $Torino;
    }
	
	
	//array('Bologna','Bari','Catania','Milano','Roma','Messina','Napoli','Palermo','Firenze','Pisa','Verbania','Trento','Alessandria','Ancona','Bergamo','Brescia','Cagliari','Ferrara','Foggia','Cesena','Forli','Genova','La Spezia','Livorno','Modena','Padova','Parma','Perugia','Pesaro','Pescara','Piacenza','Ravenna','Reggio Calabria','Reggio Emilia','Rimini','Salerno','Taranto','Trieste','Venezia','Verona','Torino');
    ?>
    <script language="javascript">
    jQuery( function($){
        var a = 'select[name="billing_city"]',
            b = 'select[name="billing_postcode2"]',
			c = 'select[name="shipping_city"]',
			d = 'select[name="shipping_postcode2"]',
			e = 'select[name="billing_country"]',
			f = 'select[name="billing_state"]',
			g = 'select[name="shipping_country"]',
			h = 'select[name="shipping_state"]',
			
			select_default = <?php echo json_encode ($select_default); ?>,
			Bologna = <?php echo json_encode ($Bologna_postcodes); ?>,
			Bari = <?php echo json_encode ($Bari_postcodes); ?>,
			Catania = <?php echo json_encode ($Catania_postcodes); ?>,
            Milano = <?php echo json_encode ($Milano_postcodes); ?>,
			Roma = <?php echo json_encode ($Roma_postcodes); ?>,
			Messina = <?php echo json_encode ($Messina_postcodes); ?>,
			Napoli = <?php echo json_encode ($Napoli_postcodes); ?>,
			Palermo = <?php echo json_encode ($Palermo_postcodes); ?>,
			Firenze = <?php echo json_encode ($Firenze_postcodes); ?>,
			Pisa = <?php echo json_encode ($Pisa_postcodes); ?>,
			Verbania = <?php echo json_encode ($Verbania_postcodes); ?>,
			Trento = <?php echo json_encode ($Trento_postcodes); ?>,
			Alessandria = <?php echo json_encode ($Alessandria_postcodes); ?>,
			Ancona = <?php echo json_encode ($Ancona_postcodes); ?>,
			Bergamo = <?php echo json_encode ($Bergamo_postcodes); ?>,
			Brescia = <?php echo json_encode ($Brescia_postcodes); ?>,
			Cagliari = <?php echo json_encode ($Cagliari_postcodes); ?>,
			Ferrara = <?php echo json_encode ($Ferrara_postcodes); ?>,
			Foggia = <?php echo json_encode ($Foggia_postcodes); ?>,
			Cesena = <?php echo json_encode ($Cesena_postcodes); ?>,
			Forli = <?php echo json_encode ($Forli_postcodes); ?>,
			Genova = <?php echo json_encode ($Genova_postcodes); ?>,
			La_Spezia = <?php echo json_encode ($La_Spezia_postcodes); ?>,
			Livorno = <?php echo json_encode ($Livorno_postcodes); ?>,
			Modena = <?php echo json_encode ($Modena_postcodes); ?>,
			Padova = <?php echo json_encode ($Padova_postcodes); ?>,
			Parma = <?php echo json_encode ($Parma_postcodes); ?>,
			Perugia = <?php echo json_encode ($Perugia_postcodes); ?>,
			Pesaro = <?php echo json_encode ($Pesaro_postcodes); ?>,
			Pescara = <?php echo json_encode ($Pescara_postcodes); ?>,
			Piacenza = <?php echo json_encode ($Piacenza_postcodes); ?>,
			Ravenna = <?php echo json_encode ($Ravenna_postcodes); ?>,
			Reggio_Calabria = <?php echo json_encode ($Reggio_Calabria_postcodes); ?>,
			Reggio_Emilia = <?php echo json_encode ($Reggio_Emilia_postcodes); ?>,
			Rimini = <?php echo json_encode ($Rimini_postcodes); ?>,
			Salerno = <?php echo json_encode ($Salerno_postcodes); ?>,
			Taranto = <?php echo json_encode ($Taranto_postcodes); ?>,
			Trieste = <?php echo json_encode ($Trieste_postcodes); ?>,
			Venezia = <?php echo json_encode ($Venezia_postcodes); ?>,
			Verona = <?php echo json_encode ($Verona_postcodes); ?>,
			Torino = <?php echo json_encode ($Torino_postcodes); ?>,
            s = $(b).html();

        //Funzione di utilità per riempire dinamicamente le opzioni del campo selezionato (billing)
        function dynamicSelectOptions( opt ){
            var options = '';
            $.each( opt, function( key, value ){
                options += '<option value="'+key+'">'+value+'</option>';
            });
            $(b).html(options);
        }
		
		//Funzione di utilità per riempire dinamicamente le opzioni del campo selezionato (shipping)
        function dynamicSelectOptionsS( opt ){
            var options = '';
            $.each( opt, function( key, value ){
                options += '<option value="'+key+'">'+value+'</option>';
            });
            $(d).html(options);
        }
		
		//Mostra per i multicap (Inserito in NO DOM - Billing)
		function multicapBnoDOM() {
			(function($){
                $('#modify-billing-cap').hide();
				$('#billing_postcode_field').hide();
				$('#billing_postcode2_field').show();
			})(jQuery);
         }
		
		//Mostra per i multicap (Inserito in NO DOM - Shipping)
		function multicapSnoDOM() {
			(function($){
                $('#modify-shipping-cap').hide();
				$('#shipping_postcode_field').hide();
				$('#shipping_postcode2_field').show();
			})(jQuery);
         }
		
		//Di Default in DOM
        (function($){
        $('#billing_postcode_field').show();
		$('#billing_postcode2_field').hide();
		$('#shipping_postcode_field').show();
		$('#shipping_postcode2_field').hide();
        })(jQuery);
		
		//Quando il documento è pronto ed ha terminato di triggare i campi, inserisci un valore vuoto al cambio nel dropdwon della città, tranne a quelli automatici
		//Billing
		$(document).ready(function(){
            $("select#billing_city").change(function(){
			  $('#modify-billing-cap').hide();
			  $('#billing_postcode_field').show();
		      $('#billing_postcode2_field').hide();
              $('#billing_postcode').val('');
                    });
                });
		//Quando il documento è pronto ed ha terminato di triggare i campi, inserisci un valore vuoto al cambio nel dropdwon della città, tranne a quelli automatici
		//Shipping
		$(document).ready(function(){
            $("select#shipping_city").change(function(){
			  $('#modify-shipping-cap').hide();
			  $('#shipping_postcode_field').show();
		      $('#shipping_postcode2_field').hide();
              $('#shipping_postcode').val('');
                    });
                });
		
		//Al cambio del campo nazione nel form billing (e)
		$(document).ready(function(){
		$('form.woocommerce-checkout').on('change', e, function() {
            console.log($(this).val());
			$('#modify-billing-cap').hide();

		});
			});
		// Al cambio del campo provincia nel form billing (f)
		$(document).ready(function(){
		$('form.woocommerce-checkout').on('change', f, function() {
            console.log($(this).val());
			$('#modify-billing-cap').hide();
		});
			});
		
		// Al cambio del campo nazione nel form shipping (g)
		$(document).ready(function(){
		$('form.woocommerce-checkout').on('change', g, function() {
            console.log($(this).val());
			$('#modify-shipping-cap').hide();
		});
			});
		// Al cambio del campo provincia nel form shipping (h)
		$(document).ready(function(){
		$('form.woocommerce-checkout').on('change', h, function() {
            console.log($(this).val());
			$('#modify-shipping-cap').hide();
		});
			});
		
		
		//array('Bologna','Bari','Catania','Milano','Roma','Messina','Napoli','Palermo','Firenze','Pisa','Verbania','Trento','Alessandria','Ancona','Bergamo','Brescia','Cagliari','Ferrara','Foggia','Cesena','Forli','Genova','La Spezia','Livorno','Modena','Padova','Parma','Perugia','Pesaro','Pescara','Piacenza','Ravenna','Reggio Calabria','Reggio Emilia','Rimini','Salerno','Taranto','Trieste','Venezia','Verona','Torino');
		////////////////////////                 INIZIO  billing              //////////////////////////////////////////
        //In DOM (Billing)
		$('form.woocommerce-checkout').on('change', a, function() {
            console.log($(this).val());
		//Quando il DOM viene caricato e troviamo il valore di una città rientrante nei multicap (Billing)
		if ( $(a).val() === 'Bologna' ) {
            dynamicSelectOptions( Bologna );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
        if ( $(a).val() === 'Bari' ) {
            dynamicSelectOptions( Bari );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }	
		if ( $(a).val() === 'Catania' ) {
            dynamicSelectOptions( Catania );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Milano' ) {
            dynamicSelectOptions( Milano );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Roma' ) {
            dynamicSelectOptions( Roma );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Messina' ) {
            dynamicSelectOptions( Messina );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Napoli' ) {
            dynamicSelectOptions( Napoli );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Palermo' ) {
            dynamicSelectOptions( Palermo );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Firenze' ) {
            dynamicSelectOptions( Firenze );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Pisa' ) {
            dynamicSelectOptions( Pisa );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Verbania' ) {
            dynamicSelectOptions( Verbania );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Trento' ) {
            dynamicSelectOptions( Trento );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Alessandria' ) {
            dynamicSelectOptions( Alessandria );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Ancona' ) {
            dynamicSelectOptions( Ancona );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Bergamo' ) {
            dynamicSelectOptions( Bergamo );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Brescia' ) {
            dynamicSelectOptions( Brescia );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Cagliari' ) {
            dynamicSelectOptions( Cagliari );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Ferrara' ) {
            dynamicSelectOptions( Ferrara );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Foggia' ) {
            dynamicSelectOptions( Foggia );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Cesena' ) {
            dynamicSelectOptions( Cesena );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Forli' ) {
            dynamicSelectOptions( Forli );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Genova' ) {
            dynamicSelectOptions( Genova );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'La Spezia' ) {
            dynamicSelectOptions( La_Spezia );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Livorno' ) {
            dynamicSelectOptions( Livorno );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Modena' ) {
            dynamicSelectOptions( Modena );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Padova' ) {
            dynamicSelectOptions( Padova );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Parma' ) {
            dynamicSelectOptions( Parma );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Perugia' ) {
            dynamicSelectOptions( Perugia );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Pesaro' ) {
            dynamicSelectOptions( Pesaro );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Pescara' ) {
            dynamicSelectOptions( Pescara );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Piacenza' ) {
            dynamicSelectOptions( Piacenza );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Ravenna' ) {
            dynamicSelectOptions( Ravenna );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Reggio Calabria' ) {
            dynamicSelectOptions( Reggio_Calabria );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Reggio Emilia' ) {
            dynamicSelectOptions( Reggio_Emilia );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Rimini' ) {
            dynamicSelectOptions( Rimini );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Salerno' ) {
            dynamicSelectOptions( Salerno );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Taranto' ) {
            dynamicSelectOptions( Taranto );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Trieste' ) {
            dynamicSelectOptions( Trieste );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Venezia' ) {
            dynamicSelectOptions( Venezia );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Verona' ) {
            dynamicSelectOptions( Verona );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(a).val() === 'Torino' ) {
            dynamicSelectOptions( Torino );
			$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		else {
			$('#billing_postcode2_field').hide();
			$('#billing_postcode_field').show();
				}	
        console.log($(a).val());
			  });
		
		//array('Bologna','Bari','Catania','Milano','Roma','Messina','Napoli','Palermo','Firenze','Pisa','Verbania','Trento','Alessandria','Ancona','Bergamo','Brescia','Cagliari','Ferrara','Foggia','Cesena','Forli','Genova','La Spezia','Livorno','Modena','Padova','Parma','Perugia','Pesaro','Pescara','Piacenza','Ravenna','Reggio Calabria','Reggio Emilia','Rimini','Salerno','Taranto','Trieste','Venezia','Verona','Torino');
		//NO DOM Billing
	    $(document).ready(function(){
		$('form.woocommerce-checkout').on('change', a, function() {	
			if ( $(this).val() === 'Bologna' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Bologna );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
            if ( $(this).val() === 'Bari' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Bari );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Catania' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Catania );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Milano' ) {
				multicapBnoDOM();
                dynamicSelectOptions( Milano );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
            }
			if ( $(this).val() === 'Roma' ) {
				multicapBnoDOM();
                dynamicSelectOptions( Roma );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
            }
			if ( $(this).val() === 'Messina' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Messina );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Napoli' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Napoli );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Palermo' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Palermo );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Firenze' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Firenze );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Pisa' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Pisa );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Verbania' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Verbania );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Trento' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Trento );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Alessandria' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Alessandria );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Ancona' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Ancona );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Bergamo' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Bergamo );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Brescia' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Brescia );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Cagliari' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Cagliari );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Ferrara' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Ferrara );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Foggia' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Foggia );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Cesena' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Cesena );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Forli' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Forli );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Genova' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Genova );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'La Spezia' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( La_Spezia );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Livorno' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Livorno );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Modena' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Modena );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Padova' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Padova );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Parma' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Parma );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Perugia' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Perugia );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Pesaro' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Pesaro );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Pescara' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Pescara );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Piacenza' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Piacenza );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Ravenna' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Ravenna );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Reggio Calabria' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Reggio_Calabria );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Reggio Emilia' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Reggio_Emilia );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Rimini' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Rimini );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Salerno' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Salerno );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Taranto' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Taranto );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Trieste' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Trieste );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Venezia' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Venezia );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Verona' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Verona );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Torino' ) {
				//ok
				multicapBnoDOM();
                dynamicSelectOptions( Torino );
				$('#billing_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			
			
			
        });
    });
		////////////////////////                 FINE  billing             //////////////////////////////////////////
		
		
		       ////////////////////////                 INIZIO shipping              //////////////////////////////////////////
        //In DOM (Shipping)
		$('form.woocommerce-checkout').on('change', c, function() {
            console.log($(this).val());
		//Quando il DOM viene caricato e troviamo il valore di una città rientrante nei multicap (Shipping)
		if ( $(c).val() === 'Bologna' ) {
            dynamicSelectOptionsS( Bologna );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
        if ( $(c).val() === 'Bari' ) {
            dynamicSelectOptionsS( Bari );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Catania' ) {
            dynamicSelectOptionsS( Catania );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Milano' ) {
            dynamicSelectOptionsS( Milano );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Roma' ) {
            dynamicSelectOptionsS( Roma );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Messina' ) {
            dynamicSelectOptionsS( Messina );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Napoli' ) {
            dynamicSelectOptionsS( Napoli );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Palermo' ) {
            dynamicSelectOptionsS( Palermo );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Firenze' ) {
            dynamicSelectOptionsS( Firenze );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Pisa' ) {
            dynamicSelectOptionsS( Pisa );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Verbania' ) {
            dynamicSelectOptionsS( Verbania );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Trento' ) {
            dynamicSelectOptionsS( Trento );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Alessandria' ) {
            dynamicSelectOptionsS( Alessandria );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Ancona' ) {
            dynamicSelectOptionsS( Ancona );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Bergamo' ) {
            dynamicSelectOptionsS( Bergamo );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Brescia' ) {
            dynamicSelectOptionsS( Brescia );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Cagliari' ) {
            dynamicSelectOptionsS( Cagliari );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Ferrara' ) {
            dynamicSelectOptionsS( Ferrara );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Foggia' ) {
            dynamicSelectOptionsS( Foggia );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Cesena' ) {
            dynamicSelectOptionsS( Cesena );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Forli' ) {
            dynamicSelectOptionsS( Forli );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Genova' ) {
            dynamicSelectOptionsS( Genova );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'La Spezia' ) {
            dynamicSelectOptionsS( La_Spezia );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Livorno' ) {
            dynamicSelectOptionsS( Livorno );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Modena' ) {
            dynamicSelectOptionsS( Modena );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Padova' ) {
            dynamicSelectOptionsS( Padova );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Parma' ) {
            dynamicSelectOptionsS( Parma );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Perugia' ) {
            dynamicSelectOptionsS( Perugia );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Pesaro' ) {
            dynamicSelectOptionsS( Pesaro );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Pescara' ) {
            dynamicSelectOptionsS( Pescara );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Piacenza' ) {
            dynamicSelectOptionsS( Piacenza );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Ravenna' ) {
            dynamicSelectOptionsS( Ravenna );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Reggio Calabria' ) {
            dynamicSelectOptionsS( Reggio_Calabria );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Reggio Emilia' ) {
            dynamicSelectOptionsS( Reggio_Emilia );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Rimini' ) {
            dynamicSelectOptionsS( Rimini );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Salerno' ) {
            dynamicSelectOptionsS( Salerno );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Taranto' ) {
            dynamicSelectOptionsS( Taranto );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Trieste' ) {
            dynamicSelectOptionsS( Trieste );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Venezia' ) {
            dynamicSelectOptionsS( Venezia );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Verona' ) {
            dynamicSelectOptionsS( Verona );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
		if ( $(c).val() === 'Torino' ) {
            dynamicSelectOptionsS( Torino );
			$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
        }
			else {
			$('#shipping_postcode2_field').hide();
			$('#shipping_postcode_field').show();
				}
			
        console.log($(c).val());
			  });
		
	
		//NO DOM Shipping
	    $(document).ready(function(){
		$('form.woocommerce-checkout').on('change', c, function() {	
			if ( $(this).val() === 'Bologna' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Bologna );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
            if ( $(this).val() === 'Bari' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Bari );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Catania' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Catania );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Milano' ) {
				multicapSnoDOM();
                dynamicSelectOptionsS( Milano );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
            }
			if ( $(this).val() === 'Roma' ) {
				multicapSnoDOM();
                dynamicSelectOptionsS( Roma );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");
            }
			if ( $(this).val() === 'Messina' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Messina );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Napoli' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Napoli );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Palermo' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Palermo );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Firenze' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Firenze );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Pisa' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Pisa );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Verbania' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Verbania );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Trento' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Trento );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Alessandria' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Alessandria );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Ancona' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Ancona );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Bergamo' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Bergamo );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Brescia' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Brescia );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Cagliari' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Cagliari );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Ferrara' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Ferrara );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Foggia' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Foggia );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Cesena' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Cesena );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Forli' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Forli );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Genova' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Genova );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'La Spezia' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( La_Spezia );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Livorno' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Livorno );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Modena' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Modena );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Padova' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Padova );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Parma' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Parma );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Perugia' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Perugia );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Pesaro' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Pesaro );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Pescara' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Pescara );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Piacenza' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Piacenza );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Ravenna' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Ravenna );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Reggio Calabria' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Reggio_Calabria );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Reggio Emilia' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Reggio_Emilia );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Rimini' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Rimini );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Salerno' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Salerno );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Taranto' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Taranto );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Trieste' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Trieste );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Venezia' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Venezia );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Verona' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Verona );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			if ( $(this).val() === 'Torino' ) {
				//ok
				multicapSnoDOM();
                dynamicSelectOptionsS( Torino );
				$('#shipping_postcode2 option[value="'+select_default+'"]').attr("selected", "selected");

            }
			
			
			
        });
    });
		////////////////////////                 FINE shipping             //////////////////////////////////////////
		
		
		
		});
    </script>
    <?php
    endif;
}

