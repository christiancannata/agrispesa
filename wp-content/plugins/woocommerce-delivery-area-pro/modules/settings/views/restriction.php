<?php
/**
 * Plugin Restrictions Setting page for wp-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.0
 * @package woo-delivery-area-pro
 */

$form  = new WDAP_FORM();
$form->set_header( esc_html__( 'Country Restriction', 'woo-delivery-area-pro' ), $response, $enable_accordian = true );
$data = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

$form->add_element(
'group', 'wdap_countries_restriction', array(
	'value' => esc_html__( 'Country Restriction', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);

$form->add_element(
'checkbox', 'enable_retrict_country', array(
	'lable' => esc_html__( 'Enable Country Restriction', 'woo-delivery-area-pro' ),
	'value' => 'true',
	'id' => 'date_filters',
	'current' => isset( $data['enable_retrict_country'] ) ? $data['enable_retrict_country'] : '',
	'desc' => esc_html__( 'Yes, perform the delivery searching within a country.', 'woo-delivery-area-pro' ),
	'class' => 'chkbox_class keep_aspect_ratio switch_onoff',
	'data' => array( 'target' => '.enable_retrict_countries' ),
	'default_value' => 'true',
)
);

$countries_obj   = new WC_Countries();
$countries   = $countries_obj->__get( 'countries' );
$newchoose_continent = array();
foreach ( $countries as  $key => $values ) {

$newchoose_continent[] = array(
	'id' => $key,
	'text' => $values,
);

}

$selected_restricted_countries = isset( $data['wdap_country_restriction_listing'] ) ? $data['wdap_country_restriction_listing'] : '';

$form->add_element(
	'category_selector', 'wdap_country_restriction_listing', array(
		'lable' => esc_html__( 'Choose Country', 'woo-delivery-area-pro' ),
		'data' => $newchoose_continent,
		'current' => ( isset( $selected_restricted_countries ) and ! empty( $selected_restricted_countries ) ) ? $selected_restricted_countries : '',
		'desc' => esc_html__( 'Some places of different counties have same zipcodes. If your product delivery area falls under such category, you can specify your country here. By this google api will provide quick and more accurate results without confliction with similar zipcode of other country. Useful only if you are not specifying zipcodes directly in textbox.', 'woo-delivery-area-pro' ),

		'class' => 'enable_retrict_countries',
		'before' => '<div class="fc-9">',
		'after' => '</div>',
		'multiple' => 'false',
		'show' => 'false',

	)
);

$form->add_element(
	'checkbox', 'enable_places_to_retrict_country_only', array(
		'lable' => esc_html__( 'Display Places Of Restricted Country Only', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'id' => 'enable_places_to_retrict_country_only',
		'current' => isset( $data['enable_places_to_retrict_country_only'] ) ? $data['enable_places_to_retrict_country_only'] : '',
		'desc' => esc_html__( 'When country restriction is enabled, display places of restricted country only in autosuggest textbox to user for only shortcode form.', 'woo-delivery-area-pro' ),
		'class' => 'chkbox_class enable_retrict_countries',
		'show' => 'false',
	)
);

$form->add_element(
	'checkbox', 'restrict_places_of_country_checkout', array(
		'lable' => esc_html__( 'Display Places Of Restricted Country Only ( Checkout Page )', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'id' => 'restrict_places_of_country_checkout',
		'current' => isset( $data['restrict_places_of_country_checkout'] ) ? $data['restrict_places_of_country_checkout'] : '',
		'desc' => esc_html__( 'When country restriction is enabled, display places of restricted country only in autosuggest textbox to user for only checkout page.', 'woo-delivery-area-pro' ),
		'class' => 'chkbox_class enable_retrict_countries',
		'show' => 'false',
	)
);

$form->add_element(
	'group', 'wdap_order_restriction', array(
		'value' => esc_html__( 'Order Restriction', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',

	)
);

$form->add_element(
	'checkbox', 'enable_order_restriction', array(
		'lable' => esc_html__( 'Enable Order Restriction ', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'id' => 'date_filters',
		'current' => isset( $data['enable_order_restriction'] ) ? $data['enable_order_restriction'] : '',
		'desc' => esc_html__( 'Yes, enable the order restriction on checkout form. If zipcode specified during checkout is not found in saved delivery area, customer won\'t be able to place an order.', 'woo-delivery-area-pro' ),
		'class' => 'chkbox_class keep_aspect_ratio switch_onoff',
		'data' => array( 'target' => '.enable_order_restriction' ),
		'default_value' => 'true',
	)
);

$form->add_element(
	'message', 'order_restriction_msg', array(
	'lable' => esc_html__( ' ', 'woo-delivery-area-pro' ),
	'value' => esc_html__( 'This Order Restriction setting will not work in case of Local Pickup', 'woo-delivery-area-pro' ),
	'class' => 'enable_order_restriction fc-10 fc-msg fc-success fade in',
	'show' => 'false', 
	
));


$checkout_method = array(
	'via_zipcode' => esc_html__( 'Via Zipcode', 'woo-delivery-area-pro' ),
);
if ( ! empty( $data['wdap_googleapikey'] ) ) {
	$checkout_method['via_address'] = esc_html__( 'Via Address', 'woo-delivery-area-pro' );
}

$post_checkout_avality_method = isset($_POST['wdap_checkout_avality_method']) ? $_POST['wdap_checkout_avality_method'] :'';

$form->add_element(
	'radio', 'wdap_checkout_avality_method', array(
		'lable' => esc_html__( 'Zipcode/Address For Checking On Checkout Page', 'woo-delivery-area-pro' ),
		'current' => ( isset( $data ['wdap_checkout_avality_method'] ) and ! empty( $data ['wdap_checkout_avality_method'] ) ) ? $data ['wdap_checkout_avality_method'] : $post_checkout_avality_method,
		'radio-val-label' => $checkout_method,
		'default_value' => 'via_zipcode',
		'desc' => esc_html__( 'Checking of delivery will be decided based on this option. If via zipcode is selected, zipcode will be taken from the default woocommerce zipcode field and will be used in testing and message will be shown accordingly. if via address is selected, billing address is used for checking delivery status in that area(address). Via Zipcode is recommended way to check for delivery on checkout page.', 'woo-delivery-area-pro' ),
	)
);

$form->add_element(
	'checkbox', 'enable_auto_suggest_checkout', array(
		'lable' => esc_html__( 'Enable Auto Suggest On Checkout Page ', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['enable_auto_suggest_checkout'] ) ? $data['enable_auto_suggest_checkout'] : '',
		'class' => 'chkbox_class keep_aspect_ratio ',
		'default_value' => 'true',
		'desc' => esc_html__( 'Google Autosuggest functionality will enable on billing and shipping address field. Checkout form fields autofill on select of address.', 'woo-delivery-area-pro' ),
	)
);

$form->add_element(
	'group', 'delivery_area_timeslot', array(
		'value' => esc_html__( 'Delivery Time Slot Settings', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);


$form->add_element(
	'checkbox', 'enable_timeslot_listing', array(
		'lable' => esc_html__( 'Enable Delivery Time  Listing ', 'woo-delivery-area-pro' ),
		'value' => 'true',
		'current' => isset( $data['enable_timeslot_listing'] ) ? $data['enable_timeslot_listing'] : '',
		'class' => 'chkbox_class ',
		'default_value' => 'true',
	)
);

$form->add_element(
	'text', 'timeslot_error_message', array(
		'lable' => esc_html__('Delivery Time Slot Error', 'woo-delivery-area-pro' ),
		'value' => isset( $data['timeslot_error_message'] ) ? $data['timeslot_error_message'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Delivery Timeslot Error', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Please enter delivery time slot error on checkout page.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);

$form->add_element(
	'text', 'timeslot_field_label', array(
		'lable' => esc_html__('Delivery Time Slot Label', 'woo-delivery-area-pro' ),
		'value' => isset( $data['timeslot_field_label'] ) ? $data['timeslot_field_label'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Delivery Timeslot Label', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Please enter delivery time slot label on checkout page.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);



$form->set_col(4);
	if ( isset( $data['default_start_time'] ) ) {
	foreach ( $data['default_start_time'] as $i => $label ) {

	$form->add_element( 'html', 'default_start_time_html[' . $i . ']', array(
		'value' => '',
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));

	$form->add_element( 'text', 'default_start_time[' . $i . ']', array(
		'value' => (isset( $data['default_start_time'][ $i ] ) and ! empty( $data['default_start_time'][ $i ] )) ? $data['default_start_time'][ $i ] : '',
		'desc' => __('Start Time - For eg. 08:30','woo-delivery-area-pro'),
		'class' => 'form-control wdap_timeslot_timepicker',
		'required' => true,
		'placeholder' => __( 'Start Time  ', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));

	$form->add_element( 'text', 'default_end_time[' . $i . ']', array(
		'value' => (isset( $data['default_end_time'][ $i ] ) and ! empty( $data['default_end_time'][ $i ] )) ? $data['default_end_time'][ $i ] : '',
		'desc' => __('End Time - For eg. 09:30','woo-delivery-area-pro'),
		'class' => 'form-control wdap_timeslot_timepicker',
		'required' => true,
		'placeholder' => __( 'End Time ', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));

	$form->add_element( 'button','default_remove_button[' . $i . ']', array(
		'value' => __( 'Remove','woo-delivery-area-pro' ),
		'class' => 'repeat_remove_button fc-btn fc-btn-blue btn-sm',
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));
	}
	}
	if ( isset( $data['default_end_time']) ) {
		$default_index = count( $data['default_end_time']);
	} else {
		$default_index = 0;
	}
	$form->set_col( 4 );
	$form->add_element( 'html', 'default_start_time_html[' . $default_index . ']', array(
		'value' => '',
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));
	$form->add_element( 'text', 'default_start_time[' . $default_index . ']', array(
		'value' => ( ! empty( $data['default_start_time'][ $default_index ] ) && isset( $data['default_start_time'][ $default_index ] )) ? $data['default_start_time'][ $default_index ] : '',
		'desc' => __('Start Time - For eg. 08:30','woo-delivery-area-pro'),
		'class' => 'form-control wdap_timeslot_timepicker',
		'required' => true,
		'placeholder' => __( 'Start Time', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));

	$form->add_element( 'text', 'default_end_time[' . $default_index . ']', array(
		'value' => ( ! empty( $data['default_end_time'][ $default_index ] ) && isset( $data['default_end_time'][ $default_index ] )) ? $data['default_end_time'][ $default_index ] : '',
		'desc' => __('End Time - For eg. 09:30','woo-delivery-area-pro'),
		'class' => 'form-control wdap_timeslot_timepicker',
		'required' => true,
		'placeholder' => __( 'End Time', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));
	$form->add_element( 'button','default_remove_button', array(
		'value' => __( 'Add More','woo-delivery-area-pro' ),
		'class' => 'repeat_button fc-btn fc-btn-blue btn-sm',
		'before' => '<div class="fc-3">',
		'after' => '</div>',
	));



$form->add_element(	'hidden', 'wdap_version', array( 'value' => WDAP_VERSION )	);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);

$form->add_element(
	'hidden', 'restrict_form_submission', array(
		'value' => true,
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
