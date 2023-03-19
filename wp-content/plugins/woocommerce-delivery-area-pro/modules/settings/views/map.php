<?php
/**
 * Plugin Setting page for woo-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.2.5
 * @package woo-delivery-area-pro
 */

$form  = new WDAP_FORM();
$form->set_header( esc_html__( 'Product Availability Map ( On Product Page )', 'woo-delivery-area-pro' ), $response, $enable_accordian = true );

$data = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

$form->add_element(
'group', 'map_general_settings', array(
	'value' => esc_html__( 'Product Availability Map ( On Product Page )', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'desc' => esc_html__( 'This map will be displayed on product page in a new tab to display all the locations where the current product can be delivered.', 'woo-delivery-area-pro' ),

)
);

$referrer = '*.'.$_SERVER['HTTP_HOST'].'/*';

$form->add_element(
	'message',
	'wdap_api_key_instructions',
	array(
		'value' => esc_html__('The very first step to get started with Google Maps is to create the right API key for your website. While creating the Google Maps API keys from the Google Cloud Platform, in the key restriction section, you need to choose HTTP referrer and then you will need to enter HTTP referrer according to your website domain name.', 'woo-delivery-area-pro') . '<br><br>' . esc_html__('You need to enter this HTTP referrer during the key creation process :   ', 'woo-delivery-area-pro') . ' &nbsp;&nbsp;&nbsp;&nbsp;<b><span class="wdap_referrer">'.$referrer. '</span></b>&nbsp;&nbsp;&nbsp;&nbsp; <input type="hidden" id="wdap_referrer" value="'.$referrer.'"> <span class="tooltip"><span class="copy_to_clipboard"><img src="'. WDAP_IMAGES. '/copy-to-clipboard.png">  <span class="tooltiptext" id="myTooltip">'.esc_html__('Copy HTTP Referrer To Clipboard','woo-delivery-area-pro').'</span></span></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.woodeliveryarea.com/blog/docs/how-to-create-map-api-key/" target="_blank">View Tutorial</a>',
		'class' => 'fc-msg fc-msg-info wdap_api_key_instructions',
		'show'  => 'true',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);


$form->set_col( 2 );

$desc = sprintf( esc_html__( 'You need to get an api key for google map to work with your website. You can read and follow from %s link to get api keys.', 'woo-delivery-area-pro' ), '<a href ="https://www.woodeliveryarea.com/blog/docs/how-to-create-map-api-key/" target="_blank" >This</a>' );
$form->add_element(
'text', 'wdap_googleapikey', array(
'lable' => esc_html__( 'Google Map API Key', 'woo-delivery-area-pro' ),
'value' => isset( $data['wdap_googleapikey'] ) ? $data['wdap_googleapikey'] : '',
'desc' => $desc,
'class' => 'form-control',
'placeholder' => esc_html__( 'Enter Google Map Key', 'woo-delivery-area-pro' ),
'before' => '<div class="fc-6" >',
'after' => '</div>',
)
);

$key_url = 'http://bit.ly/29Rlmfc';

if ( isset($data['wdap_googleapikey']) && $data['wdap_googleapikey'] == '' ) {

$generate_link = '<a onclick=\'window.open("' . wp_slash( $key_url ) . '", "newwindow", "width=700, height=600"); return false;\' href=\'' . $key_url . '\' class="wpgmp_key_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Generate API Key', 'woo-delivery-area-pro' ) . '</a>';

$form->add_element(
'html', 'wdap_key_btn', array(
	'html'   => $generate_link,
	'before' => '<div class="fc-3">',
	'after'  => '</div>',
)
);


} else {


$generate_link = '<a href="javascript:void(0);" class="wdap_check_key fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Test API Key', 'woo-delivery-area-pro' ) . '</a><span class="wdap_maps_preview"></span>';

$form->add_element(
'html', 'wdap_key_btn', array(
	'html'   => $generate_link,
	'before' => '<div class="fc-3">',
	'after'  => '</div>',
)
);


}

$form->set_col( 1 );


$form->add_element(
'text', 'wdap_map_width', array(
	'lable' => esc_html__( 'Google Map Width', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_map_width'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_width'] ) ) : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'Enter Google Map Width', 'woo-delivery-area-pro' ),
	'desc' => esc_html__( 'Enter here the map width in pixel. Leave it blank for 100% width.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
)
);

