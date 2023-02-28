<?php


class Wpcl_Admin {
	public function __construct() { }

	public function init() {
		add_filter( 'post_row_actions', [ $this, 'wpcl_row_action' ], 10, 2 );
	}


	public function wpcl_row_action( $actions, $post ) {
		global $post;

		if ( $post->post_type == 'product' ) {
			$actions['wpcl-customers'] = '<a href="' . admin_url( 'post.php' ) . '?post=' . $post->ID . '&action=edit#wc-product-customer-list-meta-box">' . __( 'Customers', 'wc-product-customer-list' ) . '</a>';
		}

		return $actions;
	}
}