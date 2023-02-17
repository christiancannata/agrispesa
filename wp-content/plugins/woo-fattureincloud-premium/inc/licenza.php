<?php
// Don't access this directly, please
if (!defined('ABSPATH')) {
    exit;
}

###############################################################################################################################################################

?>
<form method="post">

<?php wp_nonce_field(); ?>

<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
						<?php _e(' Impostazioni della License Key ', 'woo-fattureincloud-premium'); ?>
						</th>
	<tr>
		<td>

		<label for="woo_fattureincloud_license_key"> Key</label>
		<input type="password" name="fattureincloud_license_key" value="<?php echo get_option('fattureincloud_license_key'); ?>">

				   
		<label for="woo_fattureincloud_license_email">email</label>
		<input type="text" name="fattureincloud_license_email" value="<?php echo get_option('fattureincloud_license_email'); ?>">

        <input type="submit" value="Salva" name="wfic_license_checkandact" class="button button-primary button-large" 
		onclick="window.location='admin.php?page=woo-fattureincloud-premium&tab=licenza#setting-error-settings_updated';">

		</td>
		
	</tr>
	<tr>
		<td>




		</td>
	
	</tr>

</table>
</form>

<?php 

$date = new DateTime();
$instance_date = $date->getTimestamp();


$base_url_addr = 'https://woofatture.com/';
$email         = trim( get_option( 'fattureincloud_license_email' ));
$format        = '';
$product_id    = 'woofatture';
$license_key   = trim( get_option( 'fattureincloud_license_key' ));
$instance  	   = $instance_date;
$secret_key    = 'rBub7SaWZRO3tjPc7$e0EjIf';
$activation_id = '';
$order_id	   = '';

################## check ######################

$wfic_api_params = array(
		
	'wc-api'	  => 'software-api',
	'request'     => 'check',
	'email'		  => $email,
	'license_key' => $license_key,
	'product_id'  => $product_id
);
	
##################################################

execute_request($wfic_api_params );


// Create an url based on
function create_url( $wfic_api_params ) {

    $base_url_addr = 'https://woofatture.com/';
	$base_url_addr = add_query_arg( 'wc-api', 'software-api', $base_url_addr   );
    return $base_url_addr . '&' . http_build_query( $wfic_api_params);

}

function execute_request($wfic_api_params ) {

    global $license_data;

    $target_url = create_url( $wfic_api_params );
    $wfic_api_data = wp_remote_get( $target_url );

    if (is_array($wfic_api_data) && !empty($wfic_api_data['body'])) {
    
        $license_data = json_decode($wfic_api_data['body'], true);

        
    
    }

}


if( isset( $_POST['wfic_license_activate'] ) ) { 

    $wfic_api_params = array(
		'request'     => 'activation',
		'email'       => $email,
		'license_key' => $license_key,
		'product_id'  => $product_id,
		'secret_key'  => $secret_key,
        'instance' 	  => $instance
    );
}

execute_request($wfic_api_params );

if( isset( $_POST['wfic_license_deactivate'] ) ) { 

    $wfic_api_params = array(
		'request'     => 'deactivation',
		'email'       => $email,
		'license_key' => $license_key,
		'product_id'  => $product_id,
		'secret_key'  => $secret_key,
        'instance' 	  => trim( get_option( 'wfic_license_instance' ))
    );
}

execute_request($wfic_api_params );



################################################################################

global $license_data;

// 01

/*
print "<pre>";
print_r($license_data);
echo "</pre>";

*/

$wfic_activated_license = trim( get_option( 'wfic_license_activated' ));

//echo "<br>".trim( get_option( 'wfic_license_status' ));
//echo "<br>activated ".$wfic_activated_license;
//echo "<br>".$license_data['remaining'];

if (!empty($license_data["error"])) {

    update_option( 'wfic_license_activated', 'invalid');
    update_option('wfic_license_status', 'invalid');
    
    echo "<div class='error'>";
    echo "<p>";
    _e('<b>License Key not valid, </b>', 'woo-fattureincloud-premium');    
    echo $license_data['error'];
    
    if (!empty($license_data["additional info"])) {
       
        _e('<b> ,' . $license_data["additional info"] . '</b>', 'woo-fattureincloud-premium');
        
    }

    echo "</p>";
    echo "</div>";
    return;

}

