<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Gens_RAF
 * @subpackage Gens_RAF/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Gens_RAF
 * @subpackage Gens_RAF/admin
 * @author     Your Name <email@example.com>
 */
class WPGens_Settings_RAF extends WC_Settings_Page {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $gens_raf    The ID of this plugin.
	 */
	private $gens_raf;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $gens_raf       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {

		$this->id    = 'gens_raf';
		$this->label = __( 'Refer A Friend', 'gens-raf');

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			''         => __( 'General', 'gens-raf' ),
			'emails' => __( 'Email', 'gens-raf' ),
			'howto' => __( 'How to use RAF plugin', 'gens-raf' ),
			'premium' => __( 'RAF PREMIUM version', 'gens-raf' )
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array
	 *
	 * @since 1.0.0
	 * @param string $current_section Optional. Defaults to empty string.
	 * @return array Array of settings
	 */
	public function get_settings( $current_section = '' ) {
		$prefix = 'gens_raf_';
		switch ($current_section) {
			case 'emails':
				$settings = array(
					array(
						'name' => __( 'Email Settings', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'Setup the look of email that will be sent to the referal together with coupon.', 'gens-raf'),
						'id'   => 'email_options',
					),
					array(
						'id'			=> $prefix.'email_subject',
						'name' 			=> __( 'Email Subject', 'gens-raf' ),
						'type' 			=> 'text',
						'desc_tip'		=> __( 'Enter the subject of email that will be sent when notifiying the user of their coupon code.', 'gens-raf'),
						'default' 		=> 'Hey there!'
					),
					array(
						'id'			=> $prefix.'email_message',
						'name' 			=> __( 'Email Message', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc'			=> __( 'Text that will appear in email that is sent to user once they get the code. Use {{code}} to add coupon code.HTML allowed.', 'gens-raf'),
						'default' 		=> 'You referred someone! Here is your coupon code reward: {{code}} .'
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'email_options',
					),
				);
				break;
			case 'howto':
				$settings = array(
					array(
						'name' => __( 'Small tutorial to help you get started', 'gens-raf' ),
						'type' => 'title',
						'desc' => sprintf( __( 'Thanks for using Refer a Friend plugin. You can purchase premium version and support us on <a href="%s" target="_blank">this page</a><br/>
							<h3>SETUP GUIDE</h3>
							<ul>
								<li>1. After installing plugin, go to Refer a friend settings (this page), and click on General tab inside Refer a friend tab. Then setup coupon options.</li>
								<li>2. Click on email tab and populate text that will be sent to user after he gets coupon.</li>
								<li>Thats it! Now every user will have referral link in their account page. After someone makes a purchase through their referral link, and after order is marked as complete. They will recieve coupon in their inbox. For every order that was done through referral, in a single order screen, just below customer you will see the name of the person who referred him. Like on this <a href="%s" target="_blank">screenshot.</a></li>
							</ul>
							', 'gens-raf' ), 'https://wpgens.com/downloads/refer-a-friend-for-woocommerce-premium/','https://wpgens.com/slike/referral2.png'),
						'id'   => 'plugin_options',
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'Plugins', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'plugin_options',
					),
				);
				break;
			case 'plugins':
				$settings = array(
					array(
						'name' => __( 'Check out all of our super cool plugins', 'gens-raf' ),
						'type' => 'title',
						'desc' => sprintf( __( 'Thanks for using Refer a Friend plugin. If you have any cool idea that we could add to plugin, be sure to contact us at <a target="_blank" href="%s">goran@wpgens.com</a>. 
						<br/>Our plugins are coded with best practices in mind, they will not slow down your site or spam database. Guaranteed to work and always up to date.
						Check out all of our plugins at: <a target="_blank" href="%s">this link.</a> Also use that link to signup to our newsletters and get notified when new plugin is out. No SPAM, promise.', 'gens-raf' ), 'mailto:goran@wpgens.com', 'https://wpgens.com'),
						'id'   => 'plugin_options',
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'Plugins', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'plugin_options',
					),
				);
				break;
			case 'premium':
				$settings = array(
					array(
						'name' => __( 'PREMIUM Version has additional features, such as shortcodes:', 'gens-raf' ),
						'type' => 'title',
						'desc' => sprintf( __( 'In order to continue building free plugins, we need sustainable model, thats where premium version comes in. 
						<br/>Besides of all the features that free version has, premium version also has:
						– Simple Shortcode <br/>
						- Advance Shortcode
						– Give coupon to person being referred as well
						– Refer a Friend Woocommerce Tab under each product, that comes with share icons that can directly share referral links (check a screenshot) or check example tab on twenty seventeen theme 
						– Contact Form 7 shortcode
						– Minimum amount person needs to purchase in order for coupon to be generated
						– Each person has statistics how many people he refered, admin sees this for each person as well
						– Order details screen shows referral info if order was made through referral.
						– Referral statistics/info screen for admin.
						– Enable coupons for specific products only
						– Set Expiry date for each coupon
						– One year of support & free updates
						<br/>
						Checkout plugin <a target="_blank" href="%s">at this link.</a>
						', 'gens-raf' ), 'https://wpgens.com/downloads/refer-a-friend-for-woocommerce-premium/'),
						'id'   => 'plugin_options',
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'Plugins', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'plugin_options',
					),
				);
				break;

			default:
				$settings = array(
					array(
						'name' => __( 'General', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'General Options, setup plugin here first.', 'gens-raf' ),
						'id'   => 'general_options',
					),
					array(
						'id'			=> $prefix.'disable',
						'name' 			=> __( 'Disable', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Disable Coupons', 'gens-raf' ), // checkbox only
						'desc'			=> __( 'Check to disable. Referal links wont work anymore.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'		=> $prefix.'cookie_time',
						'name' 		=> __( 'Cookie Time', 'gens-raf' ),
						'type' 		=> 'number',
						'desc_tip'	=> __( 'As long as cookie is saved, user will recieve coupon after referal purchase product.', 'gens-raf'),
						'desc' 		=> __( 'How long to keep cookies before it expires.(In days)', 'gens-raf')
					),
					array(
						'id'		=> $prefix.'cookie_remove',
						'name' 		=> __( 'Single Purchase', 'gens-raf' ),
						'label' 		=> __( 'Single Purchase', 'gens-raf' ), // checkbox only
						'type' 			=> 'checkbox',
						'desc_tip'	=> __( 'This means that coupon is sent only the first time referral makes a purchase, as referral cookie is deleted after it.', 'gens-raf'),
						'desc' 		=> __( 'If checked, cookie will be deleted after customer makes a purchase.', 'gens-raf'),
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'general_options',
					),
					array(
						'name' => __( 'Coupon Settings', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'General Options, setup plugin here first.', 'gens-raf'),
						'id'   => 'coupon_options',
					),
					array(
						'id'			=> $prefix.'coupon_type',
						'name' 			=> __( 'Coupon Type', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 			=> 'select',
						'class'    => 'wc-enhanced-select',
						'options'		=> array(
							'fixed_cart'	=> 'Cart Discount',
							'percent'	=> 'Cart % Discount',
//							'fixed_product'	=> 'Product Discount',
//							'percent_product'	=> 'Product % Discount'
						)
					),
					array(
						'id'		=> $prefix.'coupon_amount',
						'name' 		=> __( 'Coupon Amount', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 		=> 'number',
						'desc_tip'	=> __( ' Entered without the currency unit or a percent sign as these will be added automatically, e.g., ’10’ for 10£ or 10%.', 'gens-raf'),
						'desc' 		=> __( 'Fixed value or percentage off depending on the discount type you choose.', 'gens-raf' )
					),
					/*
					array(
						'id'		=> $prefix.'coupon_duration',
						'name' 		=> __( 'Coupon Duration', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 		=> 'text',
						'class'		=> 'date-picker hasDatepicker',
						'desc' 		=> 'Value is number of days beginning on the coupon creation date.'
					),
					*/
					array(
						'id'		=> $prefix.'min_order',
						'name' 		=> __( 'Minimum Order', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 		=> 'number',
						'desc' 		=> __( 'Define minimum order subtotal in order for coupon to work.', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'individual_use',
						'name' 		=> __( 'Individual Use', 'gens-raf' ),
						'type' 		=> 'checkbox',
						'desc' 	=> __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'gens-raf' ), // checkbox only
						'default' 	=> 'no'
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'coupon_options',
					),
				);
				break;
		}

		/**
		 * Filter Memberships Settings
		 *
		 * @since 1.0.0
		 * @param array $settings Array of the plugin settings
		 */
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

	}

	/**
	 * Output the settings
	 *
	 * @since 1.0
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::output_fields( $settings );
	}


	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}

}

return new WPGens_Settings_RAF();