$form->add_element(
'text', 'wdap_map_height', array(
	'lable' => esc_html__( 'Google Map Height', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_map_height'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_height'] ) ) : '',
	'class' => 'form-control',
	'required' => true,
	'placeholder' => esc_html__( 'Enter Google Map Height', 'woo-delivery-area-pro' ),
	'desc' => esc_html__( 'Enter map height in pixel. For eg. 700', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',

)
);

$form->add_element(
'number', 'wdap_map_zoom_level', array(
	'lable' => esc_html__( 'Google Map Zoom Level', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_map_zoom_level'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_zoom_level'] ) ) : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'Enter Google Map Zoom Level', 'woo-delivery-area-pro' ),
	'desc' => esc_html__( 'The zoom level of map when the page is loaded.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
	'default_value' => 5,
)
);

$form->add_element(
'text', 'wdap_map_center_lat', array(
	'lable' => esc_html__( 'Map Center Latitude', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_map_center_lat'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_center_lat'] ) ) : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'Enter Map Center Latitude', 'woo-delivery-area-pro' ),
	'desc' => esc_html__( 'Enter here the map center latitude.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
	'default_value' => 40.730610,
)
);

$form->add_element(
'text', 'wdap_map_center_lng', array(
	'lable' => esc_html__( 'Map Center Longitude', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_map_center_lng'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_center_lng'] ) ) : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'Enter Map Center Longitude', 'woo-delivery-area-pro' ),
	'desc' => esc_html__( 'Enter here the map center longitude.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
	'default_value' => -73.935242,
)
);