if (!empty($license_data["reset"])) {
    


    echo "<div class='updated settings-error notice is-dismissible'>";
    echo "<p>";

    _e('<b> License key De-Activated succesfully </b>', 'woo-fattureincloud-premium');

    echo "</p>";
    echo "</div>";

    
    header("Refresh:0");



}



if (!empty($license_data["activated"])) {
    

    update_option( 'wfic_license_activated', 'valid');
    update_option('wfic_license_instance', $license_data["instance"]);

    echo "<div class='updated settings-error notice is-dismissible'>";
    echo "<p>";

    _e('<b> License key Activated > ' .$license_data['message']. '</b>', 'woo-fattureincloud-premium');

    echo "</p>";
    echo "</div>";

    header("Refresh:0");

} else {

    update_option( 'wfic_license_activated', 'invalid');

}


if ( isset($license_data["success"]) && true === $license_data["success"]) {

    update_option( 'wfic_license_status', 'valid');
    
   // echo "<div class='notice notice-success is-dismissible'>";
    echo "<p>";
            

	echo '<span class="dashicons dashicons-yes-alt" style=" color:green; font-size:1.8em; display:inline-block; " ></span><b> License key valida</b>';

    echo "</p>";
    //echo "</div>";

    



}

        




// 02
/*
if (isset ($license_data["activations"]) ) { 

        print "<pre>";
        print_r($license_data["activations"]);
        print "</pre>";

}
*/


if ( isset($license_data["activations"])) { 

    $license_instance_found = false;    


    foreach($license_data["activations"] as $key => $value) {

     

   // 03
  // echo "<br><b>" . $value['instance'] . "</b>";

   $instance_date = trim( get_option( 'wfic_license_instance' ));

  // echo "<br>".$instance_date;

    // 03 
   

    if ($value['instance'] == $instance_date ) {

        update_option( 'wfic_license_activated', 'valid');

        $license_instance_found = true;

        //echo "<div class='updated settings-error notice is-dismissible'>";
        echo "<p>";
            
        echo '<b> <span class="dashicons dashicons-yes-alt" style=" color:green; font-size:1.8em; display:inline-block; " ></span> License key attivata </b>';
        
        echo "</p>";
        //echo "</div>";

        ?>
        <table cellpadding="10">
            <tr><td>
        <form method="post">
                             
        <?php wp_nonce_field(); ?>
                                                 
        <input  type="submit" class="button button-secondary button-large" name="wfic_license_deactivate" 
                    value="<?php _e('De-Activate License', 'woo-fattureincloud-premium'); ?>" />
                                                 
        </form>
        </td>
            </tr>
        </table>
    
    
    <?php
   
        break;

    }  elseif ($value['instance'] != $instance_date) {

            update_option( 'wfic_license_activated', 'invalid');

            $license_instance_found = false;
        
        } 
    } 

    
   // echo $license_instance_found  ? 'true' : 'false';
    
    if (!$license_instance_found) {

       
        ?>
      
      <table cellpadding="10">
            <tr><td>
            <form method="post">
                                 
            <?php wp_nonce_field(); ?>
                                                     
            <input type="submit" class="button-secondary" name="wfic_license_activate" 
                        value="<?php _e('Activate License', 'woo-fattureincloud-premium'); ?>" />  
                        <span class="dashicons dashicons-arrow-left-alt" style="color:green;vertical-align: middle;font-size:2.8em;float:right" ></span>
                                                     
            </form>

       
            </td>
            </tr>
        </table>
        
        
        <?php


    } elseif ($license_data["remaining"] == 0) {

        echo "<div class='notice notice-update is-dismissible'>";
        echo "<p>";
            
        echo '<b>è stato raggiunto il numero massimo di installazioni possibili con questa licenza</b>';
            
        echo "</p>";
        echo "</div>";


        
    } 
    

}


