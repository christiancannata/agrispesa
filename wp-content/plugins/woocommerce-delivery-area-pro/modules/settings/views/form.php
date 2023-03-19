<?php
/**
 * Plugin Setting page for woo-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.2.5
 * @package woo-delivery-area-pro
 */

$form  = new WDAP_FORM();
$form->set_header( esc_html__( 'Delivery Area Form Settings', 'woo-delivery-area-pro' ), $response, $enable_accordian = true );

$form->add_element(
'group', 'enquiry_from_settings', array(
	'value' => esc_html__( 'Delivery Area Form Settings', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);

$data = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

$apply_on = array(
'product_page' => esc_html__( 'On Product Page', 'woo-delivery-area-pro' ),
'shop_page' => esc_html__( 'On Shop Page ', 'woo-delivery-area-pro' ),
'category_page' => esc_html__( 'On Category Page ', 'woo-delivery-area-pro' ),
'cart_page' => esc_html__( 'On Cart Page', 'woo-delivery-area-pro' ),
'checkout_page' => esc_html__( 'On Checkout Page', 'woo-delivery-area-pro' ),

);

$form->add_element(
'multiple_checkbox', 'apply_on[checkedvalue][]', array(
	'lable' => esc_html__( 'Display Delivery Enquiry Form', 'woo-delivery-area-pro' ),
	'value' => $apply_on,
	'current' =>isset($data['apply_on']['checkedvalue']) ? $data['apply_on']['checkedvalue']:'',
	'class' => 'chkbox_class switch_onoffs',
	'desc' => esc_html__( 'Please select woocommerce pages.', 'woo-delivery-area-pro' ),
	'default_value' => 'product_page',
	'data' => array( 'target' => '.exclude_form_categories' ),

)
);

$choose_categories = isset( $_POST['excludecategories'] ) ? $_POST['excludecategories'] : '';
$form->add_element(
'category_selector', 'excludecategories', array(
	'lable' => esc_html__( 'Exclude Categories', 'woo-delivery-area-pro' ),
	'value' => '',
	'current' => isset( $data ['excludecategories'] ) ? maybe_unserialize( $data ['excludecategories'] ) : $choose_categories,
	'class' => 'chkbox_class exclude_form_categories_excludecategories',
	'data_type' => 'taxonomy=product_cat',
	'show' => 'false',
	'desc' => esc_html__( 'Delivery area enquiry form will exclude from all products which falling in above selected categories.', 'woo-delivery-area-pro' ),
)
);


// End of Delivery Notifications
$form->add_element(
	'group', 'wdap_avl_button_settings', array(
		'value' => esc_html__( 'Delivery Area Form UI Settings', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);


$form->add_element(
	'checkbox', 'disable_availability_tab', array(
		'lable' => esc_html__( 'Disable Product Availability', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['disable_availability_tab'] ) ? $data['disable_availability_tab'] : '',
		'desc' => esc_html__( 'Disable product availability tab on all products', 'woo-delivery-area-pro' ),
		'class' => 'chkbox_class  ',
	)
);

$form->add_element('checkbox', 'disable_zipcode_listing', array(
		'lable' => esc_html__( 'Hide Zipcode List', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['disable_zipcode_listing'] ) ? $data['disable_zipcode_listing'] : '',
		'desc' => esc_html__( 'Hides the listing of zipcodes that is displayed on top of map on product availibility map.', 'woo-delivery-area-pro' ),
		'class' => 'chkbox_class  ',
	)
);

$form->add_element(
	'checkbox', 'disable_availability_status', array(
		'lable' => esc_html__( 'Disable Availability Status', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['disable_availability_status'] ) ? $data['disable_availability_status'] : '',
		'desc' => esc_html__( 'Disable product availability status form cart table on cart and checkout page.', 'woo-delivery-area-pro' ),
		'class' => 'chkbox_class  ',
	)
);


$custom_marker_img_id = (isset( $data[ 'custom_marker_img_attachment_id' ] ) ) ? $data[ 'custom_marker_img_attachment_id' ]  : '';
$desc =   esc_html__('Upload custom marker icon which show on map in product availability tab.','woo-delivery-area-pro');

$form->add_element( 'image_picker', 'custom_marker_img', array(
	'id' => 'custom_marker_img',
	'class' => 'fc-btn fc-btn-submit fc-btn-medium',
	'lable' => esc_html__( 'Custom marker icon', 'woo-delivery-area-pro' ),
	'src' => (isset( $data['custom_marker_img'] ) ) ? $data['custom_marker_img']  : '',
	'attachment_id' => $custom_marker_img_id,
	'required' => false,
	'choose_button' => esc_html__( 'Upload Icon Image', 'woo-delivery-area-pro' ),
	'remove_button' => esc_html__( 'Remove Icon','woo-delivery-area-pro' ),
	'desc' => $desc

)); 

$form->add_element(
	'text', 'search_box_placeholder', array(
		'lable' => esc_html__( 'Search Box Placeholder', 'woo-delivery-area-pro' ),
		'value' => isset( $data['search_box_placeholder'] ) ? $data['search_box_placeholder'] : '',
		'class' => 'form-control',
		'desc' => esc_html__( 'Delivey search box placeholder on WooPages ', 'woo-delivery-area-pro' ),
		'placeholder' => esc_html__( 'Enter Zipcode', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);

$form->add_element(
	'text', 'wdap_check_buttonlbl', array(
		'lable' => esc_html__( 'Button Label', 'woo-delivery-area-pro' ),
		'value' => isset( $data['wdap_check_buttonlbl'] ) ? $data['wdap_check_buttonlbl'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivey search box button label on WooPages', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
	)
);

$form->add_element(
	'text', 'wdap_checkout_buttonlbl', array(
		'lable' => esc_html__( 'Place Order Button Label', 'woo-delivery-area-pro' ),
		'value' => isset( $data['wdap_checkout_buttonlbl'] ) ? $data['wdap_checkout_buttonlbl'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Place Order', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Place Order', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Please enter place order button label on checkout page if label not translated.', 'woo-delivery-area-pro' ),

	)
);


$form->add_element(
	'text', 'wdap_frontend_desc', array(
		'lable' => esc_html__( 'Description', 'woo-delivery-area-pro' ),
		'value' => isset( $data['wdap_frontend_desc'] ) ? $data['wdap_frontend_desc'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Verify your pincode for correct delivery details', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivey search box description text on WooPages', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Verify your pincode for correct delivery details', 'woo-delivery-area-pro' ),
	)
);



$form->add_element(
	'text', 'avl_button_color', array(

		'lable' => esc_html__( 'Button Text Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['avl_button_color'] ) ? $data['avl_button_color'] : '',
		'class' => 'form-control scolor color',
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '#fff',

	)
);

$form->add_element(
	'text', 'avl_button_bgcolor', array(
		'lable' => esc_html__( 'Button Background Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['avl_button_bgcolor'] ) ? $data['avl_button_bgcolor'] : '',
		'class' => 'form-control scolor color',
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '#a46497',

	)
);

$form->add_element(
	'text', 'success_msg_color', array(
		'lable' => esc_html__( 'Success Message Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['success_msg_color'] ) ? $data['success_msg_color'] : '',
		'class' => 'form-control scolor color ',
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '#209620',

	)
);

$form->add_element(
	'text', 'error_msg_color', array(
		'lable' => esc_html__( 'Error Message Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['error_msg_color'] ) ? $data['error_msg_color'] : '',
		'class' => 'form-control scolor color',
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '#ff0000',
	)
);

$form->add_element(
	'templates', 'wdap_zip_form_design', array(
		'id' => 'wdap_zip_form_design',
		'lable' => esc_html__( 'Choose Form Template ( WooCommerce Pages ) ', 'woo-delivery-area-pro' ),
		'product' => 'wp-delivery-area-pro',
		'instance' => 'wdap',
		'tempcol' => '4',
		'enable_slider' => false,
		'dboption' => 'wp-delivery-area-pro',
		'template_types' => array( 'zipcode' ),
		'templatePath' => WDAP_TEMPLATES,
		'templateURL' => WDAP_TEMPLATES_URL,
		'settingPage' => 'wdap_setting_settings',
		'customiser' => 'false',
	)
);


$form->add_element(
	'templates', 'wdap_shortcode_form_design', array(
		'id' => 'wdap_shortcode_form_design',
		'lable' => esc_html__( 'Choose Form Template (Shortcode)', 'woo-delivery-area-pro' ),
		'product' => 'wp-delivery-area-pro',
		'instance' => 'wdap',
		'enable_slider' => false,
		'tempcol' => '4',
		'dboption' => 'wp-delivery-area-pro',
		'template_types' => array( 'shortcode' ),
		'templatePath' => WDAP_TEMPLATES,
		'templateURL' => WDAP_TEMPLATES_URL,
		'settingPage' => 'wdap_setting_settings',
		'customiser' => 'false',
	)
);

$form->add_element(
	'group', 'shortcode_settings', array(
		'value' => esc_html__( 'Shortcode Form Settings', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);

ob_start();
echo do_shortcode( '[delivery_area_form]' );
$preview = ob_get_contents();
ob_clean();
$form->add_element(
	'html', 'shortcode_preview', array(
		'lable' => esc_html__( 'Form Preview', 'woo-delivery-area-pro' ),
		'html' => $preview,
		'before' => '<div class="fc-9">',
		'after' => '</div>',
		'class' => 'email_template_preview custom_email_template_control',
		'desc' => esc_html__( 'Form Preview Will Appear Here.', 'woo-delivery-area-pro' ),
	)
);

$form->add_element(
	'text', 'shortcode_form_title', array(
		'lable' => esc_html__( 'Delivey Area Search Title', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_form_title'] ) ? $data['shortcode_form_title'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Delivery Area Form Title', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box title on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);
$form->add_element(
	'text', 'check_buttonPlaceholder', array(
		'lable' => esc_html__( 'Delivey Area Search Placeholder', 'woo-delivery-area-pro' ),
		'value' => isset( $data['check_buttonPlaceholder'] ) ? $data['check_buttonPlaceholder'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Delivey Area Search Placeholder ', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box placeholder on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);
$form->add_element(
	'text', 'shortcode_form_description', array(
		'lable' => esc_html__( 'Delivery Area Form Description', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_form_description'] ) ? $data['shortcode_form_description'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Delivery Area Form Description', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box description on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);


$form->add_element(
	'text', 'wdap_address_empty', array(

		'lable' => esc_html__( 'Empty Address Message', 'woo-delivery-area-pro' ),
		'value' => isset( $data['wdap_address_empty'] ) ? $data['wdap_address_empty'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Please enter your address.', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box empty error message on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Please enter your address.', 'woo-delivery-area-pro' ),
	)
);
$form->add_element(
	'text', 'address_not_shipable', array(

		'lable' => esc_html__( 'Not Shipping Area Message', 'woo-delivery-area-pro' ),
		'value' => isset( $data['address_not_shipable'] ) ? $data['address_not_shipable'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Sorry, We do not provide shipping in this area.', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box not shipping error message on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Sorry, We do not provide shipping in this area.', 'woo-delivery-area-pro' ),

	)
);


$form->add_element(
	'text', 'address_shipable', array(
		'lable' => esc_html__( 'Shipping Area Message', 'woo-delivery-area-pro' ),
		'value' => isset( $data['address_shipable'] ) ? $data['address_shipable'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Yes, We provide shipping in this area.', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box shipping success message on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Yes, We provide shipping in this area.', 'woo-delivery-area-pro' ),
	)
);

$form->add_element(
	'text', 'wdap_form_buttonlbl', array(
		'lable' => esc_html__( 'Button Label', 'woo-delivery-area-pro' ),
		'value' => isset( $data['wdap_form_buttonlbl'] ) ? $data['wdap_form_buttonlbl'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Delivery search box button label on shortcode page', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
	)
);



$form->add_element(
	'text', 'form_success_msg_color', array(
		'lable' => esc_html__( 'Success Message Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['form_success_msg_color'] ) ? $data['form_success_msg_color'] : '',
		'class' => 'form-control scolor color ',
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '#209620',

	)
);
$form->add_element(
	'text', 'form_error_msg_color', array(

		'lable' => esc_html__( 'Error Message Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['form_error_msg_color'] ) ? $data['form_error_msg_color'] : '',
		'class' => 'form-control scolor color ',
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '#ff0000',

	)
);

$form->add_element(
	'text', 'form_button_color', array(

		'lable' => esc_html__( 'Button Text Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['form_button_color'] ) ? $data['form_button_color'] : '',
		'class' => 'form-control scolor color ',
		'before' => '<div class="fc-9" >',
		'id'    => 'form_button_color',
		'after' => '</div>',
		'default_value' => '#fff',
	)
);

$form->add_element(
	'text', 'form_button_bgcolor', array(

		'lable' => esc_html__( 'Button Background Color', 'woo-delivery-area-pro' ),
		'value' => isset( $data['form_button_bgcolor'] ) ? $data['form_button_bgcolor'] : '',
		'class' => 'form-control scolor color ',
		'before' => '<div class="fc-9" >',
		'id'    => 'form_button_bgcolor',
		'after' => '</div>',
		'default_value' => '#a46497',
	)
);
$form->add_element(
	'checkbox', 'enable_locate_me_btn', array(

		'lable' => esc_html__( 'Enable Locate Me Button ', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['enable_locate_me_btn'] ) ? $data['enable_locate_me_btn'] : '',
		'class' => 'chkbox_class ',
		'default_value' => 'true',

	)
);

$form->add_element(
	'checkbox', 'enable_product_listing', array(
		'lable' => esc_html__( 'Enable Product Listing ', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['enable_product_listing'] ) ? $data['enable_product_listing'] : '',
		'class' => 'chkbox_class ',
		'default_value' => 'true',
	)
);
$form->add_element(
	'text', 'product_listing_error', array(

		'lable' => esc_html__( 'Product Listing Error Message ', 'woo-delivery-area-pro' ),
		'value' => isset( $data['product_listing_error'] ) ? $data['product_listing_error'] : '',
		'class' => 'chkbox_class enable_product_listing ',
		'show'  => false,
		'placeholder' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
		'default_value' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',

	)
);

$form->add_element(
	'text', 'can_be_delivered_redirect_url', array(

		'lable' => esc_html__( 'Delivery Availalble Redirect URL', 'woo-delivery-area-pro' ),
		'value' => isset( $data['can_be_delivered_redirect_url'] ) ? $data['can_be_delivered_redirect_url'] : '',
		'class' => 'chkbox_class can_be_delivered_redirect_url',
		'show'  => false,
		'desc' => esc_html__( 'Please enter URL where site needs to redirect when area specified by user is available for delivery i.e it comes under your delivery area. For eg. you can set URL of your shop page here. This redirection works on global shortcode form only not from default woocommerce pages. If redirect url is not specified the notifiction message is displayed by default.', 'woo-delivery-area-pro' ),
		'default_value' => '',
		'placeholder' => esc_html__( 'Enter URL for redirecting when delivery is possible.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);

$form->add_element(
	'text', 'cannot_be_delivered_redirect_url', array(

		'lable' => esc_html__( 'Delivery Not Availalble Redirect URL', 'woo-delivery-area-pro' ),
		'value' => isset( $data['cannot_be_delivered_redirect_url'] ) ? $data['cannot_be_delivered_redirect_url'] : '',
		'class' => 'chkbox_class cannot_be_delivered_redirect_url',
		'show'  => false,
		'desc' => esc_html__( 'Please enter URL where site needs to redirect when delivery is not possible in the area specified by user. For eg. you can set URL of your any custom page here displaying a sorry message. This redirection works on global shortcode form only not from default woocommerce pages.  If redirect url is not specified the notifiction message is displayed by default.', 'woo-delivery-area-pro' ),
		'default_value' => '',
		'placeholder' => esc_html__( 'Enter URL for redirecting when delivery is not possible.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);

$form->add_element(
	'text', 'product_listing_error', array(

		'lable' => esc_html__( 'Product Listing Error Message ', 'woo-delivery-area-pro' ),
		'value' => isset( $data['product_listing_error'] ) ? $data['product_listing_error'] : '',
		'class' => 'chkbox_class enable_product_listing ',
		'show'  => false,
		'placeholder' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
		'default_value' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);


$form->add_element(	'hidden', 'wdap_version', array( 'value' => WDAP_VERSION )	);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);
$form->add_element(
	'hidden', 'form_form_submission', array(
		'value' => true,
	)
);
$form->add_element(
	'hidden', 'hidden_zip_template', array(
		'value' => !empty($data['default_templates']['zipcode']) ? $data['default_templates']['zipcode'] : 'default',
		'id' => 'hidden_zip_template',
	)
);
$form->add_element(
	'hidden', 'hidden_shortcode_template', array(
		'value' => !empty($data['default_templates']['shortcode']) ? $data['default_templates']['shortcode'] : 'default'  ,
		'id' => 'hidden_shortcode_template',
	)
);
if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_text_field( $_GET['doaction'] ) ) {

	$form->add_element(
		'hidden', 'entityID', array(
			'value' => intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) ),
		)
	);
}

$form->add_element(
	'submit', 'WCRP_save_settings', array(
		'value' => esc_html__( 'Save Settings', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-2">',
		'after' => '</div>',
	)
);
$form->render();
