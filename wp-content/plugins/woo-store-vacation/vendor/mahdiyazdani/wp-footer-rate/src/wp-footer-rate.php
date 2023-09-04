<?php
/**
 * Description: Display a notice in the footer of the admin pages asking for a review.
 * Author:      Mahdi Yazdani
 * Author URI:  https://mahdiyazdani.com
 * Version:     1.0.0
 * Text Domain: wp-footer-rate
 * License:     GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package WP_Footer_Rate
 */

namespace WP_Footer_Rate;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class Rate.
 */
class Rate {

	/**
	 * The ID of the theme/plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The name of the theme/plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The slug of the theme/plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * Name of the transient to retrieve.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $transient_name;

	/**
	 * Whether the notice can be shown.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $do_render;

	/**
	 * Theme or plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Time until expiration in seconds.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $expiration;

	/**
	 * Bootstrap everything.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id         The ID of the theme/plugin.
	 * @param string $slug       The slug of the theme/plugin.
	 * @param string $name       Theme/Plugin name.
	 * @param bool   $do_render  Optional. Determine where the notice can be displayed.
	 * @param string $type       Optional. Determine whether the notice is meant for a theme or plugin.
	 * @param string $expiration Optional. Time until expiration in seconds.
	 *
	 * @return void
	 */
	public function __construct( $id, $slug, $name, $do_render = true, $type = 'plugin', $expiration = MONTH_IN_SECONDS ) {

		$this->id          = sanitize_text_field( $id );
		$this->slug        = sanitize_text_field( $slug );
		$this->name        = sanitize_text_field( $name );
		$this->type        = sanitize_text_field( $type );
		$this->expiration  = sanitize_text_field( $expiration );
		$this->do_render   = $do_render;
		self::$transient_name = str_replace( '-', '_', $slug ) . '_rated';

		$this->init();
	}

	/**
	 * Load actions.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init() {

		add_action( 'in_admin_footer', array( $this, 'footer_text_html' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
		add_action( 'wp_ajax_wp_footer_rate_rated', array( $this, 'rated' ) );
	}

	/**
	 * Print ask for review notice in plugin’s settings pages.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function footer_text_html() {

		// Bail early, in case it is not allowed to print-out review notices.
		if ( ! $this->do_render ) {
			return;
		}

		// Determine the page direction.
		$class_name = is_rtl() ? 'alignright' : 'alignleft';

		if ( ! get_transient( self::$transient_name ) ) {

			printf(
			/* translators: 1: Open paragraph tag, 2: Plugin name, 3: Five stars, 4: Close paragraph tag. */
				esc_html__( '%1$sIf you like %2$s please leave us a %3$s rating to help us spread the word!%4$s', 'wp-footer-rate' ),
				sprintf( '<p id="wp-footer-rate-rating" class="%s">', esc_attr( $class_name ) ),
				sprintf( '<strong>%s</strong>', esc_html( $this->name ) ),
				'<a href="https://wordpress.org/support/' . esc_html( $this->type ) . '/' . esc_html( $this->slug ) . '/reviews?filter=5#new-post" target="_blank" id="wp-footer-rate-link" rel="noopener noreferrer nofollow" aria-label="' . esc_attr__( 'five star', 'wp-footer-rate' ) . '" data-rated="' . esc_attr__( 'Thanks :)', 'wp-footer-rate' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
				'</p>'
			);
			?>
			<script type="text/javascript">
				document.getElementById( 'wp-footer-rate-link' ).addEventListener( 'click', async ( e ) => {
					const $this = e.target;
					const data = new FormData();
					data.append( '_ajax_nonce', '<?php echo wp_create_nonce( self::$transient_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>' );
					data.append( 'action', 'wp_footer_rate_rated' );
					try {
						await fetch(
							'<?php echo esc_url( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
							{
								method: 'POST',
								credentials: 'same-origin',
								body: data
							}
						)
							.then( () => {
								$this.parentNode.innerText = $this.dataset.rated;
							} );
					} catch ( { message } ) {
						throw new Error( message );
					}
				} );
			</script>
			<?php
		} else {
			printf(
			/* translators: 1: Open paragraph tag, 2: Plugin name, 3: Close paragraph tag. */
				esc_html__( '%1$sThank you for using %2$s.%3$s', 'wp-footer-rate' ),
				sprintf( '<p id="wp-footer-rate-rating" class="%s">', esc_attr( $class_name ) ),
				sprintf( '<strong>%s</strong>', esc_html( $this->name ) ),
				'</p>'
			);
		}

		?><style>#wpfooter{display:block !important;}</style><?php
	}

	/**
	 * Filters the “Thank you” text displayed in the admin footer.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The content that will be printed.
	 *
	 * @return string
	 */
	public function admin_footer_text( $text ) {

		// Avoid printing out original admin footer text.
		if ( $this->do_render ) {
			return '';
		}

		return $text;
	}

	/**
	 * Triggered when clicking the rating footer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function rated() {

		// Verify the nonce.
		check_ajax_referer( self::$transient_name );

		// Set the transient.
		set_transient( self::$transient_name, 1, sanitize_text_field( $this->expiration ) );

		exit();
	}

	/**
	 * Delete the existing user choice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function remove() {

		delete_transient( self::$transient_name );
	}
}
