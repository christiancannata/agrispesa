/* global WPURL:readonly */
jQuery(document).ready(function ($) {

  $('#woocommerce-order-items').on('click', 'button.add_order_item_meta', function () {
    let $this = $(this)
    setTimeout(
      function () {
        $this.closest('table').find('input[type="text"]').val('NOTE')
        $this.closest('table').find('input[type="text"]').attr('readonly', true)

      }, 250);
  })

  $("#_tipo_percentuale_ricarico").change(function () {

    if ($(this).is(':checked')) {

      $("#_percentuale_ricarico").attr('readonly', true)
      getPercentualeRicarico()

    } else {

      $("#_percentuale_ricarico").removeAttr('readonly')
      reloadPrice()

    }

  })

  if ($("#_tipo_percentuale_ricarico").is(':checked')) {
    getPercentualeRicarico()
    $("#_percentuale_ricarico").attr('readonly', true)

  }

  $("#_regular_price").attr('readonly', true)

  function reloadPrice() {
    let total = parseFloat($("#_prezzo_acquisto").val()) + parseFloat(($("#_prezzo_acquisto").val() / 100) * $("#_percentuale_ricarico").val());
    let tax = $("#_tax_class").val();

    if (tax === undefined || tax === null || tax === '' || tax === 'tasse' || tax === 'nessuna-tariffa') {
      let get_taxes = '';
      let new_price = parseFloat(total);
      $("#_regular_price").val(new_price.toFixed(2).replace(".", ","))
    } else {
      let get_taxes = parseFloat($("#_tax_class").val());
      let new_price = parseFloat(((total * get_taxes) / 100) + total);
      $("#_regular_price").val(new_price.toFixed(2).replace(".", ","))
    }


  }

  function getPercentualeRicarico() {
    $.get(WPURL.siteurl + '/wp-json/agrispesa/v1/products/' + $("#post_ID").val() + '/category', function (data) {
      $("#_percentuale_ricarico").val(data.ricarico_percentuale)
      reloadPrice()
    });
  }

  reloadPrice()

  $("#_prezzo_acquisto, #_percentuale_ricarico").change(function () {
    reloadPrice()
  })
  $("#_prezzo_acquisto, #_percentuale_ricarico").focusout(function () {
    reloadPrice()
  })

  $("#_tax_class").change(function () {
    reloadPrice()
  });

  $(".generate-csv").click(function (e) {
    e.preventDefault()
    location.href = '/wp-json/agrispesa/v1/delivery-group-csv?delivery_group=' + $(this).data('delivery-group') + '&data_consegna=' + $(this).closest('td').find('select').val()
  })
})
