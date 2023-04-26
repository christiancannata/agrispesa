<?php
/* Template Name: Landing - Azienda */

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
$agr_landing_product = get_field('agr_landing_product');
$what_faq_category_ID = get_field('agr_landing_faq_category');
$faq_mega_title = get_field('landing_cat_faq_title');
$landing_cat_quote_image = get_field('landing_cat_quote_image');
$agr_landing_coupon = get_field('agr_landing_coupon');
$landing_product_image = get_field('landing_product_image');
$landing_discount_badge = get_field('landing_discount_badge');
$landing_discount_type = get_field('landing_discount_type');

if($agr_landing_coupon){
	$coupon = '&coupon-code='.$agr_landing_coupon;
} else {
	$coupon= "";
}


?>


<div class="wrapper" id="index-wrapper">

	<?php get_template_part( 'global-elements/hero', 'landing-category' ); ?>


	<?php get_template_part( 'global-elements/home', 'sections' ); ?>



<?php if(!get_field('landing_cat_hide_quote')): ?>
	<section class="landing-category--quote">
		<div class="container-pg">
			<div class="landing-category--quote--flex">
				<div class="landing-category--quote--text <?php if(!$landing_cat_quote_image) { echo 'big';}?>" data-aos="fade-up" data-aos-duration="600" data-aos-delay="0">
					<div class="landing-category--quote--text--quote">
						<?php $quote = get_field('landing_cat_quote'); ?>
						<?php if($quote):?>
							<h3><?php echo $quote; ?></h3>
						<?php endif;?>
					</div>
					<p>
						<?php echo the_field('landing_cat_quote_text');?>
					</p>
				</div>
				<?php if($landing_cat_quote_image): ?>
				<div class="landing-category--quote--image" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
					<img src="<?php echo $landing_cat_quote_image;?>" alt="Tutti i vantaggi di Agrispesa" />
				</div>
				<?php endif;?>
			</div>
		</div>
	</section>
	<?php endif;?>




