jQuery(function ($) {

    $('#download_zip').click(function (event) {
        event.preventDefault();

        selector = $(this);
        files = $(this).data('files');
        text = $(this).text();

        $(this).text(wps_cleaner_i18n.downloading);

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        var filename = 'archive_wps_cleaner_medias_' + yyyy + '_' + mm + '_' + dd + '_' + today.getTime() + '.zip';

        data = {
            'action': 'create_zip_archive_medias',
            'files' : files,
            'filename' : filename,
            '_ajax_nonce' : $( this ).data('nonce')
        };

        $.ajax({
            url : ajaxurl,
            type : 'POST',
            data : data,
            tryCount : 0,
            retryLimit : 10,
            success : function( response ) {
                selector.text(text);
                var obj = jQuery.parseJSON( response );
                if(obj.zip) {
                    location.href = obj.zip;
                    data = {
                        'action': 'delete_zip_archive_medias',
                        'zip' : obj.zip,
                        '_ajax_nonce' : obj.nonce
                    };
                    $.post(ajaxurl, data)
                }
            },
            error : function( xhr, textStatus, errorThrown ) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    $.ajax(this);
                    return;
                }
                return;
            }
        });
    });

    $('#download_zip_file').click(function (event) {
        event.preventDefault();

        selector = $(this);
        ids = $(this).data('ids');
        text = $(this).text();

        $(this).text(wps_cleaner_i18n.downloading);

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        var filename = 'archive_wps_cleaner_files_' + yyyy + '_' + mm + '_' + dd + '_' + today.getTime() + '.zip';

        data = {
            'action': 'create_zip_archive_files',
            'ids' : ids,
            'filename' : filename,
            '_ajax_nonce' : $( this ).data('nonce')
        };

        t = $.post(ajaxurl, data);

        t.success(function(response){
            selector.text(text);
            var obj = jQuery.parseJSON( response );
            if(obj.zip) {
                location.href = obj.zip;
                data = {
                    'action': 'delete_zip_archive_files',
                    'zip' : obj.zip,
                    '_ajax_nonce' : obj.nonce
                };
                $.post(ajaxurl, data)
            }
        });
    });

    $('.wps-clean').click(function (event) {
        event.preventDefault();

        selector = $(this);
        text =selector.find('.text').text();
        do_action = selector.data('action');
        nonce = selector.data('nonce');

        selector.find('.text').text(wps_cleaner_i18n.doing);

        data = {
            'action': 'wps_cleaner_clean',
            'nonce': nonce,
            'do_action': do_action
        };

        $.ajax({
            url : ajaxurl,
            type : 'POST',
            data : data,
            tryCount : 0,
            retryLimit : 30,
            success : function( response ) {
                selector.find('.text').text(text);
                selector.find('.count').html('(' + response + ')');
            },
            error : function( xhr, textStatus, errorThrown ) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    $.ajax(this);
                    return;
                }
                return;
            }
        });
    });

    $('#sweep_all').click(function (event) {
        event.preventDefault();

        selector = $(this);
        text = selector.text();
        nonce = selector.data('nonce');

        selector.text(wps_cleaner_i18n.doing);

        data = {
            'action': 'wps_cleaner_clean_all',
            'nonce': nonce
        };

        $.ajax({
            url : ajaxurl,
            type : 'POST',
            data : data,
            tryCount : 0,
            retryLimit : 30,
            success : function(json) {
                selector.text(text);
            },
            error : function( xhr, textStatus, errorThrown ) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    $.ajax(this);
                    return;
                }
                return;
            }
        });
    });

    $('.delete_old_files').click(function (event) {
        event.preventDefault();

        selector = $(this);
        text = selector.text();
        do_action = selector.data('action');
        nonce = selector.data('nonce');

        selector.text(wps_cleaner_i18n.delete);

        data = {
            'action': 'delete_old_files',
            'nonce': nonce,
            'do_action': do_action
        };

        t = $.post(ajaxurl, data);

        t.success(function (response ) {
            $('.wps-table-old-files').hide();
        });
    });

    $('.delete_dir_file').click(function (event) {
        event.preventDefault();

        selector = $(this);
        text = selector.text();
        do_action = selector.data('action');
        nonce = selector.data('nonce');
        dir_file = selector.data('dir_file');

        selector.text(wps_cleaner_i18n.delete);

        data = {
            'action': 'delete_dir_file',
            'nonce': nonce,
            'do_action': do_action,
            'dir_file': dir_file
        };

        t = $.post(ajaxurl, data);

        t.success(function (response ) {
            selector.parent().parent().hide();
        });
    });

    $('#wps_cleaner_alert_footer_button').click(function (event) {
        event.preventDefault();

        data = {
            'action': 'delete_alert',
            '_ajax_nonce': $(this).data( 'nonce' )
        };

        t = $.post(ajaxurl, data);

        t.success(function () {
            $('.wps_cleaner_alert_bg').hide();
        });
    });

    $('#delete_whitelist').click(function (event) {
        event.preventDefault();

        $(this).text(wps_cleaner_i18n.deleting);

        data = {
            'action': 'delete_medias_whitelist',
            '_ajax_nonce': $(this).data('nonce')
        };

        $.ajax({
            url : ajaxurl,
            type : 'POST',
            data : data,
            success : function() {
               $('.wps_medias_whitelist').hide();
               location.reload();
            }
        });
    });

    $('#view_whitelist').click(function (event) {
        event.preventDefault();
        $(this).html($(this).html() == wps_cleaner_i18n.view_list ? wps_cleaner_i18n.hide_list : wps_cleaner_i18n.view_list);
        $('.view_whitelist').toggle();
    });
});