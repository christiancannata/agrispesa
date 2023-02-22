<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.linkedin.com/in/stratos-vetsos-08262473/
 * @since      1.0.0
 *
 * @package    Wc_Order_Navigation
 * @subpackage Wc_Order_Navigation/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<li class="order_navigation wide">
	<ul class="clearfix">
	  <?php if ( array_filter( $order_navigation ) ) : ?>

		  <?php if ( ! is_null( $order_navigation[ 'prev_order_id' ] ) ) : $previous_order_id = absint( $order_navigation[ 'prev_order_id' ] ); ?>
				<li><?php echo sprintf( '<a href="%1$s" class="button button-secondary prev-order tips button-small" data-tip=" ' . __( 'Order', 'wc-order-navigation') . ' #%2$s"><span aria-hidden="true">&lsaquo;</span> ' . __( 'Previous', 'wc-order-navigation' ) . ' ' . __( 'Order', 'wc-order-navigation' ) . '</a>', esc_url( sprintf( admin_url( 'post.php?post=%d&action=edit' ), $previous_order_id ) ), $previous_order_id ); ?></li>
		  <?php endif; ?>

			<?php if ( ! is_null( $order_navigation[ 'next_order_id' ] ) ) : $next_order_id = absint( $order_navigation[ 'next_order_id' ] ); ?>
				<li><?php echo sprintf( '<a href="%1$s" class="button button-secondary prev-order tips button-small" data-tip=" ' . __( 'Order', 'wc-order-navigation' ) . ' #%2$s">' . __( 'Next', 'wc-order-navigation' ) . ' ' . __( 'Order', 'wc-order-navigation' ) . ' <span aria-hidden="true">&rsaquo;</span></a>', esc_url( sprintf( admin_url( 'post.php?post=%d&action=edit' ), $next_order_id ) ), $next_order_id ); ?></li>
		  <?php endif; ?>

		<?php else : ?>
			<li><?php _e( 'You need to have at least two orders, to use the navigation.', 'wc-order-navigation' ); ?></li>
		<?php endif; ?>

	</ul>
</li>
