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
?>

<section style="display:none;" class="wb-section color-white" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">
	<div class="wb-section--wide wb-section--container" style="background-image:url(<?php echo wp_get_attachment_image_url( $product->get_image_id(), '' ); ?>)">
		<div class="wb-section--content--sx">

		 <?php echo the_title('<h3 class="wb-section--content--title">', '</h3>'); ?></h3>
		 <?php
		 $short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt ); if ($short_description ) {
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

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
<div class="the-box-page">
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
</div>
</div><!-- fine div.product-top in product-image.php -->

<?php get_template_part( 'global-elements/home', 'sections' ); ?>

<?php
//Campi Box
$box_1_title = get_field('box_1_title');
$box_1_text = get_field('box_1_text');
$box_1_image = get_field('box_1_image');

$box_2_title = get_field('box_2_title');
$box_2_text = get_field('box_2_text');
$box_2_image = get_field('box_2_image');

$box_3_title = get_field('box_3_title');
$box_3_text = get_field('box_3_text');
$box_3_image = get_field('box_3_image');

$box_4_title = get_field('box_4_title');
$box_4_text = get_field('box_4_text');
$box_4_image = get_field('box_4_image');

?>

<section id="go-products" class="box-types">
	<div class="box-types--flex">
		<div class="box-types--item vegana" style="background-image:url(<?php echo $box_1_image;?>);">

			<?php if($box_1_title): ?>
					<h3 class="box-types--title"><?php echo $box_1_title; ?></h3>
				<?php else: ?>
				<h3 class="box-types--title">Vegana</h3>
			<?php endif;?>
			<?php if($box_1_text): ?>
				<?php echo $box_1_text; ?>
			<?php else: ?>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Tisane con ingredienti naturali</li>
				<li><span class="icon-check"></span>Amore per gli animali</li>
			</ul>
			<?php endif;?>
			<div class="box-types--buttons">
				<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=47250'); ?>" class="btn btn-primary btn-small">Provala ora</a>
			</div>
		</div>
		<div class="box-types--item vegetariana" style="background-image:url(<?php echo $box_2_image;?>);">

			<?php if($box_2_title): ?>
					<h3 class="box-types--title"><?php echo $box_2_title; ?></h3>
				<?php else: ?>
					<h3 class="box-types--title">Vegetariana</h3>
			<?php endif;?>
			<?php if($box_2_text): ?>
				<?php echo $box_2_text; ?>
			<?php else: ?>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Latticini da allevamenti sostenibili</li>
				<li><span class="icon-check"></span>Uova da galline felici</li>
			</ul>
			<?php endif;?>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=47251'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
		<div class="box-types--item pescetariana" style="background-image:url(<?php echo $box_3_image;?>);">

			<?php if($box_3_title): ?>
					<h3 class="box-types--title"><?php echo $box_3_title; ?></h3>
				<?php else: ?>
					<h3 class="box-types--title">Pescetariana</h3>
			<?php endif;?>

			<?php if($box_3_text): ?>
				<?php echo $box_3_text; ?>
			<?php else: ?>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Uova e latticini da animali felici</li>
				<li><span class="icon-check"></span>Pesce allevato senza antibiotici</li>
			</ul>
			<?php endif;?>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=47252'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
		<div class="box-types--item onnivora" style="background-image:url(<?php echo $box_4_image;?>);">

			<?php if($box_4_title): ?>
					<h3 class="box-types--title"><?php echo $box_4_title; ?></h3>
				<?php else: ?>
					<h3 class="box-types--title">Onnivora</h3>
			<?php endif;?>
			<?php if($box_4_text): ?>
				<?php echo $box_4_text; ?>
			<?php else: ?>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Uova e latticini da animali felici</li>
				<li><span class="icon-check"></span>Carne da allevamenti sostenibili</li>
			</ul>
			<?php endif;?>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=47253'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
	</div>

	<div class="box-types--claim">
		Prova Agrispesa — Arriva ogni 15 giorni — Disdici quando vuoi
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
