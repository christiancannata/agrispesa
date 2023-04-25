<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Fatturazione', 'woocommerce' ),
			'shipping' => __( 'Consegna', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Fatturazione', 'woocommerce' ),
		),
		$customer_id
	);
}

$oldcol = 1;
$col    = 1;
?>


<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	<div class="account-addresses">
<?php endif; ?>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
	<?php
		$address = wc_get_account_formatted_address( $name );
		$col     = $col * -1;
		$oldcol  = $oldcol * -1;
	?>

	<div class="woocommerce-Address">
		<header class="woocommerce-Address-title">
			<h3 class="my-account--minititle address-title"><?php echo esc_html( $address_title ); ?></h3>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="btn btn-primary btn-xsmall"><?php echo $address ? esc_html__( 'Edit', 'woocommerce' ) : esc_html__( 'Add', 'woocommerce' ); ?></a>
		</header>
		<address>
			<?php
				echo '<strong>Indirizzo:</strong><br/>';
				echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );
				$user_id = get_current_user_id();
				$user_phone = get_field('billing_phone', 'user_' . $user_id);
				$user_mobile = get_field('billing_cellulare', 'user_' . $user_id);
				$user_scala = get_field('shipping_scala', 'user_' . $user_id);
				$user_piano = get_field('shipping_piano', 'user_' . $user_id);
				$user_citofono = get_field('shipping_citofono', 'user_' . $user_id);

				if($address_title == 'Fatturazione') {
					echo '<br/><br/>';
					echo '<strong>Telefono:</strong><br/>';
					if($user_phone) {
						echo $user_phone;
					} else {
						echo '-';
					}
					echo '<br/><br/>';
					echo '<strong>Cellulare:</strong><br/>';
					if($user_mobile) {
						echo $user_mobile;
					}else {
						echo '-';
					}
				} else {
					echo '<br/><br/>';
					echo '<strong>Scala:</strong> ';
					if($user_scala) {
						echo $user_scala;
					} else {
						echo '-';
					}
					echo '<br/><br/>';
					echo '<strong>Piano:</strong> ';
					if($user_piano) {
						echo $user_piano;
					} else {
						echo '-';
					}

					echo '<br/><br/>';
					echo '<strong>Citofono e indicazioni per il corriere:</strong><br/>';
					if($user_citofono) {
						echo $user_citofono;
					} else {
						echo '-';
					}
				}

			?>
		</address>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
	<?php
endif;
