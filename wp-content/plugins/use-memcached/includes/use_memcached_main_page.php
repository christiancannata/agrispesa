<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


// ------------------------------------------------------------------------
// plugin domain
// ------------------------------------------------------------------------
const DOMAIN = "use-memcached";

/**
 * Enqueue a script in the WordPress admin on admin.php.
 *
 * @param int $hook Hook suffix for the current admin pagees.
 */
function pluginsclub_memcached_menu_page() {
    $screen = get_current_screen();
    if ( $screen->id === 'toplevel_page_use-memcached' || $screen->id === 'use-memcached_page-info' || $screen->id === 'use-memcached_page-cache-warmer') {
    }
}
add_action( 'admin_enqueue_scripts', 'pluginsclub_memcached_menu_page' );


function pluginsclub_memcached_menu_icon() {
wp_enqueue_style( 'pluginsclub_memcached_plugin', plugin_dir_url( __FILE__ ) . 'assets/css/settings-page.css', array(), '1.0.4' );
}
add_action( 'admin_enqueue_scripts', 'pluginsclub_memcached_menu_icon' );

// Register menu pages
add_action('admin_menu', 'use_memcached_register_menu_pages');
function use_memcached_register_menu_pages() {
    $icon_url = plugin_dir_url( __FILE__ ) . 'assets/images/icon-128x128.jpg';

    add_menu_page(
        'Use Memcached',
        'Use Memcached',
        'manage_options',
        'use-memcached',
        'use_memcached_main_page',
        $icon_url,
        20
    );

    // Set the icon size
    global $menu;
    $menu[20][6] = $icon_url;
    $menu[20][7] = 'dashicons-database-view'; // Fallback dashicon class
    $menu[20][1] = '20px'; // Set the width of the icon
    $menu[20][2] = '20px'; // Set the height of the icon
}


// Memc server and port configuration
    $memcached_server = get_option('object_cacher_memcached_server', '127.0.0.1');
    $memcached_port = get_option('object_cacher_memcached_port', '11211');
// Connect to Memc and ONLY THEN load the other two admin pages
try {    
// Connect to Memc
// Create a Memcached instance
$memcached = new Memcached();

// Add the Memcached server
$memcached->addServer($memcached_server, $memcached_port);


// Try to establish a connection
if ($memcached->getVersion() !== false) {

    
    // Register menu pages
add_action('admin_menu', 'object_cacher_register_submenu_pages');
function object_cacher_register_submenu_pages() {

    add_submenu_page(
        'use-memcached',
        'Memcached Information',
        'Memc. Information',
        'manage_options',
        'use-memcached-info',
        'use_memcached_info_page'
    );
    
    add_submenu_page(
       'use-memcached',
        'Cache Warmer',
        'Cache Warmer',
        'manage_options',
        'use-memcached-warmer',
        'use_memcached_warmer_page'
    );
}
    
    
}


}
catch (Exception $e) {
}











