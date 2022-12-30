jQuery(function($){ // use jQuery code inside this to avoid "$ is not defined" error

	$('.misha_loadmore').click(function(){

		var button = $(this),
		    data = {
			'action': 'loadmore',
			'query': misha_loadmore_params.posts, // that's how we get params from wp_localize_script() function
			'page' : misha_loadmore_params.current_page
		};

		$.ajax({ // you can also use $.post here
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : data,
			type : 'POST',
			beforeSend : function ( xhr ) {
				button.text('Carico...'); // change the button text, you can also add a preloader image
			},
			success : function( data ){
				if( data ) {
					$('.append-posts').append(data);
					button.text( 'Carica altri' );

					misha_loadmore_params.current_page++;

					if ( misha_loadmore_params.current_page == misha_loadmore_params.max_page )
						button.remove(); // if last page, remove the button

					// you can also fire the "post-load" event here if you use a plugin that requires it
					// $( document.body ).trigger( 'post-load' );
				} else {
					button.remove(); // if no data, remove the button as well
				}
			}
		});
	});


	$('.misha_loadmore_home').click(function(){

		var button = $(this),
		    data = {
			'action': 'loadmore',
			'query': misha_loadmore_params.posts_home, // that's how we get params from wp_localize_script() function
			'page' : misha_loadmore_params.current_page
		};

		$.ajax({ // you can also use $.post here
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : data,
			type : 'POST',
			beforeSend : function ( xhr ) {
				button.text('Carico...'); // change the button text, you can also add a preloader image
			},
			success : function( data ){

				if( data ) {
					$('.append-posts').append(data).fadeIn(2000);
					button.text( 'Carica altri' );

					misha_loadmore_params.current_page++;

					if ( misha_loadmore_params.current_page == misha_loadmore_params.max_page_home )
						button.remove(); // if last page, remove the button

					// you can also fire the "post-load" event here if you use a plugin that requires it
					// $( document.body ).trigger( 'post-load' );
				} else {
					button.remove(); // if no data, remove the button as well
				}
			}
		});
	});

});
