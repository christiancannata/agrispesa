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

<section class="agri-values" data-aos="fade-in" data-aos-duration="600" data-aos-delay="50" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/box/cosa-contiene.jpg);">
	<h3 class="agri-values--title" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">Cosa c'è nella scatola Agrispesa?</h3>
	<div class="agri-values--flex">

		<div class="agri-values--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">
			<?php get_template_part( 'global-elements/icon', 'verdura' ); ?>
			<p class="agri-values--descr">
				Verdura<br/>
				<span>appena raccolta</span>
			</p>
		</div>
		<div class="agri-values--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
			<?php get_template_part( 'global-elements/icon', 'frutta' ); ?>
			<p class="agri-values--descr">
				Frutta fresca<br/>
				<span>di stagione</span>
			</p>
		</div>
		<div class="agri-values--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
			<?php get_template_part( 'global-elements/icon', 'latticini' ); ?>
			<p class="agri-values--descr">
				Uova e latticini<br/>
				<span>da animali felici</span>
			</p>
		</div>
		<div class="agri-values--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
			<?php get_template_part( 'global-elements/icon', 'carne-pesce' ); ?>
			<p class="agri-values--descr">
				Carne e pesce<br/>
				<span>allevati con rispetto</span>
			</p>
		</div>
		<div class="agri-values--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="250">
			<?php get_template_part( 'global-elements/icon', 'pane-cereali' ); ?>
			<p class="agri-values--descr">
				Pane e cereali<br/>
				<span>da farine italiane</span>
			</p>
		</div>

	</div>
	<div class="agri-values--button" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">
		<a href="#go-meet" class="btn btn-primary scroll-to" title="Come puoi aiutarlo tu?">Come puoi aiutarlo tu?</a>
	</div>

</section>

<section class="box-types">
	<div class="box-types--flex">
		<div class="box-types--item vegana" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/box/vegana.jpg)">
			<h3 class="box-types--title">Vegana</h3>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Tisane con ingredienti naturali</li>
				<li><span class="icon-check"></span>Amore per gli animali</li>
			</ul>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=18995'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
		<div class="box-types--item vegetariana" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/box/vegetariana.jpg)">
			<h3 class="box-types--title">Vegetariana</h3>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Latticini da allevamenti sostenibili</li>
				<li><span class="icon-check"></span>Uova da galline felici</li>
			</ul>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=18996'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
		<div class="box-types--item pescetariana" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/box/pescetariana.jpg)">
			<h3 class="box-types--title">Pescetariana</h3>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Uova e latticini da animali felici</li>
				<li><span class="icon-check"></span>Pesce allevato senza antibiotici</li>
			</ul>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=18997'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
		<div class="box-types--item onnivora" style="background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/box/onnivora.jpg)">
			<h3 class="box-types--title">Onnivora</h3>
			<ul class="box-types--list">
				<li><span class="icon-check"></span>Prodotti di agricoltura contadina</li>
				<li><span class="icon-check"></span>Uova e latticini da animali felici</li>
				<li><span class="icon-check"></span>Carne da allevamenti sostenibili</li>
			</ul>
			<a href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart=18998'); ?>" class="btn btn-primary btn-small">Provala ora</a>
		</div>
	</div>

	<div class="box-types--claim">
		Prova la scatola piccola. Oppure <a href="" title="Seleziona la dimensione">seleziona la dimensione che preferisci</a>. — Disdici quando vuoi.
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
