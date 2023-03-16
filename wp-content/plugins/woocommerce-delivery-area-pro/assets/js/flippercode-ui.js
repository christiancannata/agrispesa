(function($) {
    'use strict';

    jQuery(document).ready(function($) {
		
        if($('#search_id-search-input').length > 0 ){
            $('#search_id-search-input').attr('placeholder', 'Enter your search term here..');
        }
        
		//Manage screen delete record
		jQuery('#delete_fc_record').on('shown.bs.modal', function (event) {

			$('.modal-footer.delete').show(); 
     		var triggerElement = jQuery(event.relatedTarget); // Button that triggered the modal
			var current_id = triggerElement.data('item-id');
			var current_page_url = triggerElement.data('current-page');
			var current_page = triggerElement.data('page-slug');
			var unique_key = triggerElement.data('unique-key');
			var record_type = triggerElement.data('record-type');
			var _wpnonce = $('#_wpnonce').val();
			var delete_url = '?page='+current_page+'&doaction=delete&'+unique_key+'='+current_id+'&_wpnonce='+_wpnonce;
			var modal = jQuery(this);
			modal.find(".modal-footer a").attr("href", delete_url);
		
		});
		
		
		//Manage bulk delete delete record
		let checked_chkboxes = [];
	
		$('.wp-list-table').find(':checkbox').change(function() {
			let temp = [];
			var checked_boxes = $('.wp-list-table').find(':checkbox').not('#cb-select-all-1');
			checked_boxes.each(function(){
				
				if($(this).is(":checked")){
					temp.push(this.value);
				}
				
			});
			checked_chkboxes = temp;
			  
		});


       $(document).on('click', '#doaction', function( e ) {
		
        e.preventDefault();
        
        if($('#bulk-action-selector-top').val() == 'delete'){
			
			if(checked_chkboxes.length == 0){
				
				$("#delete_bulk_fc_record .modal_delete_msg").text('No records were selected for bulk delete. Please select some records first.');
				$('.modal-custom-heading').text('No Records Selected!');
				$('.modal-footer.delete').hide();
				$('.modal-footer.select-some').show();
				
			}else{
				
				$("#delete_bulk_fc_record .modal_delete_msg").text('Are you sure you want to delete these records ?');
				$('.modal-custom-heading').text('Confirm Deletion');
				$('.modal-footer.delete').show();
				$('.modal-footer.select-some').hide();
				
			}
			
			$('#delete_bulk_fc_record').modal('show'); 
			
			return false;
						
		}else{
			
			$('.modal-footer.delete').show();
			$('.modal-footer.select-some').show();
		}
        
	});

	$(document).on('click', '.bulk-delete-btn', function( e ) {
	  
	  e.preventDefault();
	  $(".fc_manage_screen_form").submit();
	  
	});
	
		
	//Show confirmation popup for custom action
	
	$('.fc_backend_custom_action').on('hidden.bs.modal', function () {

	    $(document).find('.modal-message').hide();
	});

    jQuery('.fc_backend_custom_action').on('shown.bs.modal', function (event) {

		$('.modal-footer.delete').show(); 
 		var triggerElement = jQuery(event.relatedTarget); // Button that triggered the modal
		var current_id = triggerElement.data('item-id');
		var current_page_url = triggerElement.data('current-page');
		var current_page = triggerElement.data('page-slug');
		var unique_key = triggerElement.data('unique-key');
		var record_type = triggerElement.data('record-type');
		var action = triggerElement.data('action');
		var _wpnonce = $('#_wpnonce').val();
		var heading = triggerElement.data('action-heading');
		var icon = triggerElement.data('action-icon');
		var content = triggerElement.data('action-content');
		var button_text = triggerElement.data('button-text');
		
		$('#custom-action-yes').data('item-id',current_id);
		$('#fc_backend_custom_action .modal-body').find('img').attr('src', icon);
		$("#fc_backend_custom_action .action-body").text(content);
		$("#fc_backend_custom_action .modal-content").find('h4').text(heading);
		$("#fc_backend_custom_action .modal-footer").find('a').text(button_text).data('action', action).attr('data-action', action);

		var action_url = '?page='+current_page+'&doaction='+action+'&'+unique_key+'='+current_id+'&_wpnonce='+_wpnonce;
		var modal = jQuery(this);
		modal.find(".modal-footer a").attr("href", action_url);
	
	});
		
        var allPanels = $('.custom-accordion > dd').hide();

        $('.custom-accordion > dd:first-of-type').show();
        $('.custom-accordion > dt:first-of-type').addClass('accordion-active');
        $('.fc-help-right .custom-accordion > dd:first-of-type').hide();
        $('.fc-help-right .custom-accordion > dt:first-of-type').removeClass('accordion-active');

        $('.custom-accordion > dt').on('click', function() {
            var $this = $(this);
            var $target = $this.next(); 
            if(!$this.hasClass('accordion-active')){
                $this.parent().children('dd').slideUp();
                jQuery('.custom-accordion > dt').removeClass('accordion-active');
                $this.addClass('accordion-active');
                $target.addClass('active').slideDown();
            }else{
                
                 $this.next('dd').slideUp();
                 $this.addClass('accordion-active');
                 jQuery('.custom-accordion > dt').removeClass('accordion-active');
            }
            return false;
        });

        $('.wp-list-table .check-column input[type="checkbox"]').wrap('<span class="checkbox"></span>'); 

        $('.wp-list-table .check-column input[type="checkbox"]').after('<label></label>');

        var table = $('.wp-list-table #the-list');    

        table.on('click', 'tr', function (e) {

            if ( $(this).hasClass('active') ) {

                $(this).removeClass('active');

            }else{
                $(this).addClass('active');
            }
        });

        var $overviewPage = ($('.fcdoc-product-info').length > 0) ? true : false;
        if ($overviewPage)
            var ajaxUrl = fcajaxurl;


        $('.fc-field.ext_btn label').on('click', function() {
            $(this).prev('.fc-file_input').trigger('click');
        });

		
	
        $(".set-default-template").click(function(e) {

            $('.current_selected').removeClass('current_selected');
            $(this).addClass('current_selected');
            e.preventDefault();

            var template = $(this).data("templatename");
            var templatetype = $(this).data("templatetype");
            var product = $(this).data("product");
            var data = {
                action: 'core_backend_ajax_calls',
                product: product,
                template: template,
                templatetype: templatetype,
                selector: '.set-default-template',
                operation: 'fc_set_default_template',
                nonce: fc_ui_obj.nonce,
                page :fc_ui_obj.page
            };

            perform_ajax_events(data);

        });

        var currentDeletedTemplate = '';

        $('.delete-custom-template').on("click", function() {

            var confir_delete_teamplate = confirm('Are you sure you want to permanantly remove this template? ');
            if (confir_delete_teamplate) {

                var instance = $(this).data('instance');
                var product = $(this).data('product');
                var templatetype = $(this).data('templatetype');
                var templateName = $(this).data('templatename');

                var data = {
                    action: 'core_backend_ajax_calls',
                    instance: instance,
                    product: product,
                    templateName: templateName,
                    templatetype: templatetype,
                    selector: '.delete-custom-template',
                    operation: 'fc_delete_custom_template',
                    nonce: fc_ui_obj.nonce,
                    page :fc_ui_obj.page
                }
                currentDeletedTemplate = templateName;
                perform_ajax_events(data);

            }
            return false;

        });

        $("body").on('click', ".repeat_button", function() {

            var target = $(this).parent().parent();
            var new_element = $(target).clone();
            var inputs = $(new_element).find("input[type='text']");
            for (var i = 0; i < inputs.length; i++) {

                var element_name = $(inputs[i]).attr("name");
                var patt = new RegExp(/\[([0-9]+)\]/i);
                var res = patt.exec(element_name);
                var new_index = parseInt(res[1]) + 1;
                var name = element_name.replace(/\[([0-9]+)\]/i, "[" + new_index + "]");
                $(inputs[i]).attr("name", name);

            }

            var inputs = $(new_element).find("input[type='number']");

            for (var i = 0; i < inputs.length; i++) {

                var element_name = $(inputs[i]).attr("name");
                var patt = new RegExp(/\[([0-9]+)\]/i);
                var res = patt.exec(element_name);
                var new_index = parseInt(res[1]) + 1;
                var name = element_name.replace(/\[([0-9]+)\]/i, "[" + new_index + "]");
                $(inputs[i]).attr("name", name);

            }

            var selects = $(new_element).find("select");

            for (var i = 0; i < selects.length; i++) {

                var element_name = $(selects[i]).attr("name");
                var element_id = $(selects[i]).attr("id");

                var patt = new RegExp(/\[([0-9]+)\]/i);
                
                var res = patt.exec(element_name);
                var new_index = parseInt(res[1]) + 1;
                var name = element_name.replace(/\[([0-9]+)\]/i, "[" + new_index + "]");

                var res_id = patt.exec(element_id);
                var new_index_id = parseInt(res_id[1]) + 1;
                var id = element_id.replace(/\[([0-9]+)\]/i, "[" + new_index_id + "]");
               
                $(selects[i]).attr("name", name);
                $(selects[i]).attr("id", id);

            }

            if ($(this).val() == "Add More")
                $(this).val('Remove');
            else
                $(this).val('Remove');

            $(new_element).find("input[type='text']").val("");
            $(new_element).find("input[type='number']").val("");
            $(target).find(".repeat_button").text("Remove");
            $(target).find(".repeat_button").removeClass("repeat_button").addClass("repeat_remove_button");
            $(target).after($(new_element));

        });



        $("body").on('click', ".repeat_remove_button", function() {

            var target = $(this).parent().parent();
            var temp = $(target).clone();
            $(target).remove();
            var inputs = $(temp).find("input[type='text']");

            $.each(inputs, function(index, element) {

                var current_name = $(this).attr("name");
                var name = current_name.replace(/\[([0-9]+)\]/i, "");
                $.each($("*[name^='" + name + "']"), function(index, element) {

                    var current_name = $(this).attr('name');
                    var name = current_name.replace(/\[([0-9]+)\]/i, "[" + index + "]");
                    $(this).attr("name", name);

                });

            });

        });

        window.send_to_editor_default = window.send_to_editor;

        $('.fa-picture-o').click(function() {

            window.send_to_editor = function(html) {

                $('body').append('<div id="temp_image">' + html + '</div>');
                var img = $('#temp_image').find('img');
                var imgurl = img.attr('src');
                $('.active_element').css('background-image', 'url(' + imgurl + ')');
                try {
                    tb_remove();
                } catch (e) {}
                $('#temp_image').remove();
                window.send_to_editor = window.send_to_editor_default;

            };

            tb_show('', 'media-upload.php?post_ID=0&type=image&TB_iframe=true');
            return false;

        });

        var wpp_image_id = '';
        var currentClickedID = '';
        $('.choose_image').click(function() {

            var target = "icon_hidden_input";
            var wpp_image_id = $(this).parent().parent().attr('id', target);
            currentClickedID = $(this).attr('id');
            window.send_to_editor = window.attach_image;
            tb_show('', 'media-upload.php?post_ID=0&target=' + target + '&type=image&TB_iframe=true');
            return false;

        });

        window.attach_image = function(html) {

            var htmlobj = $(html);
            $classes = htmlobj.attr('class');
            if (typeof $classes == typeof undefined) {

                var img = $(html).find("img");
                var htmlobj = $(img);
                var $classes = htmlobj.attr('class');
                var lastClass = $classes.split(' ').pop();
                var $aid = lastClass.replace('wp-image-', '');

            } else {

                var lastClass = $classes.split(' ').pop();
                var $aid = lastClass.replace('wp-image-', '');

            }

            $('body').append('<div id="temp_image' + currentClickedID + '">' + html + '</div>');

            var img = $('#temp_image' + currentClickedID).find('img');
            var imgurl = img.attr('src');
            var imgclass = img.attr('class');
            var imgid = parseInt(imgclass.replace(/\D/g, ''), 10);
            $(wpp_image_id).find('.remove_image').show();

            if ($('#' + currentClickedID).prev('img').length > 0) {

                $('#' + currentClickedID).prev('img').show();
                $('#' + currentClickedID).prev('img').attr('src', imgurl);
                $('#' + currentClickedID).prev('img').removeClass('noimage');

            } else {

                var imgTag = '<img src="' + imgurl + '" alt="" height="100" width="100" class="selected_image">';
                var removeLink = '<a style="border:none;text-decoration:underline" href="javascript:void(0);" id="" name="remove_image" class="fc-btn fc-btn-red remove_image remove_image fc-3 fc-offset-1" data-target="' + $('#' + currentClickedID).data('target') + '">Remove Image</a>';

                $('#' + currentClickedID).parent('div').prepend(imgTag);
                $('#' + currentClickedID).after(removeLink);

            }

            var img_hidden_field = $('#' + currentClickedID).data('target');
            $('#' + img_hidden_field).val(imgurl);
            $('#' + img_hidden_field + '_attachment_id').val($aid);

            try {

                tb_remove();

            } catch (e) {};

            $('#temp_image' + currentClickedID).remove();
            window.send_to_editor = window.send_to_editor_default;

        }

        $(document).on('click', '.remove_image', function() {

            if (confirm('Are you sure you want to remove this image ?')) {

                var img = $(this).parent().find('img');
                $(img).attr('src', '');
                $(this).parent().find('input[name="' + $(this).data('target') + '"]').val('');
                $(this).parent().find('input[name="' + $(this).data('target') + '_attachment_id"]').val('');
                $(this).hide();
                $(img).hide();
                return false;

            } else {

                return false;

            }

        });

        $('.switch_onoff').change(function() {

            var target = $(this).data('target');
            if ($(this).attr('type') == 'radio') {
                $(target).closest('.fc-form-group').hide();
                target += '_' + $(this).val();
            }

            if ($(this).is(":checked")) {
                $(target).closest('.fc-form-group').show();
            } else {

                $(target).closest('.fc-form-group').hide();
                if ($(target).hasClass('switch_onoff')) {
                    $(target).attr('checked', false);
                    $(target).trigger("change");

                }
            }

        });

        $.each($('.switch_onoff'), function(index, element) {

            if (true == $(this).is(":checked")) {
                $(this).trigger("change");
            }

        });

        $("input[name='wpp_refresh']").trigger('change');

        function ajax_success_handler(data, selector) {
            //console.log('data', data.default_templates);
            switch (selector) {

                case '.set-default-template':

                    $('.fc_tools').css('display', 'none');
                    $('.fc_name').css('display', 'none');
                    $('.current_selected').parent().parent().find('.fc_name').css('display', 'block');
                    $('.current_selected').closest('.fc_tools').css('display', 'block');
                    $('.current-temp-in-use').removeClass('current-temp-in-use');
                    $('.current_selected').addClass('current-temp-in-use');
                    if ($(selector).hasClass('user-temp')) {
                        $(selector).parent('li').next('li').find('a.delete-custom-template').remove();
                    }
                    $('#hidden_shortcode_template').val(data.default_templates.shortcode);
                    $('#hidden_zip_template').val(data.default_templates.zipcode);

                    break;

                case '.delete-custom-template':
                    $("a.set-default-template[data-templatename=" + currentDeletedTemplate + "]").closest('.fc-4').remove();
                    break;

                case '.default-custom-template':
                    $(".default-custom-template[data-templatename=" + currentDeletedTemplate + "]").parent().parent().parent().remove();
                    break;

                default:

            }

        }

        function perform_ajax_events(data) {

            var $inputs = data
            jQuery.ajax({
                type: "POST",
                url: fc_ui_obj.ajax_url,
                dataType: "json",
                data: data,
                beforeSend: function() {
                    jQuery(".se-pre-con").fadeIn("slow");

                },

                success: function(data) {
                    jQuery(".se-pre-con").fadeOut("slow");
                    ajax_success_handler(data, $inputs.selector);
                }
            });

        }

        // Sticky Footer

        if ($('.fc-footer').length > 0) {
            $(window).scroll(function() {
                if ($('.flippercode-ui-height').height() > 800) {
                    if ($('.fc-no-sticky').length > 0) {
                        return;
                    }

                    var scroll = $(window).scrollTop();
                    var scrollBottom = $(window).height() - scroll;
                    if (scroll >= 0) {
                        $(".fc-footer").addClass("fc-fixed-footer");
                    }

                    if ($(window).scrollTop() + $(window).height() > ($(document).height() - 30)) {
                        $(".fc-fixed-footer").removeClass("fc-fixed-footer");
                    }

                }

            });

        }

        $(window).scroll(function() {

            var scroll = $(window).scrollTop();
            var scrollBottom = $(window).height() - scroll;
            if (scrollBottom < 400) {
                $(".fc-fixed-footer").removeClass("fc-fixed-footer");
            }

        });

        if (jQuery(".color").length > 0) {
            $('.color').wpColorPicker();
        }

        if (jQuery(".fc_select2").length > 0) {
            jQuery(".fc_select2").select2({
                     width: 'element',

            });
        }

        $('.fc-main').find('[data-toggle="tab"]').click(function(e) {

            e.preventDefault();
            var tab_id = $(this).attr('href');
            $('.fc-tabs-container .fc-tabs-content').hide();
            $(tab_id).show();
            $('.fc-tabs .active').removeClass('active');
            $(this).parent().addClass('active');

        });

        if ($('.current-temp-in-use').length) {
            $('.current-temp-in-use').parent().parent().find('.fc_name').css('display', 'block');
            $('.current-temp-in-use').closest('.fc_tools').css('display', 'block');
        }

    });


}(jQuery))
