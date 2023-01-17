<?php get_header(); ?>


<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post();?>

        <div class="single-article--flex">

        <div class="single-article">
        <div class="container-xsmall">

        <div class="single--header">
          <div class="categories-list">
            <?php
            $category = get_the_category();
            $first_category = $category[0];
            echo sprintf( '<a href="%s">%s</a>', get_category_link( $first_category ), $first_category->name );
            ?>
          </div>
    			<h1><?php the_title(); ?></h1>
        </div>

        <div class="single--content">
  				<?php the_content(); ?>
        </div>


      </div>
      </div>
      </div>


<?php $related_products = get_field('prodotti_correlati');

if( $related_products ): ?>
<section class="products-carousel--container">
  <div class="products-carousel--intro">
    <h2 class="products-carousel--title">Forse ti è venuta fame.</h2>
  </div>
  <div class="products-carousel">
    <?php foreach( $related_products as $related_product ):
        setup_postdata(  $related_product );
        $permalink = get_permalink( $related_product->ID );
        $title = get_the_title( $related_product->ID );

        $thumb_id = get_post_thumbnail_id($related_product->ID);
        $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
        $thumb_url = $thumb_url_array[0];
        ?>

        <article class="product-box">
          <a href="<?php the_permalink($related_product->ID); ?>" class="product-box--link" title="<?php echo esc_html( $title ); ?>">
            <img src="<?php echo $thumb_url; ?>" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
          </a>
          <div class="product-box--text">
            <div class="product-box--text--top">
              <h2 class="product-box--title"><?php echo esc_html( $title ); ?></h2>
              <div class="product-box--price--flex">
                <?php if ( $product->has_weight($related_product->ID) ) {
                	echo '<p class="product-box--description product-info--quantity">' .  $product->get_weight($related_product->ID) . ' kg — </p>';
                } ?>
                <div class="product-box--price">
                  <?php echo $product->get_price_html($related_product->ID); ?>
                </div>
              </div>
              <a href="<?php the_permalink($related_product->ID); ?>" class="btn btn-primary btn-small product-box--button" title="<?php echo get_the_title($related_product->ID); ?>">Scopri di più</a>
            </div>
          </div>
        </article>

    <?php endforeach; ?>
  </div>
</section>
<?php endif;  ?>



<?php endwhile ?>
<?php endif; //end post?>

<?php get_footer();
