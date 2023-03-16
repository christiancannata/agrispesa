<?php
/**
 * This class used to manage permissions in backend.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package Flippercode
 */
global $wp_roles;

$fc_roles = $wp_roles->get_names();
unset( $fc_roles['administrator'] );

$data = array_filter(explode('_',$this->modulePrefix));
$class = $data[0].'_FORM';
$form = new $class();

$textDomain = $form->options['productTextDomain'];

$fc_permissions = [];
$plugin_model_classes = [];

foreach( get_declared_classes() as $class ){

    if (str_contains( $class, $this->modulePrefix )) { 
        $plugin_model_classes[] = $class;
    }
}

foreach ( $plugin_model_classes as $module ) {

    $object = new $module();

    if ( method_exists( $object, 'navigation' ) ) {

    	if (str_contains(get_class($object), 'Overview')) { 
		  	    $fc_permissions[strtolower($data[0]).'_admin_overview'] = esc_html__( 'Plugin Overview', $textDomain );
		}

        if ( ! is_array( $object->navigation() ) ) 
        continue;

        foreach ( $object->navigation() as $nav => $title ) {

            $fc_permissions[$nav] = $title;   

        }
        

    }


}

$form->set_header( esc_html__( 'Manage Permission(s)', $textDomain ), $response, $enable = false );

$success_msg = '';

if( isset($_POST) && isset($_POST['permission_success']) && !empty($_POST['permission_success'] ) ){

 	$success_msg = '<p class="fc-msg fc-success fade in">'.$_POST['permission_success'].'</p>';

}

$form->add_element(
	'html', 'success_msg', array(
		'html'  => $success_msg,
		'before' => '<div class="fc-12 permission_success">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'group', 'update_permissions', array(
		'value'  => esc_html__( 'Manage Permission(s)', $textDomain ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

if ( ! empty( $fc_permissions ) ) {
	$count = 0;

	foreach ( $fc_permissions as $fc_mkey => $fc_mvalue ) {
		
		$permission_row[ $count ][0] = $fc_mvalue;

		foreach ( $fc_roles as $fc_role_key => $fc_role_value ) {
			$urole                      = get_role( $fc_role_key );
			$permission_row[ $count ][] = $form->field_checkbox(
				'fc_map_permissions[' . $fc_role_key . '][' . $fc_mkey . ']', array(
					'value'   => 'true',
					'current' => ( ( @array_key_exists( $fc_mkey, $urole->capabilities ) == true ) ? 'true' : 'false' ),
					'before'  => '<div class="fc-1">',
					'after'   => '</div>',
					'class'   => 'chkbox_class',
				)
			);
		}
		$count++;
	}
	
}

$form->add_element(
	'table', 'fc_save_permission_table', array(
		'heading' => array_merge( array( 'Page' ), $fc_roles ),
		'data'    => $permission_row,
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
	)
);

$form->add_element(
	'submit', 'fc_save_permission', array(
		'value' => esc_html__( 'Save Permissions', $textDomain ),
	)
);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);


$form->render();

