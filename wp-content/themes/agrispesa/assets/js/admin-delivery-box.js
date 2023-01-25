jQuery(document).ready(function ($) {
  $(".generate-csv").click(function (e) {
    e.preventDefault()
    location.href = '/wp-json/agrispesa/v1/delivery-group-csv?delivery_group=' + $(this).data('delivery-group') + '&week=' + $(this).closest('td').find('select').val()
  })
})
