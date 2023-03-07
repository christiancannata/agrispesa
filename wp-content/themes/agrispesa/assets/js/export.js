jQuery(document).ready(function ($) {

  $("#document_type").change(function () {

    $("#settimana_div").hide()
    $("#codice_confezionamento_container").hide()
    $("#data_consegna_div").show()

    if ($(this).val() == 'prelievi_magazzino_cliente' || $(this).val() == 'prelievi_magazzino_articolo') {
      $("#codice_confezionamento_container").show()
      return true
    }


    if ($(this).val() == 'fabbisogno') {
      $("#settimana_div").show()
      $("#data_consegna_div").hide()

      return true
    }


  })
})
