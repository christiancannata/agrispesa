<?php
/**
 * Plugin Text Setting page for woo-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.2.5
 * @package woo-delivery-area-pro
 */

$form  = new WDAP_FORM();
$form->set_header( esc_html__( 'Messages For Shop Page', 'woo-delivery-area-pro' ), $response, $enable_accordian = true );

$data = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );


$form->add_element(
'group', 'wdap_shop_message', array(
	'value' => esc_html__( 'Messages For Shop Page', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);

$errormessage = array(
'notavailable' => esc_html__( 'Product Not Available ', 'woo-delivery-area-pro' ),
'available' => esc_html__( 'Product Available ', 'woo-delivery-area-pro' ),
'invalid' => esc_html__( 'Invalid Zipcode ', 'woo-delivery-area-pro' )			);
foreach ( $errormessage as $key => $message ) {
$placeholder = $message;
$desc = '';

$form->add_element(
	'text', 'wdap_shop_error_' . $key, array(
		'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
		'value' => isset( $data[ 'wdap_shop_error_' . $key ] ) ? $data[ 'wdap_shop_error_' . $key ] : '',
		'desc' => $desc,
		'class' => 'form-control',
		'placeholder' => $placeholder,
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => $message,
	)
);
}

$form->add_element(
'group', 'wdap_category_message', array(
	'value' => esc_html__( 'Messages For Category Page', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);


$errormessage = array(
'notavailable' => esc_html__( 'Product Not Available ', 'woo-delivery-area-pro' ),
'available' => esc_html__( 'Product Available ', 'woo-delivery-area-pro' ),
'invalid' => esc_html__( 'Invalid Zipcode ', 'woo-delivery-area-pro' )			);
foreach ( $errormessage as $key => $message ) {
$placeholder = $message;
$desc = '';

$form->add_element(
	'text', 'wdap_category_error_' . $key, array(
		'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
		'value' => isset( $data[ 'wdap_category_error_' . $key ] ) ? $data[ 'wdap_category_error_' . $key ] : '',
		'desc' => $desc,
		'class' => 'form-control',
		'placeholder' => $placeholder,
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => $message,
	)
);
}

$form->add_element(
'group', 'wdap_product_message', array(
	'value' => esc_html__( 'Messages For Product Page', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);

foreach ( $errormessage as $key => $message ) {
$placeholder = $message;
$desc = '';
$form->add_element(
	'text', 'wdap_product_error_' . $key, array(
		'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
		'value' => isset( $data[ 'wdap_product_error_' . $key ] ) ? $data[ 'wdap_product_error_' . $key ] : '',
		'desc' => $desc,
		'class' => 'form-control',
		'placeholder' => $placeholder,
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => $message,
	)
);
}

$form->add_element(
'group', 'wdap_cart_message', array(
	'value' => esc_html__( 'Messages For Cart Page', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);


$errormessage = array(
'notavailable' => esc_html__( 'Product Not Available ', 'woo-delivery-area-pro' ),
'available' => esc_html__( 'Product Available ', 'woo-delivery-area-pro' ),
'invalid' => esc_html__( 'Invalid Zipcode ', 'woo-delivery-area-pro' ),
'th' => esc_html__( ' Product Availability Status', 'woo-delivery-area-pro' ),
'summary' => esc_html__( 'Summary Message', 'woo-delivery-area-pro' ),

);
foreach ( $errormessage as $key => $message ) {
$placeholder = $message;
$desc = '';
if ( $key == 'th' ) {
	$placeholder = esc_html__( 'Availability Status', 'woo-delivery-area-pro' );
	$desc = esc_html__( 'Shop Table Heading', 'woo-delivery-area-pro' );
}
if ( $key == 'summary' ) {
	$placeholder = esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' );
	$desc = esc_html__( 'Use placeholders {no_products_available} = for number of available products , {no_products_unavailable} = for number of unavailable products ', 'woo-delivery-area-pro' );
}

$form->add_element(
	'text', 'wdap_cart_error_' . $key, array(
		'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
		'value' => isset( $data[ 'wdap_cart_error_' . $key ] ) ? $data[ 'wdap_cart_error_' . $key ] : '',
		'desc' => $desc,
		'class' => 'form-control',
		'placeholder' => $placeholder,
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => $message,
	)
);
}

$form->add_element(
'group', 'wdap_checkout_message', array(
	'value' => esc_html__( 'Message For Checkout Page', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
)
);

foreach ( $errormessage as $key => $message ) {
$placeholder = $message;
$desc = '';
if ( $key == 'th' ) {
	$placeholder = esc_html__( 'Availability Status', 'woo-delivery-area-pro' );
	$desc = esc_html__( 'Shop Table Heading', 'woo-delivery-area-pro' );
}
if ( $key == 'summary' ) {
	$placeholder = esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' );
	$desc = esc_html__( 'Use placeholders {no_products_available} = for number of available products , {no_products_unavailable} = for number of unavailable products ', 'woo-delivery-area-pro' );
}

$form->add_element(
	'text', 'wdap_checkout_error_' . $key, array(
		'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
		'value' => isset( $data[ 'wdap_checkout_error_' . $key ] ) ? $data[ 'wdap_checkout_error_' . $key ] : '',
		'desc' => $desc,
		'class' => 'form-control',
		'placeholder' => $placeholder,
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => $message,
	)
);
}

$form->add_element(
'text', 'wdap_empty_zip_code', array(
	'lable' => esc_html__( 'Empty Zipcode Error', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_empty_zip_code'] ) ? $data['wdap_empty_zip_code'] : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'Please enter zip code.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
	'default_value' => esc_html__( 'Please enter zip code.', 'woo-delivery-area-pro' ),
)
);

$form->add_element(
'text', 'wdap_order_restrict_error', array(
	'lable' => esc_html__( 'Order Restriction Error Message', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_order_restrict_error'] ) ? $data['wdap_order_restrict_error'] : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'We could not complete your order due to Zip Code Unavailability.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
	'default_value' => esc_html__( 'We could not complete your order due to Zip Code Unavailability.', 'woo-delivery-area-pro' ),
)
);


$form->add_element(	'hidden', 'wdap_version', array( 'value' => WDAP_VERSION )	);

$form->add_element(
'hidden', 'operation', array(
	'value' => 'save',
)
);
$form->add_element(
'hidden', 'text_form_submission', array(
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
