jQuery(document).ready(function ($) {
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
