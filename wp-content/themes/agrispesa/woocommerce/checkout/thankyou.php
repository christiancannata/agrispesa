<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$next_week = date("LYmd", strtotime("+1 week"));
$next_week_weekday = date("l", strtotime("+1 week"));
$next_week_day = date("d", strtotime("+1 week"));
$next_week_month = date("m", strtotime("+1 week"));
$next_week_year = date("Y", strtotime("+1 week"));

//Giorni
$weekday_it = '';
if($next_week_weekday === 'Monday') {
	$weekday_it = 'Lunedì';
} else if($next_week_weekday === 'Tuesday') {
	$weekday_it = 'MArtedì';
} else if($next_week_weekday === 'Wednesday') {
	$weekday_it = 'Mercoledì';
} else if($next_week_weekday === 'Thursday') {
	$weekday_it = 'Giovedì';
} else if($next_week_weekday === 'Friday') {
	$weekday_it = 'Venerdì';
} else if($next_week_weekday === 'Saturday') {
	$weekday_it = 'Sabato';
} else if($next_week_weekday === 'Sunday') {
	$weekday_it = 'Domenica';
}
//Mesi
$month_it = '';
if($next_week_month === '01') {
	$month_it = 'Gennaio';
} else if($next_week_month === '02') {
	$month_it = 'Febbraio';
} else if($next_week_month === '03') {
	$month_it = 'Marzo';
} else if($next_week_month === '04') {
	$month_it = 'Aprile';
} else if($next_week_month === '05') {
	$month_it = 'Maggio';
} else if($next_week_month === '06') {
	$month_it = 'Giugno';
} else if($next_week_month === '07') {
	$month_it = 'Luglio';
} else if($next_week_month === '08') {
	$month_it = 'Agosto';
} else if($next_week_month === '09') {
	$month_it = 'Settembre';
} else if($next_week_month === '10') {
	$month_it = 'Ottobre';
} else if($next_week_month === '11') {
	$month_it = 'Novembre';
} else if($next_week_month === '12') {
	$month_it = 'Dicembre';
}
?>




<div class="woocommerce-order">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<div class="thankyou">
				<div class="thankyou--intro">
					<h1 class="thankyou--title">Grazie, <?php echo $order->get_billing_first_name(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>!
						<br/>Il tempo di raccogliere,<br class="only-desktop"/> e siamo da te.</h1>
					<p class="thankyou--subtitle">Riceverai presto una mail con i dettagli del tuo ordine.</p>
					<div class="thankyou--details">
						<div class="thankyou--details--item">
							<span class="icon-consegna"></span>
							<div class="thankyou--details--text">
								<h3 class="thankyou--details--title">Consegniamo la tua scatola</h3>
								<p class="thankyou--details--info">Mercoledì, 22 febbraio 2023
									<?php //echo get_order_delivery_date_from_date(new \DateTime(),null, $order->get_shipping_postcode()); ?>
								</p>
							</div>
						</div>
						<div class="thankyou--details--item">
							<span class="icon-ordine"></span>
							<div class="thankyou--details--text">
								<h3 class="thankyou--details--title">Sul tuo ordine</h3>
								<p class="thankyou--details--info"><?php echo 'È il numero #'.$order->get_order_number();?> — grazie!</p>
							</div>
						</div>
						<div class="thankyou--details--item">
							<span class="icon-indirizzo"></span>
							<div class="thankyou--details--text">
								<h3 class="thankyou--details--title">Indirizzo di consegna</h3>
								<p class="thankyou--details--info"><?php echo $order->get_shipping_address_1() . '<br/>' . $order->get_shipping_postcode() . ' ' . $order->get_shipping_city();?></p>
							</div>
						</div>
						<div class="thankyou--details--item">
							<span class="icon-totale"></span>
							<div class="thankyou--details--text">
								<h3 class="thankyou--details--title">Totale</h3>
								<p class="thankyou--details--info">Hai pagato <?php echo $order->get_formatted_order_total();?>
									<br/>
									Tramite <?php echo wp_kses_post( $order->get_payment_method_title() ); ?>
								</p>
							</div>
						</div>
						<div class="thankyou--details--item">

								<?php $items = $order->get_items();
											$category_in_order = false;
											foreach ( $items as $item ) {
									    $product_id = $item['product_id'];
												//Controlla se tra i prodotti c'è una box
										    if ( has_term( 'box', 'product_cat', $product_id ) ) {
										        $category_in_order = true;
										        break;
										    }
											}
											if ( $category_in_order ) {
												echo '<span class="icon-prossimo-pagamento"></span>';
												echo '<div class="thankyou--details--text">';
												echo '<h3 class="thankyou--details--title">Prossimo pagamento</h3>';
												echo '<p class="thankyou--details--info">' .$weekday_it . ', ' . $next_week_day . ' ' . $month_it . ' ' . $next_week_year .'</p>';
												echo '</div>';
											} else {
												echo '<span class="icon-email"></span>';
												echo '<div class="thankyou--details--text">';
												echo '<h3 class="thankyou--details--title">Controlla la tua mail</h3>';
												echo $order->get_billing_email();
												echo '</div>';
											} ?>


						</div>
						<div class="thankyou--details--item buttons">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>bacheca" title="Vai alla tua bacheca" class="btn btn-primary">
								Vai alla tua bacheca
							</a>
						</div>
					</div>
				</div>
				<div class="thankyou--image">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/elements/thank-you.svg" />
				</div>
			</div>

		<?php endif; ?>
		<div class="thankyou--messages">
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		</div>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
