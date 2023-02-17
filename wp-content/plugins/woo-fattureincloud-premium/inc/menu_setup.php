<?php

/* better separated voice to check quicker what every single value in add_menu_page is */

function woo_fattureincloud_setup_menu()
{
        $parent_slug = 'woocommerce';
        $page_title  = 'WooCommerce Fattureincloud Admin Page';
        $menu_title  = 'Fattureincloud';
        $capability  = 'manage_woocommerce';
        $menu_slug   = 'woo-fattureincloud-premium';
        $function    = 'woo_fattureincloud_setup_page_display';

        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

}

function page_tabs( $current = 'ordine' ) 
{

    if ( is_admin() ) {
    
    $tabs = array(
    'ordine'   => __('Ordine', 'woo-fattureincloud-premium'),
    'impostazioni'  => __('Impostazioni', 'woo-fattureincloud-premium'),
    'fatture' => __('Fatture', 'woo-fattureincloud-premium'),
    'ricevute' => __('Ricevute', 'woo-fattureincloud-premium'),
    'corrispettivi' => __('Corrispettivi', 'woo-fattureincloud-premium'),
    'email' => __('Email', 'woo-fattureincloud-premium'),
    'licenza' => __('Licenza', 'woo-fattureincloud-premium'),
    'connetti' => __('Connetti', 'woo-fattureincloud-premium')
    );
    $html = '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="?page=woo-fattureincloud-premium&tab=' . $tab . '">' . $name . '</a>';
    }
    $html .= '</h2>';
    echo $html;

    }
}

/**
* Include the new Navigation Bar the Admin page.
*/

function add_to_woocommerce_navigation_bar() {

    if ( is_admin() ) {

    if ( function_exists( 'wc_admin_connect_page' ) ) {

        wc_admin_connect_page(
            
                        array(
					        'id'        => 'woo-fattureincloud-premium',
					        'screen_id' => 'woocommerce_page_woo-fattureincloud-premium',
                            'title'     => __( 'Fattureincloud', 'woo-fattureincloud-premium' ),
           
                            'path'      => add_query_arg(
                                            array(
                                                'page' => 'woo-fattureincloud-premium',
                                                'tab'  => 'ordine',
                                            ),
                                            
                                            'admin.php' ),
                            
	        			)
        );
        
    }

    }
}


