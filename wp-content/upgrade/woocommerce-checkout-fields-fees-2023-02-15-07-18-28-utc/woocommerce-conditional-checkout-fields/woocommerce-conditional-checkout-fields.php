<?php 
/*
Plugin Name: WooCommerce Checkout Fields & Fees
Description: Checkout fields showed according to conditional logic.
Author: Lagudi Domenico
Version: 9.9
*/



/* Const */
//Domain: woocommerce-conditional-checkout-fields
define('WCCCF_PLUGIN_PATH', rtrim(plugin_dir_url(__FILE__), "/") ) ;
define('WCCCF_PLUGIN_ABS_PATH', dirname( __FILE__ ) ); ///ex.: "woocommerce/wp-content/plugins/woocommerce-conditional-checkout-fields"
define('WCCCF_PLUGIN_LANG_PATH', basename( dirname( __FILE__ ) ) . '/languages' ) ;


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
     (is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins') ))	
	)
{
	$wcccf_is_woocommerce_booking_active = false;
	$wcccf_id = 20668577;
	$wcccf_name = "WooCommerce Checkout Fields & Fees";
	$wcccf_activator_slug = "wcccf-activator";
	
	// Classes Init 
	include_once( "classes/com/WCCCF_Globals.php"); 
	require_once('classes/admin/WCCCF_ActivationPage.php');
	
	add_action('init', 'wcccf_global_init');
	add_action('admin_notices', 'wcccf_admin_notices' );
	add_action('admin_menu', 'wcccf_init_act');
	if(defined('DOING_AJAX') && DOING_AJAX)
		wcccf_init_act();
}

/* Functions */
function wcccf_admin_notices()
{
	global $lcccf, $wcccf_name, $wcccf_activator_slug;
	if($lcccf && (!isset($_GET['page']) || $_GET['page'] != $wcccf_activator_slug))
	{
		 ?>
		<div class="notice notice-success">
			<p><?php wcccf_html_escape_allowing_special_tags(sprintf(__( 'To complete the <span style="color:#96588a; font-weight:bold;">%s</span> plugin activation, you must verify your purchase license. Click <a href="%s">here</a> to verify it.', 'woocommerce-conditional-checkout-fields' ), $wcccf_name, get_admin_url()."admin.php?page=".$wcccf_activator_slug)); ?></p>
		</div>
		<?php
	}
}

