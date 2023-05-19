<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\GiftCards\Classes\Compatibilities\Elementor
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWGC_Elementor_Form_Widget' ) ) {

	/**
	 * YWGC_Elementor_Form_Widget
	 */
	class YWGC_Elementor_Form_Widget extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 */
		public function get_name() {
			return 'ywgc-form-widget';
		}

		/**
		 * Get widget title.
		 */
		public function get_title() {
			return esc_html__( 'YITH Gift Card Product Form', 'yith-woocommerce-gift-cards' );
		}

		/**
		 * Get widget icon.
		 */
		public function get_icon() {
			return 'fas fa-address-card';
		}

		/**
		 * Get widget categories.
		 */
		public function get_categories() {
			return array( 'yith' );
		}

		/**
		 * Register widget controls.
		 */
		protected function register_controls() {

			$this->start_controls_section(
				'content_section',
				array(
					'label' => esc_html__( 'Content', 'yith-woocommerce-gift-cards' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'section-title',
				array(
					'label'       => esc_html__( 'Form Title', 'yith-woocommerce-gift-cards' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'placeholder' => esc_html__( 'Your section title', 'yith-woocommerce-gift-cards' ),
				)
			);

			$this->end_controls_section();

		}

		/**
		 * Render widget output on the frontend.
		 */
		protected function render() {

			$settings = $this->get_settings_for_display();

			$html = wp_oembed_get( $settings['section-title'] );

			echo '<div class="ywgc-form-widget-elementor-widget">';

			echo ( $html ) ? $html : $settings['section-title'];

			echo do_shortcode( '[yith_ywgc_display_gift_card_form]' );

			echo '</div>';

		}
	}
}
