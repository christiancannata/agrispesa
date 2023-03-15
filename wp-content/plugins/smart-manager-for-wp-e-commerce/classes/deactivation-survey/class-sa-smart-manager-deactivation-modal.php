<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slug                    = $vars[ 'slug' ];
$confirmation_message    = '';
$reasons                 = $vars['reasons']['default'];
$reasons_list_items_html = '';
$incr                    = 0;

$sa_plugin_name 		 = self::$plugin_name;

$sa_plugin_version = '';
if( is_callable( array( 'Smart_Manager', 'get_version' ) ) ) {
	$sa_plugin_version = Smart_Manager::get_version();
}

foreach ( $reasons as $reason ) {
	$list_item_classes           = 'reason' . ( ! empty( $reason['input_type'] ) ? ' has-input' : '' ) . ( ( isset( $reason[ 'html' ] ) && ( ! empty( $reason[ 'html' ] ) ) ) ? ' has_html' : '' );
	$reason_html                 = ( isset( $reason['html'] ) && ( ! empty( $reason['html'] ) ) ) ? '<div class="reason_html">' . $reason['html'] . '</div>' : '';
	$sa_reason_input_type        = ( isset( $reason['input_type'] ) && ( ! empty( $reason['input_type'] ) ) ) ?  $reason['input_type']  : '';
	$sa_reason_input_placeholder = ( isset( $reason['input_placeholder'] ) && ( ! empty( $reason['input_placeholder'] ) ) ) ?  $reason['input_placeholder']  : '';
	$sa_reason_id                = ( isset( $reason['id'] ) && ( ! empty( $reason['id'] ) ) ) ?  $reason['id'] : '';
	$sa_reason_text              = ( isset( $reason['text'] ) && ( ! empty( $reason['text'] ) ) ) ?  $reason['text']  : '';

	$selected = "";

	$reasons_list_items_html .= '<li class="' . $list_item_classes . '" data-input-type="' . $sa_reason_input_type . '" data-input-placeholder="' . $sa_reason_input_placeholder . '"><label><span><input type="radio" name="selected-reason" value="' . $sa_reason_id . '" ' . $selected . '/></span><span>' . $sa_reason_text . '</span></label>' . $reason_html . '</li>';
	$incr ++;
}
 
?>
<style type="text/css">
	.sa-modal {
		position: fixed;
		overflow: auto;
		height: 100%;
		width: 100%;
		top: 0;
		z-index: 100000;
		display: none;
		background: rgba(0, 0, 0, 0.6)
	}

	.sa-modal .sa-modal-dialog {
		background: transparent;
		position: absolute;
		left: 50%;
		margin-left: -298px;
		padding-bottom: 30px;
		top: -100%;
		z-index: 100001;
		width: 596px
	}

	.sa-modal li.reason.has_html .reason_html {
		display: none;
		border: 1px solid #ddd;
		padding: 4px 6px;
		margin: 6px 0 0 20px;
	}

	.sa-modal li.reason.has_html.li-active .reason_html {
		display: block;
	}

	.sa-modal-deactivation-headline {
		text-align: center;
	}

	.sa-modal-deactivation-second-headline {
		margin: 0 0 1em 0 !important;
	}

	.reason.has-input input[type="radio"] {
		vertical-align: middle;
	}

	@media (max-width: 650px) {
		.sa-modal .sa-modal-dialog {
			margin-left: -50%;
			box-sizing: border-box;
			padding-left: 10px;
			padding-right: 10px;
			width: 100%
		}

		.sa-modal .sa-modal-dialog .sa-modal-panel > h3 > strong {
			font-size: 1.3em
		}

		.sa-modal .sa-modal-dialog li.reason {
			margin-bottom: 10px
		}

		.sa-modal .sa-modal-dialog li.reason .reason-input {
			margin-left: 29px
		}

		.sa-modal .sa-modal-dialog li.reason label {
			display: table
		}

		.sa-modal .sa-modal-dialog li.reason label > span {
			display: table-cell;
			font-size: 1.3em
		}
	}

	.sa-modal.active {
		display: block
	}

	.sa-modal.active:before {
		display: block
	}

	.sa-modal.active .sa-modal-dialog {
		top: 10%
	}

	.sa-modal .sa-modal-body, .sa-modal .sa-modal-footer {
		border: 0;
		background: #fefefe;
		padding: 20px
	}

	.sa-modal .sa-modal-body {
		border-bottom: 0
	}

	.sa-modal .sa-modal-body h2 {
		font-size: 20px
	}

	.sa-modal .sa-modal-body > div {
		margin-top: 10px
	}

	.sa-modal .sa-modal-body > div h2 {
		font-weight: bold;
		font-size: 20px;
		margin-top: 0
	}

	.sa-modal .sa-modal-footer {
		border-top: #eeeeee solid 1px;
		/*text-align: right*/
	}

	.sa-modal .sa-modal-footer > .button {
		margin: 0 7px
	}

	.sa-modal .sa-modal-footer > .button:first-child {
		margin: 0
	}

	.sa-modal .sa-modal-panel:not(.active) {
		display: none
	}

	.sa-modal .reason-input {
		margin: 3px 0 3px 22px
	}

	.sa-modal .reason-input input, .sa-modal .reason-input textarea {
		width: 100%
	}

	body.has-sa-modal {
		overflow: hidden
	}

	#the-list .deactivate > .sa-sm-slug {
		display: none
	}

	.sa-modal li.reason-hide {
		display: none;
	}

	.sa-modal .sm-skip-deactivate-survey {
	    float: right;
	    font-size: 13px;
	    color: #ccc;
	    text-decoration: none;
	    padding-top: 7px;
	}

	.sa-modal .sm-skip-deactivate-survey:hover {
		color: #00a0d2;
		text-decoration: underline;
	}

	.sa-modal .error {
        display: block;
        color: red;
        margin: 0 0 10px 0;
    }

