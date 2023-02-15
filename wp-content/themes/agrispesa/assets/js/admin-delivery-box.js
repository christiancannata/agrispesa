/* global WPURL:readonly */
jQuery(document).ready(function ($) {

  $("#_tipo_percentuale_ricarico").change(function () {

    if ($(this).is(':checked')) {

      $("#_percentuale_ricarico").attr('readonly', true)

      $.get(WPURL.siteurl + '/wp-json/agrispesa/v1/products/' + $("#post_ID").val() + '/category', function (data) {
        $("#_percentuale_ricarico").val(data.ricarico_percentuale)
        reloadPrice()
      });


    } else {

      $("#_percentuale_ricarico").removeAttr('readonly')
      reloadPrice()
    }

  })

  $("#_regular_price").attr('readonly', true)

  function reloadPrice() {
    let total = parseFloat($("#_prezzo_acquisto").val()) + parseFloat(($("#_prezzo_acquisto").val() / 100) * $("#_percentuale_ricarico").val())
    $("#_regular_price").val(total)
  }

  reloadPrice()

  $("#_prezzo_acquisto, #_percentuale_ricarico").change(function () {
    reloadPrice()

  })

  $(".generate-csv").click(function (e) {
    e.preventDefault()
    location.href = '/wp-json/agrispesa/v1/delivery-group-csv?delivery_group=' + $(this).data('delivery-group') + '&data_consegna=' + $(this).closest('td').find('input').val()
  })
})
