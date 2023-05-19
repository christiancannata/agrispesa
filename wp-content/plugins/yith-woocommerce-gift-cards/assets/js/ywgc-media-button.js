jQuery(function($){

  $( "form#edittag" )
    .attr( "enctype", "multipart/form-data" )
    .attr( "encoding", "multipart/form-data" )
  ;

  $('#ywgc-image-cat-upload-button').on( 'click', function(e){
    e.preventDefault();
    $( '#ywgc-image-cat-upload-input').click();
  });

  $( '#ywgc-image-cat-upload-input' ).on('change', function(){
    $('.ywgc_safe_submit_field').val( 'uploading_image_on_category');
    $(this).closest('form').submit();

  });


  $(document).on( "click", ".ywgc-category-image-delete", function (e) {
    e.preventDefault();

    var image_id = $( this ).parent().data( 'design-id' );
    var cat_id = $( this ).parent().data( 'design-cat' );

    var data = {
      'action'   : 'ywgc_delete_image_from_category',
      'image_id' :image_id,
      'cat_id'   : cat_id
    };

    var clicked_item = $(this).parent();
    clicked_item.block({
      message   : null,
      overlayCSS: {
        background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
        opacity   : .6
      }
    });

    $.post(ywgc_data.ajax_url, data, function (response) {
      if (1 == response.code) {
        clicked_item.remove();
      }
      clicked_item.unblock();
    });

  });


  //Submit the form on edit category images
  $( '.image_gallery_ids' ).on('change', function(e){
    e.preventDefault();
    $(this).closest('form').submit();
  });


  // Delete the images when create the category
  $('#submit').click(function() {
    $( '#ywgc-upload-images-cat-creation-extra-images li' ).remove();
    $( '.image_gallery_ids' ).val( '' );
  });



  });
