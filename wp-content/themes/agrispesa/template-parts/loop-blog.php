<?php

$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
$thumb_url = $thumb_url_array[0];
?>
<article class="magazine-post">
  <div class="magazine-post--flex">
    <a href="<?php the_permalink(); ?>" class="magazine-post--link" title="<?php echo the_title(); ?>">
      <span style="background-image: url(<?php echo $thumb_url;?>);"></span>
    </a>
    <div class="magazine-post--text">
      <div class="magazine-post--text--top">
        <div class="categories-list">
          <?php
          $category = get_the_category();
          $first_category = $category[0];
          echo sprintf( '<a href="%s">%s</a>', get_category_link( $first_category ), $first_category->name );
          ?>
        </div>
        <h2 class="magazine-post--title">
          <a href="<?php the_permalink(); ?>" class="magazine-post--titlelink" title="<?php echo the_title(); ?>">
            <?php echo the_title(); ?>
          </a>
        </h2>
      </div>
      <div class="magazine-post--text--bottom">
        <div class="magazine-post--data">
            <?php
            $post_date = get_the_date( 'j F Y' );
            echo '<p>'.$post_date.'</p>';?>
        </div>
      </div>
    </div>
  </div>
</article>
