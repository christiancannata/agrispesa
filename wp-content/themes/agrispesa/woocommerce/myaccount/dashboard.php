<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

//Get User info
//$current_user = wp_get_current_user();


// Set limit
$limit = 3;

// Get customer $limit last orders
$customer_orders = wc_get_orders(array(
	'customer' => get_current_user_id(),
	'limit' => $limit
));

// Count customers orders
$count = count($customer_orders);
$has_sub = wcs_user_has_subscription( '', '', 'active' );
// $currentDate = date("d/m/Y");
//
// print_r($currentDate);


?>

<?php if ( $has_sub):?>
	<div id="box-calendar"></div>

	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/jsCalendar.js"></script>
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/jsCalendar.lang.it.js"></script>
	<script type="text/javascript">
		var today = new Date();
		var myCalendar = jsCalendar.new('#box-calendar');
		// Set date
		myCalendar.setLanguage("it");
		myCalendar.set("31/03/2023");
		myCalendar.min("31/03/2023");

		myCalendar.select([
			"03/04/2023",
			"10/04/2023",
		]);
	</script>

	<?php else: ?>
	<div class="agr-memo">

		<div class="agr-memo--flex">
			<div class="agr-memo--text">
				<h4 class="agr-memo--title">Facciamo noi o scegli tu?</h4>
				<p class="agr-memo--subtitle">
					Può capitare di avere fretta.<br/>
					Per questo prepariamo noi la tua spesa, scegliendo tra i prodotti di stagione più freschi che
					arriveranno a casa tua in una scatola su misura per te.
					<br/><br/>Proviamo?
				</p>
				<a href="<?php echo esc_url(home_url('/')); ?>box/facciamo-noi" class="btn btn-primary agr-memo--button"
				   title="Abbonati alla spesa" class="empty-states--subtitle">Sì, fate voi!</a>
			</div>
			<div class="agr-memo--image">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/box/banner-box.jpg"
					 alt="Facciamo noi o scegli tu?"/>
			</div>
		</div>
	</div>
	<?php endif; ?>


<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_dashboard');

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_before_my_account');

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_after_my_account');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
