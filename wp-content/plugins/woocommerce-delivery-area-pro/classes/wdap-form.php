<?php
if ( ! class_exists( 'WDAP_FORM' ) ) {

	class WDAP_FORM extends FlipperCode_HTML_Markup {

		function __construct( $options = array() ) {

			$dubug_info = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );
			$debug_array = array();


			if( isset( $dubug_info[ 'wdap_debug_info' ] ) && !empty( $dubug_info[ 'wdap_debug_info' ] ) ){
				$debug_array = maybe_unserialize( $dubug_info[ 'wdap_debug_info' ] );
			}

			$productOverview = array(
				'debug_array' => $debug_array,
			    'membership_subscription' => true,
				'subscribe_mailing_list' => esc_html__( 'Subscribe to our mailing list', 'woo-delivery-area-pro' ),
				'product_info_heading' => esc_html__( 'Product & Subscription Information', 'woo-delivery-area-pro' ),
				'get_started_heading' => esc_html__( 'Getting Started Guide', 'woo-delivery-area-pro' ),
				'product_info_desc' => esc_html__( 'For each of our plugins, we have created step by step detailed tutorials that helps you to get started quickly.', 'woo-delivery-area-pro' ),
				'get_started_btn_text' => esc_html__( 'View Docs', 'woo-delivery-area-pro' ),
				'installed_version' => esc_html__( 'Installed version :', 'woo-delivery-area-pro' ),
				'latest_version_available' => esc_html__( 'Latest Version Available : ', 'woo-delivery-area-pro' ),
				'updates_available' => esc_html__( 'Update Available', 'woo-delivery-area-pro' ),
				'subscribe_now' => array(
					'heading' => esc_html__( 'Get Updates', 'woo-delivery-area-pro' ),
					'desc1' => esc_html__( 'Receive updates on our new product features and new products effortlessly.', 'woo-delivery-area-pro' ),
					'desc2' => esc_html__( 'We will not share your email addresses in any case.', 'woo-delivery-area-pro' ),
				),

				'product_support' => array(
					'heading' => esc_html__( 'Product Support', 'woo-delivery-area-pro' ),
					'desc' => esc_html__( 'For our each product we have very well explained starting guide to get you started in matter of minutes.', 'woo-delivery-area-pro' ),
					'click_here' => esc_html__( ' Click Here', 'woo-delivery-area-pro' ),
					'desc2' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode. You can see a working demo before making a purchase.', 'woo-delivery-area-pro' ),
					
					'active_plan' => esc_html__( 'Active Plan', 'woo-delivery-area-pro' ),
					'plan_fee' => esc_html__( 'Plan Fee', 'woo-delivery-area-pro' ),
					'start_date' => esc_html__( 'Start Date', 'woo-delivery-area-pro' ),
					'last_payment_date' => esc_html__( 'Last Payment Date', 'woo-delivery-area-pro' ),
					'next_due_date' => esc_html__( 'Next Due Date', 'woo-delivery-area-pro' ),
				),
				'create_support_ticket' => array(
                    'heading' => esc_html__( 'Create Support Ticket', 'woo-delivery-area-pro' ),
                    'desc1' => esc_html__( 'If you have any question and need our help, click below button to create a support ticket and our support team will assist you.', 'woo-delivery-area-pro' ),
                    'link' => array( 
						'label' => esc_html__( 'Create Ticket', 'woo-delivery-area-pro' ),
						'url' => 'https://www.flippercode.com/support'
					)
                ),

                'hire_wp_expert' => array(
                    'heading' => esc_html__( 'Hire Wordpress Expert', 'woo-delivery-area-pro' ),
                    'desc' => esc_html__( 'Do you have a custom requirement which is missing in this plugin?', 'woo-delivery-area-pro' ),
                    'desc1' => esc_html__( 'We can customize this plugin according to your needs. Click below button to send an quotation request.', 'woo-delivery-area-pro' ),
                    'link' => array(
                                    
                        'label' => esc_html__( 'Request a quotation', 'woo-delivery-area-pro' ),
                        'url' => 'https://www.flippercode.com/contact/'
					)
                ),
                'plugin_css_path' => WDAP_CSS

			);

			$productInfo = array(
				'productName' => esc_html__( 'WooCommerce Delivery Area Pro', 'woo-delivery-area-pro' ),
				'productSlug' => esc_html__( 'wdap_view_overview', 'woo-delivery-area-pro' ),
				'productTextDomain' => 'woo-delivery-area-pro',
				'productIconImage' => WDAP_URL . 'core/core-assets/images/wp-poet.png',
				'productVersion' => WDAP_VERSION,
				'docURL' => 'https://www.woodeliveryarea.com/tutorials/',
				'videoURL' => 'https://www.youtube.com/watch?v=0x1gbCgn5b8&list=PLlCp-8jiD3p3skgYCjyW2ooRi62SY8fq6',
				'getting_started_link' => 'https://www.woodeliveryarea.com/blog/docs/how-to-define-delivery-area-using-zipcodes/',
				'productSaleURL' => 'https://shop.woodeliveryarea.com/price/',
				'multisiteLicence' => 'https://shop.woodeliveryarea.com/price/',
				'productOverview' => $productOverview

			);

			$productInfo = array_merge( $productInfo, $options );
			parent::__construct( $productInfo );

		}

	}

}
