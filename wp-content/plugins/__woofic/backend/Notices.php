<?php
/**
 * WooFic
 *
 * @package   WooFic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 Christian Cannata
 * @license   GPL 2.0+
 * @link      https://christiancannata.com
 */

namespace WooFic\Backend;

use WooFic\Engine\Base;
use Yoast_I18n_WordPressOrg_v3;

/**
 * Everything that involves notification on the WordPress dashboard
 */
class Notices extends Base {

	/**
	 * Initialize the class
	 *
	 * @return void|bool
	 */
	public function initialize() {
		if ( ! parent::initialize() ) {
			return;
		}

		//\wpdesk_wp_notice( \__( 'Updated Messages', W_TEXTDOMAIN ), 'updated' );

		$builder = new \Page_Madness_Detector(); // phpcs:ignore

		if ( $builder->has_entrophy() ) {
			\wpdesk_wp_notice( \__( 'A Page Builder/Visual Composer was found on this website!', W_TEXTDOMAIN ), 'error', true );
		}

		/*
		 * Review plugin notice.
		 */
		new \WP_Review_Me(
			array(
				'days_after' => 15,
				'type'       => 'plugin',
				'slug'       => W_TEXTDOMAIN,
				'rating'     => 5,
				'message'    => \__( 'Review me!', W_TEXTDOMAIN ),
				'link_label' => \__( 'Click here to review', W_TEXTDOMAIN ),
			)
		);

		/*
		 * Alert after few days to suggest to contribute to the localization if it is incomplete
		 * on translate.wordpress.org, the filter enables to remove globally.
		 */
		if ( \apply_filters( 'woofic_alert_localization', true ) ) {
			new Yoast_I18n_WordPressOrg_v3(
				array(
					'textdomain' => W_TEXTDOMAIN,
					'woofic'     => W_NAME,
					'hook'       => 'admin_notices',
				),
				true
			);
		}

	}

}