// memcached Configuration Page
function use_memcached_main_page() {
    if (isset($_POST['submit'])) {
        // Retrieve Memcached server and port values from form submission
        $memcached_server = isset($_POST['memcached_server']) && !empty($_POST['memcached_server']) ? sanitize_text_field($_POST['memcached_server']) : get_option('object_cacher_memcached_server', '127.0.0.1');
        $memcached_port = isset($_POST['memcached_port']) && !empty($_POST['memcached_port']) ? sanitize_text_field($_POST['memcached_port']) : get_option('object_cacher_memcached_port', '11211');

        // Save Memc server and port to database
        update_option('object_cacher_memcached_server', $memcached_server);
        update_option('object_cacher_memcached_port', $memcached_port);
    }

    // Memc server and port configuration
    $memcached_server = get_option('object_cacher_memcached_server', '127.0.0.1');
    $memcached_port = get_option('object_cacher_memcached_port', '11211');

    ?>

<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1><img class="logo-slika" src="<?php echo plugin_dir_url( __FILE__ ) . '/assets/images/icon-128x128.jpg';?>"></img> Use Memcached</h1>
	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">


							
<?php
// Memc server and port configuration
    $memcached_server = get_option('object_cacher_memcached_server', '127.0.0.1');
    $memcached_port = get_option('object_cacher_memcached_port', '11211');


// Create a Memcached instance
$memcached = new Memcached();


// Add the Memcached server
$memcached->addServer($memcached_server, $memcached_port);

// Get stats from the Memcached server
$stats = $memcached->getStats();

// Try to establish a connection
if ($memcached->getVersion() !== false) {

// Extract the required information
$currentMemoryUsage = $stats[$memcached_server.':'.$memcached_port]['bytes'];
$maxMemoryLimit = $stats[$memcached_server.':'.$memcached_port]['limit_maxbytes'];


$uptime = $stats[$memcached_server.':'.$memcached_port]['uptime'];
$currentConnections = $stats[$memcached_server.':'.$memcached_port]['curr_connections'];
$bytesRead = $stats[$memcached_server.':'.$memcached_port]['bytes_read'];
$bytesWritten = $stats[$memcached_server.':'.$memcached_port]['bytes_written'];
$currentItems = $stats[$memcached_server.':'.$memcached_port]['curr_items'];


$usagePercentage = ($currentMemoryUsage / $maxMemoryLimit) * 100;

if ($usagePercentage > 90) {
    $color = 'red';
} elseif ($usagePercentage > 70) {
    $color = 'orange';
} else {
    $color = 'green';
}


    // Convert to human-readable format
    $currentMemoryUsageReadable = formatByte($currentMemoryUsage);
    $maxMemoryLimitReadable = formatByte($maxMemoryLimit);


if ($uptime > 60) {
    $uptime = formatTime($uptime);
}
else {
    $uptime = $uptime . ' seconds';
}

$serverHtml .= '<table style="width: 100%;text-align: center;border: 0 none"><tbody><tr>';
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Memory Usage <h3 style='color: #fff;padding: 0'><b><span style='color: {$color};'>{$currentMemoryUsageReadable}</span></b>/<b> {$maxMemoryLimitReadable}</b></h3></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Uptime <h3 style='color: #fff;padding: 0'><b>{$uptime}</b></h3></td>";

$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Current Connections <h3 style='color: #fff;padding: 0'><b>{$currentConnections}</b></h3></td>";
$serverHtml .= "</tr></tr>";  
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Bytes Read </br><b> {$bytesRead}</b></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Bytes Written </br><b>{$bytesWritten}</b></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Current Item </br><b>{$currentItems}</b></td>";
$serverHtml .= "</tr></tbody></table>";    
$serverHtml .= "</br>"; 
echo $serverHtml;
} else {
    //echo "Failed to connect to Memcached server.";
}
    
    
?>    

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<h2><span><?php esc_attr_e( 'Memcached Configuration', 'WpAdminStyle' ); ?></span></h2>

						<div class="inside">
 <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="memcached_server">Server</label></th>
                    <td><input type="text" name="memcached_server" id="memcached_server" value="<?php echo esc_attr($memcached_server); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="memcached_port">Port</label></th>
                    <td><input type="number" name="memcached_port" id="memcached_port" value="<?php echo esc_attr($memcached_port); ?>"></td>
                </tr>
            </table>
                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
        </form>

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


// memcached server and port configuration
    $memcached_server = get_option('object_cacher_memcached_server', '127.0.0.1');
    $memcached_port = get_option('object_cacher_memcached_port', '11211');

// Create a Memcached instance
$memcached = new Memcached();

// Add the Memcached server
$memcached->addServer($memcached_server, $memcached_port);

// Try to establish a connection
if ($memcached->getVersion() !== false) {
    // Display success message
        echo '<div class="notice notice-success"><p>&#128994; Successfully connected to Memcached server on <span style="color:green; font-weight: bold">' . $memcached_server . ':' . $memcached_port . '</span></p></div>';

} else {
        echo '<div class="notice notice-error"><p>&#128308; Failed to connect to Memcached server on <span style="color:red; font-weight: bold">' . $memcached_server . ':' . $memcached_port . '</span>. Please check your Memcached server configuration.</p></div>';
}


    try {
        // Create a Memcached instance
$memcached = new Memcached();

// Add the Memcached server
$memcached->addServer($memcached_server, $memcached_port);

// Update wp-config.php file
        $config_file = ABSPATH . 'wp-config.php';

        // Read the current contents of wp-config.php
        $config_content = file_get_contents($config_file);

        // Define the replacement constants
        $replacement = "
define('USE_MEMCACHED_SERVER', '{$memcached_server}');
define('USE_MEMCACHED_PORT', '{$memcached_port}');";

        // Check if the constants already exist in the file
        $existing_constants = "define('USE_MEMCACHED_SERVER',";
        if (strpos($config_content, $existing_constants) !== false) {
            // Replace the existing constant values with the new values
            $updated_content = preg_replace("/define\('USE_MEMCACHED_SERVER',\s*'.*?'\);/", "define('USE_MEMCACHED_SERVER', '{$memcached_server}');", $config_content);
            $updated_content = preg_replace("/define\('USE_MEMCACHED_PORT',\s*'.*?'\);/", "define('USE_MEMCACHED_PORT', '{$memcached_port}');", $updated_content);
        } else {
            // Insert the new constants just after the opening <?php tag
            $updated_content = str_replace('<?php', '<?php' . $replacement, $config_content);
        }

        // Write the updated contents back to wp-config.php
        file_put_contents($config_file, $updated_content);


    // Perform custom cache actions
    // If connection is successfully ONLY THEN enable caching in wp-config.php and symlink the advanced-cache.php file
    // Add to wp-config.php
    if (get_option('emcached_cache_added_to_wp_config')) {
        return; // Already added, so no need to do anything
    }

    $wp_config_path = ABSPATH . 'wp-config.php';
$config_contents = file_get_contents($wp_config_path);

if (strpos($config_contents, "define('WP_CACHE', true);") === false) {
    if (!get_option('memcached_cache_added_to_wp_config')) {
        $config_contents = preg_replace('/<\?php\s*(\R)/', "<?php\ndefine( 'WP_CACHE', true );\n$1", $config_contents, 1);
        file_put_contents($wp_config_path, $config_contents);
        update_option('memcached_cache_added_to_wp_config', true);
    }
}
    } catch (Exception $e) {
    }    



?>


<?php


}






// Function to format time in a human-readable format
function formatTime($seconds) {
    $intervals = array(
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60
    );

    foreach ($intervals as $interval => $secondsInInterval) {
        $quotient = floor($seconds / $secondsInInterval);
        if ($quotient >= 1) {
            $unit = ($quotient == 1) ? $interval : $interval . 's';
            return $quotient . ' ' . $unit;
        }
    }

    return $seconds . ' seconds';
}

// Function to convert bytes to human-readable format
function formatByte($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}