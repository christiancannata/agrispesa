jQuery(document).ready(function($){
	$('#jstree').jstree({
		core: {
		    data: {
				url: ajaxurl,
				type: 'POST',
				data: function( node ){
					return {
						action: 'itw_subdirectories',
						_wpnonce: $('[name=_wpnonce]').val(),
						_wp_http_referer: $('[name=_wp_http_referer]').val(),
						folder: node.id
					};
				}
			},
			themes: {
				variant: 'large'
			}
		},
		checkbox: {
			keep_selected_style: false
		},
		plugins: [ 'wholerow', 'checkbox' ]
	});

	$('.convert-all-images, .convert-missing-images').click(function(event){
		event.preventDefault();
		const button = this;
		window.selected_folders = $('#jstree').jstree().get_top_checked();
		if( selected_folders.length ){
			$('#transparency_status_message span').text( transparency_status_message );
			$('#transparency_status_message').show();
			$('#hide-on-convert').hide();
			$('#show-on-convert').prepend('<span>Loading all subdirectories...</span>');
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'itw_get_all_subdirectories',
					_wpnonce: $('[name=_wpnonce]').val(),
					_wp_http_referer: $('[name=_wp_http_referer]').val(),
					folders: selected_folders
				}
			})
			.done(function( response, statusText, xhr ){
				$('#show-on-convert').prepend('<span>Subdirectories loaded successfully.</span><br>');
				window.selected_folders = response;
				convert_old_images( $( button ).hasClass('convert-missing-images') ? 1 : 0 );
			})
			.fail(function( xhr, textStatus ){
				$('#show-on-convert').prepend( '<span>' + xhr.status + '</span><br>' );
				console.log( xhr, textStatus );
			});
		}
	});

	function convert_old_images( only_missing, folder ){
		if( selected_folders.length || folder ){
			var folder = folder || selected_folders.shift();
			$('#show-on-convert').prepend('<div>' + folder + '/ ... </div>');
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'convert_old_images',
					_wpnonce: $('[name=_wpnonce]').val(),
					_wp_http_referer: $('[name=_wp_http_referer]').val(),
					only_missing: only_missing,
					folder: folder
				}
			})
			.done(function( response, statusText, xhr ){
				$('#show-on-convert div').first().append( response );
				convert_old_images( only_missing );
			})
			.fail(function( xhr, textStatus ){
				$('#show-on-convert div').first().append( error_message.replace( '{{ERROR}}', xhr.status ) );
				convert_old_images( 1, folder );
			});
		}else{
			$('#transparency_status_message').hide();
			$('#show-on-convert').prepend('<div>DONE.</div>');
		}
	}
});