</style>
<script type="text/javascript">
	var currentPluginBaseName = "";
	var SACustomReasons = {};
	var SADefaultReason = {};
	var SAFormName = 'SM Deactivation Reason';
	var SAPluginName = '<?php echo $sa_plugin_name; ?>';
	var SAPluginVersion = '<?php echo $sa_plugin_version; ?>';
	( function ($) {
		var $deactivateLinks = {};
		var reasonsHtml = <?php echo json_encode( $reasons_list_items_html ); ?>,
			modalHtml =
				'<div class="sa-modal<?php echo ( $confirmation_message == "" ) ? ' no-confirmation-message' : ''; ?>">'
				+ ' <div class="sa-modal-dialog">'
				+ '     <div class="sa-modal-body">'
				+ '         <div class="sa-modal-panel" data-panel-id="confirm"><p><?php echo $confirmation_message; ?></p></div>'
				+ '         <div class="sa-modal-panel active" data-panel-id="reasons"><h2 class="sa-modal-deactivation-headline"><?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-headline' ) ); ?></h2><hr><h3 class="sa-modal-deactivation-second-headline"><?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-share-reason' ) ); ?>:</h3><ul id="reasons-list">' + reasonsHtml + '</ul></div>'
				+ '     </div>'
				+ '     <div class="sa-modal-footer">'
				+ '         <a href="#" class="button button-primary button-large button-deactivate"><?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-button-submit' ) ); ?></a>'
				+ '         <a href="#" class="sm-skip-deactivate-survey"><?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-button-cancel' ) ); ?></a>'
				+ '     </div>'
				+ ' </div>'
				+ '</div>',
			$modal = $(modalHtml),

			$deactivateLink = $('#the-list .deactivate > .sa-sm-slug').prev();

		for( var i = 0; i < $deactivateLink.length; i++ ) {
			$deactivateLinks[ $( $deactivateLink[i] ).siblings( ".sa-sm-slug" ).attr( 'data-slug' ) ] = $deactivateLink[i].href;
		}
   
		$modal.appendTo( $( 'body' ) );

		registerEventHandlers();

		jQuery(document).on('change', function() {
			setTimeout( function() {
				registerEventHandlers();
			}, 1000);
		});

		function registerEventHandlers() {
					
			$deactivateLink = $('#the-list .deactivate > .sa-sm-slug').prev();

			$deactivateLink.on( "click", function (evt) {
				evt.preventDefault();
				currentPluginBaseName = $(this).siblings( ".sa-sm-slug" ).attr( 'data-slug' );
				showModal();
			});

			$modal.on( 'click', '.sm-skip-deactivate-survey', function (evt) {
				window.location.href = $deactivateLinks[currentPluginBaseName];
			});

			$modal.on( 'click', '.button', function (evt) {
				evt.preventDefault();
				if ( $(this).hasClass( 'disabled' ) ) {
					return;
				}

				var _parent = $(this).parents( '.sa-modal:first' );
				var _this = $(this);

				if( _this.hasClass( 'allow-deactivate' ) ) {
					var $radio = $('input[type="radio"]:checked');
					var $selected_reason = $radio.parents('li:first'),
						$input = $selected_reason.find('textarea, input[type="text"]');


					if ( $radio.length == 0 ) {
						if( $modal.find('.sa-modal-footer .error').length == 0 ) {
							$modal.find('.sa-modal-footer').prepend('<span class="error"><?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-error' ) ); ?></span>');
						}
						return;
					}

					if( $radio.length == 0 ) {
						var data = {
							'action': 'sm_submit_survey',
							'rm_lead_name': SAFormName,
							'sa_plugin_name': SAPluginName,
							'sa_plugin_version': SAPluginVersion,
							'reason_id': 0,
							'reason_text': "Deactivated without any option",
						};
					} else {
						var data = {
							'action': 'sm_submit_survey',
							'rm_lead_name': SAFormName,
							'sa_plugin_name': SAPluginName,
							'sa_plugin_version': SAPluginVersion,
							'reason_id': ( 0 !== $radio.length ) ? $radio.val() : '',
							'reason_text': $selected_reason.text(),
							'reason_info': ( 0 !== $input.length ) ? $input.val().trim() : '',
						};
					}
					
					$.ajax({
						url: ajaxurl,
						method: 'POST',
						data: data,
						beforeSend: function () {
							_parent.find('.button').addClass('disabled');
							_parent.find('.button-secondary').text('Processing...');
						},
						complete: function () {
							// Do not show the dialog box, deactivate the plugin.
							window.location.href = $deactivateLinks[currentPluginBaseName];
						}
					});
				} else if ( _this.hasClass( 'button-deactivate' ) ) {
					// Change the Deactivate button's text and show the reasons panel.
					_parent.find('.button-deactivate').addClass('allow-deactivate');

					showPanel('reasons');
				}
			});

			$modal.on('click', 'input[type="radio"]', function () {
				var _parent = $(this).parents('li:first');
				var _parent_ul = $(this).parents('ul#reasons-list');

				_parent_ul.children("li.li-active").removeClass("li-active");

				$modal.find('.reason-input').remove();
				$modal.find('.button-deactivate').text('<?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-button-submit' ) ); ?>');

				if (_parent.hasClass('has_html')) {
					_parent.addClass('li-active');
				}
				if (_parent.hasClass('has-input')) {
					var inputType = _parent.data('input-type'),
						inputPlaceholder = _parent.data('input-placeholder'),
						reasonInputHtml = '<div class="reason-input">' + (('textfield' === inputType) ? '<input type="text" />' : '<textarea rows="5"></textarea>') + '</div>';

					_parent.append($(reasonInputHtml));
					_parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
				}
			});

			// If the user has clicked outside the window, cancel it.
			$modal.on('click', function (evt) {
				var $target = $(evt.target);

				// If the user has clicked anywhere in the modal dialog, just return.
				if ($target.hasClass('sa-modal-body') || $target.hasClass('sa-modal-footer')) {
					return;
				}

				// If the user has not clicked the close button and the clicked element is inside the modal dialog, just return.
				if (!$target.hasClass('button-close') && ($target.parents('.sa-modal-body').length > 0 || $target.parents('.sa-modal-footer').length > 0)) {
					return;
				}

				closeModal();
			});
		}

		function showModal() {
			resetModal();

			// Display the dialog box.
			$modal.addClass('active');

			$('body').addClass('has-sa-modal');
		}

		function closeModal() {
			$modal.removeClass('active');

			$('body').removeClass('has-sa-modal');
		}

		function resetModal() {
			if ( SACustomReasons.hasOwnProperty(currentPluginBaseName) === true ) {
				$modal.find("ul#reasons-list").html(SACustomReasons[currentPluginBaseName]);
			} else {
				$modal.find("ul#reasons-list").html(reasonsHtml);

			}
			var defaultSelect = SADefaultReason[currentPluginBaseName];
			$modal.find('.button').removeClass('disabled');

			// Remove all input fields ( textfield, textarea ).
			$modal.find('.reason-input').remove();

			var $deactivateButton = $modal.find('.button-deactivate');
			$modal.find(".reason-hide").hide();

			/*
			 * If the modal dialog has no confirmation message, that is, it has only one panel, then ensure
			 * that clicking the deactivate button will actually deactivate the plugin.
			 */
			if ( $modal.hasClass( 'no-confirmation-message' ) ) {
				$deactivateButton.addClass( 'allow-deactivate' );
				showPanel('reasons');
			} else {
				$deactivateButton.removeClass( 'allow-deactivate' );
				showPanel( 'confirm' );
			}
		}

		function showPanel(panelType) {
			$modal.find('.sa-modal-panel').removeClass('active ');
			$modal.find('[data-panel-id="' + panelType + '"]').addClass('active');

			updateButtonLabels();
		}

		function updateButtonLabels() {
			var $deactivateButton = $modal.find('.button-deactivate');
			
			// Reset the deactivate button's text.
			if ( 'confirm' === getCurrentPanel() ) {
				$deactivateButton.text('<?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-button-confirm' ) ); ?>');
			} else {
				var $radio = $('input[type="radio"]:checked');
				if( $radio.length == 0 ) {
					// $deactivateButton.text('<?php //printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-button-deactivate' ) ); ?>');
				} else {
					var _parent = $( $radio ).parents('li:first');
					var _parent_ul = $( $radio ).parents('ul#reasons-list');

					_parent_ul.children("li.li-active").removeClass("li-active");

					$modal.find('.reason-input').remove();
					$modal.find('.button-deactivate').text('<?php printf( SA_Smart_Manager_Deactivation::load_str( 'deactivation-modal-skip-deactivate' ) ); ?>');

					if ( _parent.hasClass('has_html') ) {
						_parent.addClass('li-active');
					}
					
					if ( _parent.hasClass('has-input') ) {
						var inputType = _parent.data('input-type'),
							inputPlaceholder = _parent.data('input-placeholder'),
							reasonInputHtml = '<div class="reason-input">' + (('textfield' === inputType) ? '<input type="text" />' : '<textarea rows="5"></textarea>') + '</div>';

						_parent.append($(reasonInputHtml));
						_parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
					}
				}
			}
		}

		function getCurrentPanel() {
			return $modal.find('.sa-modal-panel.active').attr('data-panel-id');
		}
	})(jQuery);
</script>
