/* global jQuery, wc_install_notice */

( function ( wp, $, l10n ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	$( function () {
		$( document ).on( 'click', '.wc-install-notice-nux .notice-dismiss', function () {
			$.ajax( {
				type: 'POST',
				url: l10n.ajaxurl,
				data: {
					nonce: l10n.nonce,
					action: 'wc_install_notice_dismiss_notice',
				},
				dataType: 'json',
			} );
		} );

		$( document ).on( 'click', '.wc-install-notice', function ( event ) {
			const $button = $( event.target );

			if ( $button.hasClass( 'activate-now' ) ) {
				return true;
			}

			event.preventDefault();

			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
				wp.updates.requestFilesystemCredentials( event );

				$( document ).on( 'credential-modal-cancel', function () {
					const $message = $( '.wc-install-notice.updating-message' );

					$message.removeClass( 'updating-message' ).text( wp.updates.l10n.installNow );

					wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
				} );
			}

			wp.updates.installPlugin( {
				slug: $button.data( 'slug' ),
			} );
		} );
	} );
} )( window.wp, jQuery, wc_install_notice );
