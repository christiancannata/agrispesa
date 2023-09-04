<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Memcached Information page
function use_memcached_info_page() {
    ?>
<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1><img class="logo-slika" src="<?php echo plugin_dir_url( __FILE__ ) . '/assets/images/icon-128x128.jpg';?>"></img> Use Memcached</h1>
	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">    
    <?php
// memcached server and port configuration
    $memcached_server = get_option('object_cacher_memcached_server', '127.0.0.1');
    $memcached_port = get_option('object_cacher_memcached_port', '11211');

// Create a Memcached instance
$memcached = new Memcached();

// Add the Memcached server
$memcached->addServer($memcached_server, $memcached_port);

// Try to establish a connection
if ($memcached->getVersion() !== false) {

    // Get stats from the Memcached server
    $stats = $memcached->getStats();

    // Extract the required information
    $version = $stats[$memcached_server.':'.$memcached_port]['version'];
    $processId = $stats[$memcached_server.':'.$memcached_port]['pid'];


// Initialize the $serverHtml variable
$serverHtml = '';
$serverHtml .= '<table style="width: 100%;text-align: center;border: 0 none"><tbody><tr>';
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Memcached Server <h3 style='color: #fff;padding: 0'><b>" . $memcached_server . "</b></h3></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>TCP Port <h3 style='color: #fff;padding: 0'><b>{$memcached_port}</b></h3></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Process ID <h3 style='color: #fff;padding: 0'><b>{$processId}</b></h3></td>";
$serverHtml .= "</tr></tbody></table>";    
$serverHtml .= "</br>"; 
echo $serverHtml;
    
?> 
  				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">  
    						<div class="inside">
    
    
    <?php

function get_all_cache_keys() {
    global $wp_object_cache;

    $all_keys = array();
    $cache_keys = $wp_object_cache->cache;

    foreach ($cache_keys as $group => $group_keys) {
        foreach ($group_keys as $key => $value) {
            $all_keys[] = $key;
        }
    }

    return $all_keys;
}

$keys = get_all_cache_keys();

$dataHtml = '<table>';

foreach ($keys as $key) {
    $dataHtml .= '<tr>';
    $dataHtml .= "<td>$key</td>";

    $dataHtml .= '</tr>';
}

$dataHtml .= '</table>';

// Combine server information and data HTML
$html = $dataHtml;

// Display HTML
echo $html;

////////

} else {
        echo '<div class="notice notice-error"><p>&#128308; Failed to connect to Memcached server on <span style="color:red; font-weight: bold">' . $memcached_server . ':' . $memcached_port . '</span>. Please check your Memcached server configuration.</p></div>';
}
   


?>


	</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">

					<div class="postbox">
<?php
		$config = use_memcached_get_configuration();

        $titleText = (!$config->isEnabled())?
			__("Memcached is disabled.", DOMAIN)
			:
			__("Memcache is enabled.", DOMAIN);
?>
						<h2><span><?php echo $titleText; ?></span></h2>

						<div class="inside">
<?php

		$config = use_memcached_get_configuration();

		$buttonText = (!$config->isEnabled())?
			__("Enable memcached!", DOMAIN)
			:
			__("Disable memcached!", DOMAIN);

		$primaryClass = (!$config->isEnabled())?
			"": "button-primary";

		echo "<form method='post'>";
			echo "<input type='hidden' name='use_memcached_disable_toggle' value='yes' />";
			echo "<p style='text-align: center;'>";
			echo "<button class='button $primaryClass button-hero'>$buttonText</button>";
			echo "</p>";
		echo "</form>";

?>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables -->

			</div>
			<!-- #postbox-container-1 .postbox-container -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
    
<?php
    
    // TODO: Display Memc server information
}
