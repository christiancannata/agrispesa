<?php
/**
 * Plugin Setting page for woo-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.2.5
 * @package woo-delivery-area-pro
 */

$form  = new WDAP_FORM();
$form->set_header( esc_html__( 'Apply Custom css', 'woo-delivery-area-pro' ), $response, $enable_accordian = true );

$form->add_element(
	'group', 'wdap_custom_css', array(
		'value' => esc_html__( 'Apply Custom css', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);

$data = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );


//custom css style for frontend	
$form->add_element(
	'textarea', 'wdap_custom_box_css', array(
		'lable' => esc_html__( 'Enter Custom css Style', 'woo-delivery-area-pro' ),
		'value' => isset( $data['wdap_custom_box_css'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_custom_box_css'] ) ) : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Custom css Style', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Add here custom CSS for the frontend delivery area form.','woo-delivery-area-pro'),
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
	'hidden', 'plugin_form_submission', array(
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
