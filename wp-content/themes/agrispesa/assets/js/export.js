jQuery(document).ready(function($){

  $("#document_type").change(function () {

    if ($(this).val() == 'prelievi_magazzino_cliente' || $(this).val() == 'prelievi_magazzino_articolo') {
      $("#codice_confezionamento_container").show()
    } else {
      $("#codice_confezionamento_container").hide()
    }
  })
})
