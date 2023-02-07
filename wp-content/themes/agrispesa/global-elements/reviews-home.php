<?php
$page = get_page_by_path('recensioni');
$post_id = $page->ID;

if( have_rows('lista_recensioni' , $post_id) ):
  $i = 0;
  echo '<div class="reviews-home" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">';
  echo '<div class="container-pg">';
  echo '<div class="reviews--slider">';
    while( have_rows('lista_recensioni' , $post_id) ) : the_row();
    $review_name = get_sub_field('review_name' , $post_id);
    $review_quote = get_sub_field('review_quote' , $post_id);
    $review_title = get_sub_field('review_title' , $post_id);
    $review_star = get_sub_field('review_stars' , $post_id); //Valore immagine

    $i++;
    if( $i > 4 ) {
		    break;
		}
    ?>

<div class="review--item">

  <div class="review--text">
    <div class="review-page--stars">
      <span class="icon-star <?php if($review_star == 1 || $review_star > 1 ) { echo 'yellow'; } ?>"></span>
      <span class="icon-star <?php if($review_star > 1) { echo 'yellow'; } ?>"></span>
      <span class="icon-star <?php if($review_star > 2) { echo 'yellow'; } ?>"></span>
      <span class="icon-star <?php if($review_star > 3) { echo 'yellow'; } ?>"></span>
      <span class="icon-star <?php if($review_star > 4) { echo 'yellow'; } ?>"></span>
    </div>

    <div class="review--description"><p><?php echo $review_quote;?></p></div>
    <p class="review--name"><?php echo $review_name;?></p>
  </div>
</div>


<?php endwhile;
    echo '</div>';
    echo '</div>';
    echo '</div>';
endif; ?>