$form->add_element(
'textarea', 'wdap_map_style', array(
	'lable' => esc_html__( 'Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
	'value' => isset( $data['wdap_map_style'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_style'] ) ) : '',
	'class' => 'form-control',
	'placeholder' => esc_html__( 'Enter Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
	'desc' => esc_html__( 'Copy google map style from snazzymaps.com and paste here.', 'woo-delivery-area-pro' ),
	'before' => '<div class="fc-9" >',
	'after' => '</div>',
)
);
$form->add_element(
'checkbox', 'enable_map_bound', array(
	'lable' => esc_html__( 'Map Bound', 'woo-delivery-area-pro' ),
	'value' => 'true',
	'current' => isset( $data['enable_map_bound'] ) ? $data['enable_map_bound'] : '',
	'desc' => esc_html__( 'Check to enable map bound. ', 'woo-delivery-area-pro' ),
	'default_value' => 'true',
)
);
$form->add_element(
'checkbox', 'enable_markers_on_map', array(
	'lable' => esc_html__( 'Markers on Map', 'woo-delivery-area-pro' ),
	'value' => 'true',
	'current' => isset( $data['enable_markers_on_map'] ) ? $data['enable_markers_on_map'] : '',
	'desc' => esc_html__( 'Check to enable markers on the map.', 'woo-delivery-area-pro' ),
	'default_value' => 'true',
)
);
$form->add_element(
'checkbox', 'enable_polygon_on_map', array(
	'lable' => esc_html__( 'Polygons on Map', 'woo-delivery-area-pro' ),
	'value' => 'true',
	'current' => isset( $data['enable_polygon_on_map'] ) ? $data['enable_polygon_on_map'] : '',
	'desc' => esc_html__( 'Check to enable polygons on the map.', 'woo-delivery-area-pro' ),
	'default_value' => 'true',
)
);


$form->add_element(
	'group', 'product_delivery_area_shortcode', array(

		'value' => esc_html__( 'Global Delivery Area Map Settings', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);


$language = array(
'en' => esc_html__( 'ENGLISH', 'woo-delivery-area-pro' ),
'ar' => esc_html__( 'ARABIC', 'woo-delivery-area-pro' ),
'eu' => esc_html__( 'BASQUE', 'woo-delivery-area-pro' ),
'bg' => esc_html__( 'BULGARIAN', 'woo-delivery-area-pro' ),
'bn' => esc_html__( 'BENGALI', 'woo-delivery-area-pro' ),
'ca' => esc_html__( 'CATALAN', 'woo-delivery-area-pro' ),
'cs' => esc_html__( 'CZECH', 'woo-delivery-area-pro' ),
'da' => esc_html__( 'DANISH', 'woo-delivery-area-pro' ),
'de' => esc_html__( 'GERMAN', 'woo-delivery-area-pro' ),
'el' => esc_html__( 'GREEK', 'woo-delivery-area-pro' ),
'en-AU' => esc_html__( 'ENGLISH (AUSTRALIAN)', 'woo-delivery-area-pro' ),
'en-GB' => esc_html__( 'ENGLISH (GREAT BRITAIN)', 'woo-delivery-area-pro' ),
'es' => esc_html__( 'SPANISH', 'woo-delivery-area-pro' ),
'fa' => esc_html__( 'FARSI', 'woo-delivery-area-pro' ),
'fi' => esc_html__( 'FINNISH', 'woo-delivery-area-pro' ),
'fil' => esc_html__( 'FILIPINO', 'woo-delivery-area-pro' ),
'fr' => esc_html__( 'FRENCH', 'woo-delivery-area-pro' ),
'gl' => esc_html__( 'GALICIAN', 'woo-delivery-area-pro' ),
'gu' => esc_html__( 'GUJARATI', 'woo-delivery-area-pro' ),
'hi' => esc_html__( 'HINDI', 'woo-delivery-area-pro' ),
'hr' => esc_html__( 'CROATIAN', 'woo-delivery-area-pro' ),
'hu' => esc_html__( 'HUNGARIAN', 'woo-delivery-area-pro' ),
'id' => esc_html__( 'INDONESIAN', 'woo-delivery-area-pro' ),
'it' => esc_html__( 'ITALIAN', 'woo-delivery-area-pro' ),
'iw' => esc_html__( 'HEBREW', 'woo-delivery-area-pro' ),
'ja' => esc_html__( 'JAPANESE', 'woo-delivery-area-pro' ),
'kn' => esc_html__( 'KANNADA', 'woo-delivery-area-pro' ),
'ko' => esc_html__( 'KOREAN', 'woo-delivery-area-pro' ),
'lt' => esc_html__( 'LITHUANIAN', 'woo-delivery-area-pro' ),
'lv' => esc_html__( 'LATVIAN', 'woo-delivery-area-pro' ),
'ml' => esc_html__( 'MALAYALAM', 'woo-delivery-area-pro' ),
'it' => esc_html__( 'ITALIAN', 'woo-delivery-area-pro' ),
'mr' => esc_html__( 'MARATHI', 'woo-delivery-area-pro' ),
'nl' => esc_html__( 'DUTCH', 'woo-delivery-area-pro' ),
'no' => esc_html__( 'NORWEGIAN', 'woo-delivery-area-pro' ),
'pl' => esc_html__( 'POLISH', 'woo-delivery-area-pro' ),
'pt' => esc_html__( 'PORTUGUESE', 'woo-delivery-area-pro' ),
'pt-BR' => esc_html__( 'PORTUGUESE (BRAZIL)', 'woo-delivery-area-pro' ),
'pt-PT' => esc_html__( 'PORTUGUESE (PORTUGAL)', 'woo-delivery-area-pro' ),
'ro' => esc_html__( 'ROMANIAN', 'woo-delivery-area-pro' ),
'ru' => esc_html__( 'RUSSIAN', 'woo-delivery-area-pro' ),
'sk' => esc_html__( 'SLOVAK', 'woo-delivery-area-pro' ),
'sl' => esc_html__( 'SLOVENIAN', 'woo-delivery-area-pro' ),
'sr' => esc_html__( 'SERBIAN', 'woo-delivery-area-pro' ),
'sv' => esc_html__( 'SWEDISH', 'woo-delivery-area-pro' ),
'tl' => esc_html__( 'TAGALOG', 'woo-delivery-area-pro' ),
'ta' => esc_html__( 'TAMIL', 'woo-delivery-area-pro' ),
'te' => esc_html__( 'TELUGU', 'woo-delivery-area-pro' ),
'th' => esc_html__( 'THAI', 'woo-delivery-area-pro' ),
'tr' => esc_html__( 'TURKISH', 'woo-delivery-area-pro' ),
'uk' => esc_html__( 'UKRAINIAN', 'woo-delivery-area-pro' ),
'vi' => esc_html__( 'VIETNAMESE', 'woo-delivery-area-pro' ),
'zh-CN' => esc_html__( 'CHINESE (SIMPLIFIED)', 'woo-delivery-area-pro' ),
'zh-TW' => esc_html__( 'CHINESE (TRADITIONAL)', 'woo-delivery-area-pro' ),
);

$form->add_element( 'select', 'wpdap_language', array(
	'lable' => esc_html__( 'Map Language', 'woo-delivery-area-pro' ),
	'current' => isset($data[ 'wpdap_language' ]) ? $data[ 'wpdap_language' ] : 'en',
	'desc' => esc_html__( 'Choose your language for map. Default is English.', 'woo-delivery-area-pro' ),
	'options' => $language,
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));

$form->add_element(
	'text', 'shortcode_map_title', array(
		'lable' => esc_html__( 'Delivey Area Map Title', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_title'] ) ? $data['shortcode_map_title'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Delivery Area Map Title', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter Map name / title here.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => '',
	)
);
$form->add_element(
	'text', 'shortcode_map_description', array(
		'lable' => esc_html__( 'Delivery Area Map Description', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_description'] ) ? $data['shortcode_map_description'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Delivery Area Map Description', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter Map description here.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);

$form->add_element(
	'text', 'shortcode_map_width', array(

		'lable' => esc_html__( 'Google Map Width', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_width'] ) ? $data['shortcode_map_width'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Google Map Width', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter here the map width in pixel. Leave it blank for 100% width.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);
$form->add_element(
	'text', 'shortcode_map_height', array(
		'lable' => esc_html__( 'Google Map Height', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_height'] ) ? $data['shortcode_map_height'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Google Map Height', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter map height in pixel. For eg. 700', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);

$form->add_element(
	'number', 'shortcode_map_zoom_level', array(
		'lable' => esc_html__( 'Google Map Zoom Level', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_zoom_level'] ) ? $data['shortcode_map_zoom_level'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Google Map Zoom Level', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'The zoom level of map when the page is loaded.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => 5,
	)
);
$form->add_element(
	'text', 'shortcode_map_center_lat', array(

		'lable' => esc_html__( 'Map Center Latitude', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_center_lat'] ) ? $data['shortcode_map_center_lat'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Map Center Latitude', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter here the map center latitude.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => 40.730610,
	)
);
$form->add_element(
	'text', 'shortcode_map_center_lng', array(
		'lable' => esc_html__( 'Map Center Longitude', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_center_lng'] ) ? $data['shortcode_map_center_lng'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Map Center Longitude', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter here the map center longitude.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
		'default_value' => -73.935242,
	)
);
$form->add_element(
	'textarea', 'shortcode_map_style', array(
		'lable' => esc_html__( 'Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
		'value' => isset( $data['shortcode_map_style'] ) ? $data['shortcode_map_style'] : '',
		'class' => 'form-control',
		'placeholder' => esc_html__( 'Enter Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Copy google map style from snazzymaps.com and paste here.', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-9" >',
		'after' => '</div>',
	)
);
$marker_img_id = (isset( $data[ 'marker_img_attachment_id' ] ) ) ? $data[ 'marker_img_attachment_id' ]  : '';
$marker_desc =   esc_html__('Upload custom marker icon which show on global delivery area map.','woo-delivery-area-pro');

$form->add_element( 'image_picker', 'marker_img', array(
	'id' => 'marker_img',
	'class' => 'fc-btn fc-btn-submit fc-btn-medium',
	'lable' => esc_html__( 'Marker icon', 'woo-delivery-area-pro' ),
	'src' => (isset( $data['marker_img'] ) ) ? $data['marker_img']  : '',
	'attachment_id' => $marker_img_id,
	'required' => false,
	'choose_button' => esc_html__( 'Upload Icon Image', 'woo-delivery-area-pro' ),
	'remove_button' => esc_html__( 'Remove Icon','woo-delivery-area-pro' ),
	'desc' => $marker_desc

)); 

	

$form->add_element(	'hidden', 'wdap_version', array( 'value' => WDAP_VERSION )	);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);
$form->add_element(
	'hidden', 'map_form_submission', array(
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
