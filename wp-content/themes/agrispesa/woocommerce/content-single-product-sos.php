<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
global $post;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
?>
<section class="wb-section color-white" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">
	<div class="wb-section--wide wb-section--container" style="background-image:url(<?php echo wp_get_attachment_image_url( $product->get_image_id(), '' ); ?>)">
		<div class="wb-section--content--sx">

		 <?php echo the_title('<h3 class="wb-section--content--title">', '</h3>'); ?></h3>
		 <?php if ($short_description ) {
			 echo '<div class="wb-section--content--descr wide">';
			 echo $short_description;
			 echo '</div>';
		 }?>

		 <div class="wb-section--content--buttons">
			 <div class="wb-section--content--buttons--flex">
				 <div class="wb-section--content--price"><?php echo $product->get_price_html();?></div>
					<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart='.$product->get_id()); ?>" class="btn btn-primary" title="Dona una spesa">Dona una spesa</a>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="manifesto--hero">
	<div class="manifesto--container">

			<div class="manifesto--hero--title">

			</div>

			<div class="manifesto--hero--subtitle">
				<p>Siamo le scelte che facciamo.<br/>
Siamo il modo in cui trattiamo il pianeta.<br/>
Siamo la scelta di ciò che acquistiamo e mangiamo.<br/>
Siamo i rapporti che costruiamo l'uno con l'altro.<br/>
<br/>
Siamo le nostre scelte e abbiamo un potere.<br/>
Ogni nostra scelta, infatti, modifica la società nella quale viviamo.<br/>
<br/>
Oggi ci siamo scoperti fragili a tanti livelli e, sia individualmente, sia come società, siamo chiamati a confrontarci con questa fragilità.<br/>
Agrispesa sceglie di non rimanere indifferente e lancia una campagna di solidarietà per dare un aiuto concreto alle famiglie in difficoltà economica.<br/>
Per questo nasce la spesa SOSpesa.</p>
			</div>

	</div>
</section>

<section class="sos-numbers">
	<div class="sos-numbers--container">
		<span class="icon-heart"></span>
		<div class="sos-numbers--top">
			<h3 class="sos-numbers--title">Siamo le scelte<br/>che facciamo.</h3>
			<p class="sos-numbers--subtitle">
				Ogni nostra scelta, infatti, modifica la società nella quale viviamo. Oggi ci siamo scoperti fragili a tanti livelli e, sia individualmente, sia come società, siamo chiamati a confrontarci con questa fragilità.
				<br/><br/>
				<strong>Scegli di aiutare una famiglia in difficoltà.</strong>
			</p>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart='.$product->get_id()); ?>" class="btn btn-primary" title="Dona una spesa">Dona una spesa</a>
		</div>
		<div class="sos-numbers--bottom">
			<div class="sos-numbers--text">
				<span class="sos-numbers--number">406</span>
				<span class="sos-numbers--label">Le famiglie che hanno ricevuto la SOSpesa.</span>
			</div>
		</div>
	</div>
</section>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );
	?>



	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );
		?>
	</div>

</div><!-- fine div.product-top in product-image.php -->

<section id="read-description" class="product-bottom">

	<div class="sec-home sec-full bg-panna line-orange">
		<div class="container-xsmall">
			<div class="sec-product-description">
				<?php echo the_content(); ?>
			</div>
		</div>
	</div>

</section>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