function wcccf_unregister_css_and_js($enqueue_styles)
{
	
}
function wcccf_eu()
{
	global $wcccf_wpml_helper, $wcccf_field_model, $wcccf_html_helper, $wcccf_product_model, $wcccf_customer_model,
		   $wcccf_country_model, $wcccf_cart_model, $wcccf_order_model, $wcccf_fee_model, $wcccf_file_model, $wcccf_code_model,
		   $wcccf_datetime_model, $wcccf_option_model, $wcccf_admin_order_details_page, $wcccf_field_display_managment, $wcccf_fronted_checkout_page;
	
	//com
	if(!class_exists('WCCCF_Wpml'))
	{
		require_once('classes/com/WCCCF_Wpml.php');
		$wcccf_wpml_helper = new WCCCF_Wpml();
	} 
	if(!class_exists('WCCCF_Field'))
	{
		require_once('classes/com/WCCCF_Field.php');
		$wcccf_field_model = new WCCCF_Field();
	} 
	if(!class_exists('WCCCF_Html'))
	{
		require_once('classes/com/WCCCF_Html.php');
		$wcccf_html_helper = new WCCCF_Html();
	}
	if(!class_exists('WCCCF_Product'))
	{
		require_once('classes/com/WCCCF_Product.php');
		$wcccf_product_model = new WCCCF_Product();
	}
	if(!class_exists('WCCCF_Customer'))
	{
		require_once('classes/com/WCCCF_Customer.php');
		$wcccf_customer_model = new WCCCF_Customer();
	}
	if(!class_exists('WCCCF_Country'))
	{
		require_once('classes/com/WCCCF_Country.php');
		$wcccf_country_model = new WCCCF_Country();
	}
	if(!class_exists('WCCCF_Cart'))
	{
		require_once('classes/com/WCCCF_Cart.php');
		$wcccf_cart_model = new WCCCF_Cart();
	}
	if(!class_exists('WCCCF_Order'))
	{
		require_once('classes/com/WCCCF_Order.php');
		$wcccf_order_model = new WCCCF_Order();
	}
	if(!class_exists('WCCCF_Fee'))
	{
		require_once('classes/com/WCCCF_Fee.php');
		$wcccf_fee_model = new WCCCF_Fee();
	}
	if(!class_exists('WCCCF_File'))
	{
		require_once('classes/com/WCCCF_File.php');
		$wcccf_file_model = new WCCCF_File();
	}
	if(!class_exists('WCCCF_Code'))
	{
		require_once('classes/com/WCCCF_Code.php');
		$wcccf_code_model = new WCCCF_Code();
	}
	if(!class_exists('WCCCF_DateTime'))
	{
		require_once('classes/com/WCCCF_DateTime.php');
		$wcccf_datetime_model = new WCCCF_DateTime();
	}
	if(!class_exists('WCCCF_Option'))
	{
		require_once('classes/com/WCCCF_Option.php');
		$wcccf_option_model = new WCCCF_Option();
	}
	
	
	//admin
	if(!class_exists('WCCCF_FieldCustomConfiguratorPage'))
	{
		require_once('classes/admin/WCCCF_FieldCustomConfiguratorPage.php');
	}
	if(!class_exists('WCCCF_FieldCoreConfiguratorPage'))
	{
		require_once('classes/admin/WCCCF_FieldCoreConfiguratorPage.php');
	}
	if(!class_exists('WCCCF_OrderDetailsPage'))
	{
		require_once('classes/admin/WCCCF_OrderDetailsPage.php');
		$wcccf_admin_order_details_page = new WCCCF_OrderDetailsPage();
	}
	if(!class_exists('WCCCF_FeeConfiguratorPage'))
	{
		require_once('classes/admin/WCCCF_FeeConfiguratorPage.php');
	}
	if(!class_exists('WCCCF_DisplayManagerPage'))
	{
		require_once('classes/admin/WCCCF_DisplayManagerPage.php');
	}
	if(!class_exists('WCCCF_CustomCodePage'))
	{
		require_once('classes/admin/WCCCF_CustomCodePage.php');
	}
	if(!class_exists('WCCCF_OptionPage'))
	{
		require_once('classes/admin/WCCCF_OptionPage.php');
	}
	
	//frontend
	if(!class_exists('WCCCF_FieldDisplayManagment'))
	{
		require_once('classes/frontend/WCCCF_FieldDisplayManagment.php');
		$wcccf_field_display_managment = new WCCCF_FieldDisplayManagment();
	}
	if(!class_exists('WCCCF_CheckoutPage'))
	{
		require_once('classes/frontend/WCCCF_CheckoutPage.php');
		$wcccf_fronted_checkout_page = new WCCCF_CheckoutPage();
	}
	
	/* Actions */
	add_action('admin_menu', 'wcccf_init_admin_panel');
}
function wcccf_global_init()
{
	global $wcccf_is_woocommerce_booking_active ;
	$wcccf_is_woocommerce_booking_active = in_array( 'woocommerce-bookings/woocommerce-bookings.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	
	// Languages 
	load_plugin_textdomain('woocommerce-conditional-checkout-fields', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
function wcccf_init_act()
{
	global $wcccf_activator_slug, $wcccf_name, $wcccf_id;
	new WCCCF_ActivationPage($wcccf_activator_slug, $wcccf_name, 'woocommerce-conditional-checkout-fields', $wcccf_id, WCCCF_PLUGIN_PATH);
}
function wcccf_admin_init()
{
	$remove = remove_submenu_page( 'woocommerce-role-by-amount-spent', 'woocommerce-conditional-checkout-fields');
}	
function wcccf_init_admin_panel()
{ 
	$place = wcccf_get_free_menu_position(69 , .1);
	$cap = 'manage_woocommerce';
	
	
	add_menu_page( 'WooCommerce Checkout Fields & Fees', esc_html__('WooCommerce Checkout Fields & Fees', 'woocommerce-conditional-checkout-fields'), $cap, 'woocommerce-conditional-checkout-fields', null,  'dashicons-cart' , (string)$place);
	$wccc_page = new WCCCF_FieldCustomConfiguratorPage();
	$wccc_page->add_page($cap);
	
	/* under developement 
	$wccc_page = new WCCCF_FieldCoreConfiguratorPage();
	$wccc_page->add_page($cap); */
	
	$wccc_page = new WCCCF_DisplayManagerPage();
	$wccc_page->add_page($cap); 
	
	$wccc_page = new WCCCF_FeeConfiguratorPage();
	$wccc_page->add_page($cap);	
	
	$wccc_page = new WCCCF_CustomCodePage();
	$wccc_page->add_page($cap);
	
	$wccc_page = new WCCCF_OptionPage();
	$wccc_page->add_page($cap);	
	
}
function wcccf_get_free_menu_position($start, $increment = 0.1)
{
	foreach ($GLOBALS['menu'] as $key => $menu) {
		$menus_positions[] = $key;
	}
	
	if (!in_array($start, $menus_positions)) return $start;

	/* the position is already reserved find the closet one */
	while (in_array($start, $menus_positions)) 
	{
		$start += $increment;
	}
	return (string)$start;
}

function wcccf_var_dump($var)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}
?>