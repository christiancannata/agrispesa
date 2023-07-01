/* global Swal:readonly */
/* global WPURL:readonly */
/* global axios:readonly */

jQuery(document).ready(function ($) {

  $(".change_subscription_status").click(function (e) {
    e.preventDefault()

    let subscription = $(this).data('id')

    let status = 'attivare'
    if ($(this).data('current-status') == 'active') {
      status = 'disattivare'
    }

    Swal.fire({
      title: 'Vuoi ' + status + ' la Facciamo noi?',
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
        axios.post(WPURL.siteurl + '/wp-json/agrispesa/v1/subscription/' + subscription + '/change-status', {})
          .then((response) => {
            Swal.fire({
              title: 'Facciamo noi aggiornata con successo.',
              text: "",
              icon: '',
              confirmButtonColor: '#3c21ff',
              confirmButtonText: 'Continua',
            }).then(function () {
              location.href = '';
            })
          })
          .catch((error) => {
            if (error.response.status == 404) {
              alert(error.response.data.message)
              return false
            }
            console.log(error)
          })
      }
    })


  })

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
