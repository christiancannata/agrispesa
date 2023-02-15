jQuery(function($) {

	/**
	 * Remove amount for the current gift card
	 */
	$(document).on("click", "a.remove-amount", function(e) {
		e.preventDefault();
		remove_amount($(this));
	});


	function remove_amount(item) {

		var clicked_item = item.closest("span.variation-amount");
		var position_of_clicked_item = clicked_item.index();

		var currency_prices_section = $(".yith-wcmcs-currencies-prices");
		var is_currency_amount = clicked_item.parent().parent().parent().parent().hasClass('yith-wcmcs-currencies-prices--row');

		clicked_item.remove();

		if (currency_prices_section.length && !is_currency_amount) {

			$(".yith-wcmcs-currencies-prices--row .variation-amount-list").each(function() {
				$(this).children().eq(position_of_clicked_item).remove();
			});

		}

		$(document.body).trigger('removed_gift_card_amount');

	};

	/**
	 * Add a new amount for the current gift card
	 */
	$(document).on("click", "a.add-new-amount", function(e) {
		e.preventDefault();
		add_amount($(this));
	});

	/**
	 * Add a new amount to current gift card
	 * @param item
	 */
	function add_amount(item) {

		var amount_input_value = item.parent().find('#gift_card-amount').val();
		var clicked_item = item.closest("span.add-new-amount-section");
		var amounts_list = item.parent().parent().find("span.variation-amount-list");
		var currency_prices_section = $(".yith-wcmcs-currencies-prices");
		var is_currency_amount = clicked_item.parent().parent().hasClass('yith-wcmcs-currencies-prices--row');
		var hidden_input_aux = $(".variation-amount-aux");
		var amount_index = amounts_list.find("span.variation-amount").length;
		var add_amount_condition = true;

		if (!amount_input_value.length || parseFloat(amount_input_value.replace(',', '.')) <= 0) {
			add_amount_condition = false;

			$('.add-new-amount-section #gift_card-amount').addClass('ywgc-wrong-amount-alert-border ywgc-wrong-amount-alert');
			$('.add-new-amount-section .ywgc-currency-symbol-enter-amount').addClass('ywgc-wrong-amount-alert');

			$('.ywgc-tooltip-container.ywgc-invalid-amount').removeClass('ywgc-hidden');
		}

		amounts_list.find("span.variation-amount .yith_wcgc_multi_currency").each(function() {

			if (parseFloat($(this).val().replace(',', '.')) == parseFloat(amount_input_value.replace(',', '.'))) {
				add_amount_condition = false;

				$('.add-new-amount-section #gift_card-amount').addClass('ywgc-wrong-amount-alert-border ywgc-wrong-amount-alert');
				$('.add-new-amount-section .ywgc-currency-symbol-enter-amount').addClass('ywgc-wrong-amount-alert');

				$('.ywgc-tooltip-container.ywgc-amount-already-added').removeClass('ywgc-hidden');

			}

		});

		if (add_amount_condition) {

			hidden_input_aux.clone().insertBefore(hidden_input_aux).removeClass('ywgc-hidden variation-amount-aux').addClass('variation-amount new-amount');

			var hidden_input = $('.variation-amount.new-amount input.yith_wcgc_multi_currency');
			var visible_input = $('.variation-amount.new-amount input.gift_card-amount');

			hidden_input.attr('name', 'gift-card-amounts[' + amount_index + ']');
			hidden_input.val(amount_input_value);

			hidden_input.parent().data("amount", amount_input_value);

			visible_input.val(amount_input_value);
			visible_input.data("amount", amount_input_value);

			$('.variation-amount.new-amount').data("amount", amount_input_value);
			$('.variation-amount.new-amount').removeClass('new-amount');


			// sort the amount list
			amounts_list.find("span.variation-amount ").sort(sort_amount_list).appendTo(amounts_list);

			// clear the add amount input
			$('#gift_card-amount').val('');
			$('#gift_card-amount').selectionStart = 0;
			$('#gift_card-amount').selectionEnd = 0;

			// if there is additional currencies
			if (currency_prices_section.length && !is_currency_amount) {

				$(".yith-wcmcs-currencies-prices--row .variation-amount-list").each(function() {

					var hidden_input_currency_aux = $(this).find(".variation-amount-aux-currency");

					hidden_input_currency_aux.clone().insertBefore(hidden_input_currency_aux).removeClass('ywgc-hidden variation-amount-aux-currency').addClass('variation-amount new-amount-currency');

					var currency_hidden_input = $('.variation-amount.new-amount-currency input.yith_wcgc_multi_currency');
					var currency_visible_input = $('.variation-amount.new-amount-currency input.gift_card-amount');

					var currency_id = currency_visible_input.data('currency-id');
					var currency_options = $(this).parent().parent().parent().data('currency-options');

					var fromPrice = parseFloat(amount_input_value.replace(',', '.')),
						fromRate = 1,
						toRate = currency_options['rate'],
						toDecimals = currency_options['decimals'],
						toRound = currency_options['round'],
						decimalSeparator = currency_options['decimal_separator'];

					var converted_amount = convert_amount(fromPrice, fromRate, toRate, toDecimals, toRound, decimalSeparator);

					currency_hidden_input.attr('name', 'yith_wcgc_multi_currency[gift-card-amounts][' + currency_id + '][' + amount_index + ']');
					currency_hidden_input.val(converted_amount);

					currency_hidden_input.parent().data("amount", converted_amount);


					currency_visible_input.val(converted_amount);
					currency_visible_input.data("amount", converted_amount);

					$('.variation-amount.new-amount-currency').data("amount", converted_amount).removeClass('new-amount-currency');

					$(this).find("span.variation-amount").sort(sort_amount_list).appendTo($(this));

				});

			}

			$(document.body).trigger('added_gift_card_amount');
		}

	}

	$(document).on('input', '.add-new-amount-section #gift_card-amount', function() {
		$(this).removeClass('ywgc-wrong-amount-alert-border ywgc-wrong-amount-alert');
		$('.add-new-amount-section .ywgc-currency-symbol-enter-amount').removeClass('ywgc-wrong-amount-alert');

		$('.ywgc-tooltip-container').addClass('ywgc-hidden');
	});

	/**
	 * Add a new amount for the current gift card on "enter"
	 */
	$(document).on('keypress', 'input#gift_card-amount', function(e) {
		if (event.which === 13) {
			e.preventDefault();

			$(this).parent().find('a.add-new-amount').click();

		}
	});


	/**
	 * Update amount for the current gift card
	 */
	$(document).on('focusout', 'input.gift_card-amount', function(e) {
		e.preventDefault();

		var amount_input = $(this).parent().find('.gift_card-amount').val();
		$(this).parent().find('.yith_wcgc_multi_currency').val(amount_input);
	});

	function sort_amount_list(a, b) {

		return parseFloat($(b).data('amount').toString().replace(',', '.')) < parseFloat($(a).data('amount').toString().replace(',', '.')) ? 1 : -1;
	}

	/**
	 * Convert amount to specific currency
	 */
	function convert_amount(fromPrice, fromRate, toRate, toDecimals, toRound, decimalSeparator) {

		var price = parseFloat(fromPrice) / parseFloat(fromRate) * parseFloat(toRate);

		switch (toRound) {
			case 'round-up':
				var pow = Math.pow(10, toDecimals);
				price = -Math.round((price * (-1) * pow - 0.5)) / pow;
				break;
			case 'round-down':
				price -= 0.5 / Math.pow(10, toDecimals);
				break;
			case 'round-int-up':
				price = Math.ceil(price);
				break;
			case 'round-int-down':
				price = Math.floor(price);
				break;
		}
		price = price.toFixed(toDecimals);

		price = Math.max(parseFloat(price.replace(decimalSeparator, '.')), 0).toString().replace('.', decimalSeparator);

		return price < 0 ? 0 : price;

	}


	$(document).on('change', 'input[name="ywgc_physical_gift_card"]', function(e) {
		var status = $(this).prop("checked");
		$('input[name="_virtual"]').prop("checked", !status);
	});

	$(document).on('click', '.image-gallery-reset', function(e) {
		e.preventDefault();

		$('#ywgc-card-header-image img').remove();
		$("#ywgc_product_image_id").val(0);
	});


	$('body .ywgc_order_sold_as_gift_card').each(function() {
		$(this).parent('td').find('.wc-order-item-name').hide();
	});

	//show the manage stock in the inventory tab
	$('._manage_stock_field').addClass('show_if_gift-card').show();

	/* Manage date when gift card is created manually */
	if (typeof jQuery.fn.datepicker !== "undefined") {

		$(".ywgc-expiration-date-picker").datepicker({ dateFormat: ywgc_data.date_format, minDate: +1 });
	}


	var default_button_text = $('button.ywgc-actions:first').text();


	$(document).on('click', 'button.ywgc-actions', function(e) {
        e.preventDefault();

        var button      = $(this),
            link        = button.prev('#ywgc_direct_link').text(),
            copied_text = $('#ywgc_copied_to_clipboard').text();

        if ( navigator.clipboard && window.isSecureContext ) {
            navigator.clipboard.writeText( link ).then(
                () => {
                    button.text(copied_text);
                },
                () => {
                    console.log( 'Copy to clipboard failed' );
                }
            );
        }

        setTimeout(function() {
            button.text(default_button_text);
        }, 1000);
    });

	$(document).on('change', '.ywgc-toggle-enabled input', function() {

		var enabled = $(this).val() === 'yes' ? 'yes' : 'no',
			container = $(this).closest('.ywgc-toggle-enabled'),
			gift_card_ID = container.data('gift-card-id');

		var blockParams = {
			message: null,
			overlayCSS: { background: '#fff', opacity: 0.7 },
			ignoreIfBlocked: true
		};
		container.block(blockParams);

		$.ajax({
			type: 'POST',
			data: {
				action: 'ywgc_toggle_enabled_action',
				id: gift_card_ID,
				enabled: enabled,
			},
			url: ajaxurl,
			success: function(response) {
				if (typeof response.error !== 'undefined') {
					alert(response.error);
				}
			},
			complete: function() {
				container.unblock();
			}
		});
	});


	if ($('.ywgc-override-product-settings input').val() === 'yes') {

		$('.ywgc-custom-amount-field').removeClass('ywgc-hidden');
		$('.minimal-amount-field').removeClass('ywgc-hidden');
		$('.maximum-amount-field').removeClass('ywgc-hidden');

	}

	$(document).on('change', '.ywgc-override-product-settings > input', function() {

		var enabled = $(this).val() === 'yes' ? 'yes' : 'no';

		if (enabled == 'yes') {
			$('.ywgc-custom-amount-field').show();

			if ($('.ywgc-custom-amount-field input').val() === 'yes') {
				$('.minimal-amount-field').show();
				$('.maximum-amount-field').show();
			}


		} else {
			$('.ywgc-custom-amount-field').hide();
			if ($('.ywgc-custom-amount-field input').val() === 'yes') {
				$('.minimal-amount-field').hide();
				$('.maximum-amount-field').hide();
			}


		}
	});


	if ($('.ywgc-custom-amount-field input').val() === 'yes' && $('.ywgc-override-product-settings input').val() == 'yes') {
		$('.minimal-amount-field').removeClass('ywgc-hidden');
		$('.maximum-amount-field').removeClass('ywgc-hidden');

	} else {
		$('.minimal-amount-field').addClass('ywgc-hidden');
		$('.maximum-amount-field').addClass('ywgc-hidden');

	}


	$(document).on('change', '.ywgc-custom-amount-field input', function() {
		var enabled = $(this).val() === 'yes' ? 'yes' : 'no';

		if (enabled == 'yes') {
			$('.minimal-amount-field').show();
			$('.maximum-amount-field').show();

		} else {
			$('.minimal-amount-field').hide();
			$('.maximum-amount-field').hide();

		}
	});


	if ($('.ywgc-add-discount-settings input').val() === 'yes') {
		$('.ywgc-add-discount-settings-container').removeClass('ywgc-hidden');
	}

	$(document).on('change', '.ywgc-add-discount-settings input', function() {
		var enabled = $(this).val() === 'yes' ? 'yes' : 'no';

		if (enabled == 'yes') {
			$('.ywgc-add-discount-settings-container').show();
		} else {
			$('.ywgc-add-discount-settings-container').hide();
		}

	});


	if ($('.ywgc-expiration-settings input').val() === 'yes') {
		$('.ywgc-expiration-settings-container').removeClass('ywgc-hidden');
	}

	$(document).on('change', '.ywgc-expiration-settings input', function() {
		var enabled = $(this).val() === 'yes' ? 'yes' : 'no';

		if (enabled == 'yes') {
			$('.ywgc-expiration-settings-container').show();
		} else {
			$('.ywgc-expiration-settings-container').hide();
		}

	});


	// Table hover and hide for the send email and PDF buttons
	$(document).on('mouseover', 'table tr.type-gift_card', function(e) {
		$(this).css('background-color', '#f1f6f8');
		$(this).find('td.gift_card_actions .ywgc-actions').show();
	});

	$(document).on('mouseout', 'table tr.type-gift_card', function(e) {
		$(this).removeAttr('style');
		$(this).find('td.gift_card_actions .ywgc-actions').hide();
	});


	$(document).ready(function() {

		var amounts = $('.ywgc-product-edit-page-amount-options .variation-amount-list').has('span.variation-amount').length;

		if (!amounts) {
			$('.ywgc-product-edit-page-multi-currency-options').hide();
		}

	});


	$(document).on('added_gift_card_amount', function(event) {

		$('.ywgc-product-edit-page-multi-currency-options').show();


		var amounts = $('.ywgc-product-edit-page-amount-options .variation-amount-list span.variation-amount').length;

		$(".ywgc-product-edit-page-multi-currency-options .yith-wcmcs-currencies-prices .yith-wcmcs-currencies-prices--row").each(function() {

			var currency_amounts = $(this).find('.variation-amount-list span.variation-amount').length;

			if (amounts != currency_amounts) {
				var difference = Math.abs(amounts - currency_amounts);
				$(this).find('.variation-amount-list span.variation-amount:nth-last-child(-n+' + difference + ')').remove();
			}
		});

	});

	$(document).on('removed_gift_card_amount', function(event) {

		var amounts = $('.ywgc-product-edit-page-amount-options .variation-amount-list').has('span').length;

		if (!amounts) {
			$('.ywgc-product-edit-page-multi-currency-options').hide();
		}

		var amounts = $('.ywgc-product-edit-page-amount-options .variation-amount-list span.variation-amount').length;

		$(".ywgc-product-edit-page-multi-currency-options .yith-wcmcs-currencies-prices .yith-wcmcs-currencies-prices--row").each(function() {

			var currency_amounts = $(this).find('.variation-amount-list span.variation-amount').length;

			if (amounts != currency_amounts) {
				var difference = Math.abs(amounts - currency_amounts) + 1;
				$(this).find('.variation-amount-list span.variation-amount:nth-last-child(-n+' + difference + ')').remove();
			}
		});

	});



});
