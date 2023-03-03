/**
 * Wider Admin Menu
 */
jQuery( document ).ready(function($){

	var wpVersion = $('input[name="wp_version"]').val();

	if( undefined === wpVersion )
		return;

	wpArr = wpVersion.split('.');

	var target = $("#wpmwam_width");
	var max = target.data('max'),
		min = target.data('min'),
		step = target.data('step'),
		value = parseInt(target.val(), 10);

	$("#wpmwam_slider").slider({
		orientation: "horizontal",
		step: step,
		min: min,
		max: max,
		value: value,
		slide: function( event, ui ) {
			target.val( ui.value ).trigger( 'change' );
		},
		stop: function(){
			update();
		}
	});


	// Some browsers save form data despite it being set on form generation.
	// So instead, let's get the current setting from "current" element,
	// then populate input field with it. (f u browser)
	var currentWidth = parseInt( $('#wpmwam_current').html() );
	target.val( currentWidth );

	function update(){
		var wider = parseInt( target.val() ),
				widerpx = wider + 'px';
		var wider1 = wider + 1,
				wider1px = wider1 + 'px';
		var wider2 = wider + 20,
				wider2px = wider2 + 'px';
		// or to lock content at the widest point:
		// var wider2px = '320px';

		if( parseInt( wpArr[0] ) >= 4 && parseInt( wpArr[1] ) >= 0 ) {  // 4.0+

			$('#wpcontent, #wpfooter').css('margin-left', widerpx);

			$('#adminmenuback, #adminmenuwrap, #adminmenu, #adminmenu .wp-submenu').width(widerpx);

			$('#adminmenu .wp-submenu')
					.not('.wp-has-current-submenu.wp-menu-open .wp-submenu')
					.css('left', widerpx);

			$('#adminmenu .wp-not-current-submenu .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu').css('min-width', widerpx);

			$("#qm").css("margin-left", widerpx);

		} else if( parseInt( wpArr[0] ) >= 3 && parseInt( wpArr[1] ) >= 8 ) {  // 3.8+

			$('#wpcontent, #wpfooter').css('margin-left', wider2px);

			$('#adminmenuback, #adminmenuwrap, #adminmenu, #adminmenu .wp-submenu').width(widerpx);

			$('#adminmenu .wp-submenu')
					.not('.wp-has-current-submenu.wp-menu-open .wp-submenu')
					.css('left', widerpx);

			$('#adminmenu .wp-not-current-submenu .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu').css('min-width', widerpx);

		} else {  // 3.3 - 3.7.1

			$('#wpcontent, #footer').css('margin-left', wider2px);

			$('#adminmenuback, #adminmenuwrap, #adminmenu, #adminmenu .wp-submenu, #adminmenu .wp-submenu-wrap, .folded #adminmenu .wp-has-current-submenu .wp-submenu')
					.width(widerpx);

			$('#adminmenu .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu, .wp-has-submenu.wp-not-current-submenu .wp-submenu')
					.not('.wp-has-current-submenu.wp-menu-open .wp-submenu')
					.css('left', wider1px);

			$('.wp-menu-arrow').css({
				'-moz-transform':    'translate( ' + wider1px + ' )',
				'-webkit-transform': 'translate( ' + wider1px + ' )',
				'-o-transform':      'translate( ' + wider1px + ' )',
				'-ms-transform':     'translate( ' + wider1px + ' )',
				'transform':         'translate( ' + wider1px + ' )'
			});

			$('#adminmenu li.wp-not-current-submenu .wp-menu-arrow').css({
				'-moz-transform':    'translate( ' + widerpx + ' )',
				'-webkit-transform': 'translate( ' + widerpx + ' )',
				'-o-transform':      'translate( ' + widerpx + ' )',
				'-ms-transform':     'translate( ' + widerpx + ' )',
				'transform':         'translate( ' + widerpx + ' )'
			});

		}

	}

	// reset buttons
	$('#reset-current').click(function(e){
		$("#wpmwam_slider").slider({
			value:currentWidth
		});
		target.val(currentWidth);
		update();
	});
	$('#reset-default').click(function(e){
		$("#wpmwam_slider").slider({
			value:160
		});
		target.val(160);
		update();
	});

});
