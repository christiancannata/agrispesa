<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Redis Information page
function use_memcached_warmer_page() {
    
    // Retrieve the saved value from the database
    $saved_sitemap_url = get_option('sitemap_url');
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

?>

  				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">  
    						<div class="inside">
<?php

} else {
        echo '<div class="notice notice-error"><p>&#128308; Failed to connect to Memcached server on <span style="color:red; font-weight: bold">' . $memcached_server . ':' . $memcached_port . '</span>. Please check your Memcached server configuration.</p></div>';
}

   
?>
<h2>Set Sitemap Location</h2>
                    <form method="POST" action="">
                        <input type="url" name="sitemap_url" style="width:50%" value="<?php echo $saved_sitemap_url; ?>">
                        <input type="submit" class="button button-primary" value="Save & Warm">
                    </form>
                                        <p id="progress"></p>
                                        
                    <?php
// Check if the form is submitted
    if (isset($_POST['sitemap_url'])) {
        // Save the submitted value to the database
        update_option('sitemap_url', $_POST['sitemap_url']);

        // Get the sitemap URL
        $sitemap_url = $_POST['sitemap_url'];

        // Process the sitemap and its sub-sitemaps
        process_sitemaps($sitemap_url);

        echo '<h2>Cache warm-up process completed.</h2>';
    }

    // Retrieve the saved value from the database
    $saved_sitemap_url = get_option('sitemap_url');

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
    
    // TODO: Display Redis server information
}


// Helper function to retrieve links from a sitemap XML content
function get_link_from_sitemap($sitemap_content)
{
    $links = [];

    // Load the sitemap XML content
    $sitemap = new SimpleXMLElement($sitemap_content);

    // Iterate over each URL in the sitemap
    foreach ($sitemap->url as $url) {
        // Extract the URL and add it to the links array
        $links[] = (string)$url->loc;
    }

    return $links;
}

// Helper function to process a sitemap and its sub-sitemaps recursively
function process_sitemaps($sitemap_url)
{
    // Fetch the sitemap content
    $sitemap_content = file_get_contents($sitemap_url);

    // Get the sitemap name
    $sitemap_name = get_sitemaps_name($sitemap_content, $sitemap_url);
    echo '<hr>';
    // Display the sitemap name
    echo '<h2>Sitemap: ' . $sitemap_name . '</h2>';

    // Process the links from the sitemap
    $links = get_link_from_sitemap($sitemap_content);

    // Display the cache warm-up process
    echo '<ul>';

    $totalLinks = count($links);
    $processedLinks = 0;

    foreach ($links as $link) {
        echo '<li>- Processing link: ' . $link . '</li>';
        // Perform the necessary action to warm up the cache for each link
        $result = warm_up_memcached($link);

        if ($result) {
            echo '<li>&#10003;️ Successfully warmed up cache for: ' . $link . '</li>';
        } else {
            echo '<li>&#10060; Failed to warm up cache for: ' . $link . '</li>';
        }
        
        $processedLinks++;

        // Display the progress to the user
        $progress = ($processedLinks / $totalLinks) * 100;
        echo '<script>document.getElementById("progress").innerHTML = "Progress: ' . $progress . '%";</script>';
        echo str_pad('', 4096) . "\n";
        flush();
        ob_flush();
    }

    echo '</ul>';

    // Process the sub-sitemaps if any
    $sub_sitemaps = get_sub_sitemap($sitemap_content);

    foreach ($sub_sitemaps as $sub_sitemap) {
        process_sitemaps($sub_sitemap);
    }
}

// Helper function to retrieve the name of a sitemap
function get_sitemaps_name($sitemap_content, $sitemap_url)
{
    // Load the sitemap XML content
    $sitemap = new SimpleXMLElement($sitemap_content);

    // Check if the sitemap has a name attribute
    if (isset($sitemap['name'])) {
        return (string)$sitemap['name'];
    }

    // If the sitemap doesn't have a name attribute, return the file link
    return $sitemap_url;
}

// Helper function to retrieve sub-sitemaps from a sitemap XML content
function get_sub_sitemap($sitemap_content)
{
    $sub_sitemaps = [];

    // Load the sitemap XML content
    $sitemap = new SimpleXMLElement($sitemap_content);

    // Check if the sitemap is a sitemapindex
    if ($sitemap->getName() === 'sitemapindex') {
        // Iterate over each sitemap in the sitemapindex
        foreach ($sitemap->sitemap as $sub_sitemap) {
            // Extract the sub-sitemap URL and add it to the sub_sitemaps array
            $sub_sitemaps[] = (string)$sub_sitemap->loc;
        }
    }

    return $sub_sitemaps;
}

// Helper function to warm up the cache for a given link
function warm_up_memcached($link)
{
    // Perform the necessary action to warm up the cache for the link
    // For example: Use cURL to open the link and fetch its content

    // Use cURL to fetch the URL content
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode == 200);
}
