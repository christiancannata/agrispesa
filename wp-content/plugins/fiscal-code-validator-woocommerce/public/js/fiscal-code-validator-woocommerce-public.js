(function ($) {
    'use strict';

    $(document).ready(function () {
        let currentRequest = null;

        $("#fiscal_code").change(function () {
            let elem = $(this)
            elem.parent().find('.alert').remove()
            currentRequest = jQuery.ajax({
                type: 'POST',
                data: {
                    fiscal_code: elem.val()
                },
                dataType: 'json',
                url: '/wp-json/fiscal-code/validate',
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function (data) {
                    let div = '';
                    if (data.is_valid) {
                        div = '<span class="alert alert-success">' + data.message + '</span>';
                        $("#is_fiscal_code_valid").val(1)
                    } else {
                        div = '<span class="alert alert-danger">' + data.message + '</span>';
                        $("#is_fiscal_code_valid").val(0)
                    }
                    elem.parent().append(div)

                },
                error: function (e) {
                    // Error
                }
            });
        })
    });

})(jQuery);
