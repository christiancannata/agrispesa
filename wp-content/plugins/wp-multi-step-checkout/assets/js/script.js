jQuery(window).ready(function($){
    "use strict";

	var wpmc_checkout = {
		$tabs			: $( '.wpmc-tab-item' ),
		$sections		: $( '.wpmc-step-item' ),
		$buttons		: $( '.wpmc-nav-button' ),
		$checkout_form	: $( 'form.woocommerce-checkout' ),
		$coupon_form	: $( '#checkout_coupon' ),
		$before_form	: $( '#woocommerce_before_checkout_form' ),
		current_step	: $( 'ul.wpmc-tabs-list' ).data( 'current-title' ),

		init: function() {
			var self = this;

			// add the "wpmc_switch_tab" trigger
			$( '.woocommerce-checkout' ).on( 'wpmc_switch_tab', function( event, theIndex) {
				self.switch_tab( theIndex );
			});

			$( '.wpmc-step-item:first' ).addClass( 'current' );

			// Click on "next" button
			$( '#wpmc-next, #wpmc-skip-login').on( 'click', function() {
				self.switch_tab( self.current_index() + 1);
			});

			// Click on "previous" button
			$( '#wpmc-prev' ).on( 'click', function() {
				self.switch_tab( self.current_index() - 1);
			});

			// After submit, switch tabs where the invalid fields are
			$( document ).on( 'checkout_error', function() {

				if ( ! $( '#createaccount' ).is( ':checked') ) {
					$( '#account_password_field, #account_username_field' ).removeClass( 'woocommerce-invalid-required-field' );
				}

				if ( ! $( '#ship-to-different-address-checkbox' ).is( ':checked' ) ) {
					$( '.woocommerce-shipping-fields__field-wrapper p' ).removeClass( 'woocommerce-invalid-required-field' );
				}

				var section_class = $( '.woocommerce-invalid-required-field' ).closest( '.wpmc-step-item' ).attr( 'class' );

				$( '.wpmc-step-item' ).each( function( i ) {
					if ( $( this ).attr( 'class' ) === section_class ) {
						self.switch_tab(i)
					}
				})
			});


			// Compatibility with Super Socializer
			if ( $( '.the_champ_sharing_container' ).length > 0 ) {
				$( '.the_champ_sharing_container' ).insertAfter( $( this ).parent().find( '#checkout_coupon' ) );
			}

			// Prevent form submission on Enter
			$( '.woocommerce-checkout' ).on( 'keydown', function( e ) {
				if ( e.which === 13 && ! $('.woocommerce-checkout textarea').is(':focus') ) {
					e.preventDefault();
					return false;
				}
			});

			// "Back to Cart" button
			$( '#wpmc-back-to-cart' ).on( 'click', function() {
				window.location.href = $( this ).data( 'href' ); 
			});

			// Switch tabs with <- and -> keyboard arrows
			if ( WPMC.keyboard_nav === '1' ) {
				$( document ).on( 'keydown', function ( e ) {
				  var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
				  if ( key === 39 ) {
					  self.switch_tab( self.current_index() + 1 );
				  }
				  if ( key === 37 ) {
					  self.switch_tab( self.current_index() - 1 );
				  }
				});
			}

			// Change tab if the hash #step-0 is present in the URL
			if ( typeof window.location.hash != 'undefined' && window.location.hash ) {
				changeTabOnHash( window.location.hash );
			}
			$( window ).on( 'hashchange', function() { 
				changeTabOnHash( window.location.hash ) 
			} ); 
			function changeTabOnHash( hash ) {
				if ( /step-[0-9]/.test( hash ) ) {
					var step = hash.match( /step-([0-9])/ )[1];
					self.switch_tab( step );
				}
			}

			// select2
			if ( typeof $(this).selectWoo !== 'undefined' ) {
				self.wc_country_select_select2();
				$( document.body ).on( 'country_to_state_changed', function() {
					self.wc_country_select_select2();
				});
			}

			// customizations
			self.customizations();

		},
		current_index: function() {

			return this.$sections.index( this.$sections.filter( '.current' ) );
		},
		scroll_top: function() {
			// scroll to top
			if ( $( '.wpmc-tabs-wrapper' ).length === 0 ) {
				return;
			}

			var diff = $( '.wpmc-tabs-wrapper' ).offset().top - $( window ).scrollTop();
			var scroll_offset = 70;
			if ( typeof WPMC.scroll_top !== 'undefined' ) {
				scroll_offset = WPMC.scroll_top;
			}
			if ( diff < -40 ) {
				$( 'html, body' ).animate({
					scrollTop: $( '.wpmc-tabs-wrapper' ).offset().top - scroll_offset, 
				}, 800);
			}
		},
		switch_tab: function( theIndex ) {
			var self = this;

			$( '.woocommerce-checkout' ).trigger( 'wpmc_before_switching_tab' );

			if ( theIndex < 0 || theIndex > this.$sections.length - 1 ) {
				return false;
			}

			this.scroll_top(); 
		
			$( 'html, body' ).promise().done( function() {

				self.$tabs.removeClass( 'previous' ).filter( '.current' ).addClass( 'previous' );
				self.$sections.removeClass( 'previous' ).filter( '.current' ).addClass( 'previous' );
				$( '.woocommerce-NoticeGroup-checkout:not(wpmc-error)' ).show();

				// Change the tab
				self.$tabs.removeClass( 'current' );
				self.$tabs.eq( theIndex ).addClass( 'current' );
				self.current_step = self.$tabs.eq( theIndex ).data( 'step-title' );
				$( '.wpmc-tabs-list' ).data( 'current-title', self.current_step );
			 
				// Change the section
				self.$sections.removeClass( 'current' );
				self.$sections.eq( theIndex ).addClass( 'current' );

				// Which buttons to show?
				self.$buttons.removeClass( 'current' );
				self.$coupon_form.hide();
				self.$before_form.hide();

				// Show "next" button 
				if ( theIndex < self.$sections.length - 1 ) {
					$( '#wpmc-next' ).addClass( 'current' );
				}

				// Remove errors from previous steps
				if ( typeof $( '.woocommerce-NoticeGroup-checkout' ).data( 'for-step' ) !== 'undefined' && $( '.woocommerce-NoticeGroup-checkout' ).data( 'for-step' ) !== self.current_step ) {
					$( '.woocommerce-NoticeGroup-checkout' ).remove();
				}

				// Show "skip login" button
				if ( theIndex === 0 && $( '.wpmc-step-login' ).length > 0 ) {
					$( '#wpmc-skip-login').addClass( 'current' );
					$( '#wpmc-next' ).removeClass( 'current' );
					$( '.woocommerce-NoticeGroup-checkout:not(wpmc-error)' ).hide();
				}

				// Last section
				if ( theIndex === self.$sections.length - 1 ) {
					$( '#wpmc-prev' ).addClass( 'current' );
					$( '#wpmc-submit' ).addClass( 'current' );
					self.$checkout_form.removeClass( 'processing' ).unblock();
				}

				// Show "previous" button 
				if ( theIndex != 0 ) {
					$( '#wpmc-prev' ).addClass( 'current' );
				}


				if ( $( '.wpmc-step-review.current' ).length > 0 ) {
					self.$coupon_form.show();
				}

				if ( $( '.wpmc-' + self.$before_form.data( 'step' ) + '.current' ).length > 0 ) {
					self.$before_form.show();
				}

				$( '.woocommerce-checkout' ).trigger( 'wpmc_after_switching_tab' );
			});
		},
		wc_country_select_select2: function() {
			var self = this;
			$( 'select.country_select:not(visible), select.state_select:not(visible)' ).each( function() {
				var $this = $( this );

				var select2_args = $.extend({
					placeholder: $this.attr( 'data-placeholder' ) || $this.attr( 'placeholder' ) || '',
					label: $this.attr( 'data-label' ) || null,
					width: '100%'
				}, self.getEnhancedSelectFormatString() );

				$( this )
					.on( 'select2:select', function() {
						$( this ).trigger( 'focus' ); // Maintain focus after select https://github.com/select2/select2/issues/4384
					} )
					.selectWoo( select2_args );
			});
		},
		getEnhancedSelectFormatString: function() {
			return {
				'language': {
					errorLoading: function() {
						// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
						return wc_country_select_params.i18n_searching;
					},
					inputTooLong: function( args ) {
						var overChars = args.input.length - args.maximum;

						if ( 1 === overChars ) {
							return wc_country_select_params.i18n_input_too_long_1;
						}

						return wc_country_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
					},
					inputTooShort: function( args ) {
						var remainingChars = args.minimum - args.input.length;

						if ( 1 === remainingChars ) {
							return wc_country_select_params.i18n_input_too_short_1;
						}

						return wc_country_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
					},
					loadingMore: function() {
						return wc_country_select_params.i18n_load_more;
					},
					maximumSelected: function( args ) {
						if ( args.maximum === 1 ) {
							return wc_country_select_params.i18n_selection_too_long_1;
						}

						return wc_country_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
					},
					noResults: function() {
						return wc_country_select_params.i18n_no_matches;
					},
					searching: function() {
						return wc_country_select_params.i18n_searching;
					}
				}
			};
		},
		customizations: function() {
			if ( WPMC.hide_last_prev === '1' ) {
				$('.woocommerce-checkout').on('wpmc_after_switching_tab', function() {
					$('#wpmc-prev').css('visibility', $('.wpmc-tabs-wrapper li.wpmc-tab-item:last-child').hasClass('current') ? 'hidden' : 'visible');
				});
			}

			if ( WPMC.hide_last_back_to_cart === '1' ) {
				$('.woocommerce-checkout').on('wpmc_after_switching_tab', function() {
					$('#wpmc-back-to-cart').css('visibility', $('.wpmc-tabs-wrapper li.wpmc-tab-item:last-child').hasClass('current') ? 'hidden' : 'visible');
				});
			}

			if ( WPMC.skip_login_above_form === '1' ) {
				if ( $('.wpmc-login.current').length === 1 ) {
					$('.wpmc-steps-wrapper').before( $('.wpmc-nav-wrapper'));
				}
				$('.woocommerce-checkout').on('wpmc_after_switching_tab', function() {
					if ( $('.wpmc-login.current').length === 1 ) {
						$('.wpmc-steps-wrapper').before( $('.wpmc-nav-wrapper'));
					} else {
						$('.wpmc-steps-wrapper').after( $('.wpmc-nav-wrapper'));
					}
				});
			}

			if ( WPMC.skip_login_next_to_login_button === '1' ) {
				$('.woocommerce-form-login__submit').after($('#wpmc-skip-login'));
			  	$('#wpmc-skip-login').css('margin-left', '7px');				
			}
		}
	}
	wpmc_checkout.init();
});
