/* global Swal:readonly */

jQuery(document).ready(function ($) {

  $(".change_subscription").click(function (e) {

    e.preventDefault()

    Swal.fire({
      title: 'Vuoi cambiare tipo di Facciamo noi?',
      text: "",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3c21ff',
      cancelButtonColor: '#f8f5f1',
      confirmButtonText: 'Si',
      cancelButtonText: 'No',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        location.href = '/box/facciamo-noi?skipCheckSubscription=1'
      }
    })
  })

})
