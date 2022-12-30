jQuery(function($){

	/*
	 * Load More
	 */
	$('#misha_loadmore').click(function(){

		$.ajax({
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'loadmorebutton', // the parameter for admin-ajax.php
				'query': misha_loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : misha_loadmore_params.current_page // current page
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				$('#misha_loadmore').text('Loading...'); // some type of preloader
			},
			success : function( posts ){
				if( posts ) {

					$('#misha_loadmore').text( 'More posts' );
					$('#misha_posts_wrap').append( posts ); // insert new posts
					misha_loadmore_params.current_page++;

					if ( misha_loadmore_params.current_page == misha_loadmore_params.max_page )
						$('#misha_loadmore').hide(); // if last page, HIDE the button

				} else {
					$('#misha_loadmore').hide(); // if no data, HIDE the button as well
				}
			}
		});
		return false;
	});
 

});
