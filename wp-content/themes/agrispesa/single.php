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
<section class="section-hero">
  <div class="section-hero--container">
      <h4 class="section-hero--subtitle">
        Forse ti è venuta fame.
      </h4>
  </div>
</section>
<section class="products-carousel--container">
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

<section class="section-hero">
  <div class="section-hero--container">
      <h4 class="section-hero--subtitle">
        Storie di agricoltura.
      </h4>
  </div>
</section>

<section class="magazine">
  <div class="magazine--slider">
  <?php /* Blog */ ?>
  <?php
  $args = array(
          'posts_per_page' => '3',
          'category__in' => wp_get_post_categories($post->ID),
          'post_type' => 'post',
          'orderby' => 'date',
          'order' => 'DESC'
      );
  $query = new WP_Query( $args );
  if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

  <?php get_template_part( 'template-parts/loop', 'blog' ); ?>

  <?php endwhile;
    wp_reset_postdata();
  endif; ?>
  </div>
</section>

<div class="clearfix"></div>

<section class="big-search">
  <div class="big-search--content">
    <div class="big-search--text">
      <h3 class="big-search--title">Cerca i tuoi prodotti preferiti.</h3>
    </div>
    <?php get_search_form() ?>
  </div>
</section>

<?php get_template_part( 'global-elements/all', 'categories' ); ?>

<?php get_footer();