<section id="go-products" class="landing-category--loop" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
	<h3 class="landing-category--loop--title">Prova Agrispesa.</h3>
	<div class="container-big">
			<div class="landing-box">
				<?php if( $agr_landing_product ): ?>

					<article class="landing-box--article">

						<?php if($landing_product_image):?>
 						 <div class="landing-box--image">
 							 <?php if($landing_discount_badge):
								 $type_discount = "";
								 if($landing_discount_type == 'percentage') {
									 $type_discount = "<small>%</small>";
								 } else {
									 $type_discount = "<small>€</small>";
								 }
								 ?>

 								 <span class="landing-discount--badge" data-aos="fade-up" data-aos-duration="800" data-aos-delay="0">
	 								 <span class="landing-discount--content">
										 <?php echo '<small>-</small>' .$landing_discount_badge . $type_discount;?>
											 <span class="landing-discount--discount">DI SCONTO</span>
									 </span>
								 </span>
 							 <?php endif;?>
 							 <img src="<?php echo $landing_product_image;?>" class="landing-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
 						 </div>
 					 <?php endif;?>

					 <div class="landing-box--bottom">

    <?php

		foreach( $agr_landing_product as $post ):
        setup_postdata($post);
				$product = wc_get_product( $post );
				$children = $product->get_children();

				 $attributes = $product->get_variation_attributes();
         $attribute_keys = array_keys( $attributes );
         $available_variations = array( $product->get_available_variations() );


				 foreach ( $attributes as $attribute_name => $options ) :

					 $attr_array = array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product );

						 ?>
							 <tr>
								 <th class="label"><label class="landing-label-var" for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></th>
									 <td class="value">
											 <?php
													 $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
													 wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
											 ?>
									 </td>
							 </tr>
					 <?php endforeach;
					 ?>
							<div class="landing-box--price--flex">
 				        <div class="landing-box--price">
 				          <?php

									foreach($product->get_available_variations() as $variation ){

										if($landing_discount_badge){
										$var_old_price = "";
		 								 $var_price = $variation['display_price'];
		 								 if($landing_discount_type == 'percentage') {
											 	$var_old_price = '<span class="landing-old-price">'.wc_price($var_price). '</span>';
												$var_price = $var_price - ($var_price * ($landing_discount_badge / 100));
												$var_price = wc_price($var_price) . '<span class="week">/settimana</span>';
		 									 //$var_price = "<small>€</small>";
		 								 } else {
											 $var_old_price = '<span class="landing-old-price">'.wc_price($var_price). '</span>';
											 $var_price = $var_price - $landing_discount_badge;
											 $var_price = wc_price($var_price). '<span class="week">/settimana</span>';
		 								 }
									 } else {
										 $var_old_price = "";
										 $var_price = $variation['display_price']. '<span class="week">/settimana</span>';
									 }

				 		        // Variation ID
				 		        $variation_id = $variation['variation_id'];
				 		        // Attributes
				 		        $attributes = array();
				 		        foreach( $variation['attributes'] as $key => $value ){
				 		            $taxonomy = str_replace('attribute_', '', $key );
				 		            $taxonomy_label = get_taxonomy( $taxonomy )->labels->singular_name;
				 		            $term_name = get_term_by( 'slug', $value, $taxonomy )->name;
				 		            $attributes[] = $taxonomy_label.': '.$term_name;
				 		        }
										//print_r($variation['display_price']);
										//print_r($variation_id);
				 		        echo '<span class="change-price-box" data-id="'.$variation_id.'" data-type="'.$variation['attributes']['attribute_pa_tipologia'].'" data-size="'.$variation['attributes']['attribute_pa_dimensione'].'">'.$var_old_price.'<span class="landing-new-price"> '.$var_price.'</span></span>';
				 		    }
								 ?>
 				        </div>
								<div class="landing-box--button">
									<a id="get_url" href="<?php echo esc_url(wc_get_cart_url().'?add-to-cart='.$product->get_id().'&quantity=1&variation_id=18995'); ?>" class="btn btn-primary">Abbonati alla spesa</a>
								</div>
 				      </div>




    <?php endforeach; ?>

    <?php
    // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata(); ?>
	</div>
		</article>
<?php endif; ?>

			</div>
		</div>
	</section>








		<?php if(!get_field('landing_cat_hide_faq' && $what_faq_category_ID)):
			$faq_category = array_column($what_faq_category_ID, 'slug');?>
			<section class="faq">
			  <div class="container-small">
					<?php if($faq_mega_title):?>
			    	<h3 class="faq--title" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0"><?php echo $faq_mega_title;?></h3>
					<?php else: ?>
						<h3 class="faq--title" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">Ci chiedono spesso.</h3>
					<?php endif; ?>
			    <div class="faq--list">
			      <?php //Loop FAQs
			      $args = array(
			      'post_type' => 'faq',
			      'post_status' => 'publish',
			      'posts_per_page' => -1,
			      'order' => 'ASC',
						'tax_query' => array(
                array(
                    'taxonomy'  => 'faq_cats',
                    'terms'     =>  $faq_category,
                    'field'     => 'slug'
                )
            )
			      );

			      $loop = new WP_Query( $args );
			      $i = 1;
			      while ( $loop->have_posts() ) : $loop->the_post();
						$delay = 50 * $i;
						?>

			          <article id="post-<?php the_ID(); ?>" class="faq__item <?php echo 'faq-'.$i; ?>"  data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $delay; ?>">
			            <header class="faq__content">
			              <h2 class="faq__title"><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>" class="faq__link"><span class="faq__icon icon-arrow-down"></span><?php the_title(); ?></a></h2>
			              <div class="faq__description"><?php the_content(); ?></div>
			            </header>
			          </article>
			      <?php $i++; endwhile; wp_reset_postdata(); ?>
			    </div>
			  </div>
			</section>

		<?php endif;?>


</div>

<?php
get_footer();
