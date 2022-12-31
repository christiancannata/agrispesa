jQuery(document).ready(function ($) {

    $('#cmb2-metabox-gdpr-compliance-cookie-consent .cmb-group-title:first-child').addClass('active');

    var cookie = document.cookie.replace(/(?:(?:^|.*;\s*)stm_gdpr_tab\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    if (cookie) {
        $('#cmb2-metabox-gdpr-compliance-cookie-consent .cmb-type-group').hide();
        $('#cmb2-metabox-gdpr-compliance-cookie-consent .cmb-type-group#' + cookie).show();
        $('#cmb2-metabox-gdpr-compliance-cookie-consent > a').removeClass('active');
        $('#' + cookie).addClass('active');
    } else {
        $('#cmb2-metabox-gdpr-compliance-cookie-consent .cmb-type-group#stmgdpr_general').show();
    }

    $('#cmb2-metabox-gdpr-compliance-cookie-consent > a').on('click', function(event) {
        event.preventDefault();
        
        $('#cmb2-metabox-gdpr-compliance-cookie-consent .cmb-type-group').hide();
        $('#cmb2-metabox-gdpr-compliance-cookie-consent .cmb-type-group#' + $(this).data('href')).show();
        $('#cmb2-metabox-gdpr-compliance-cookie-consent > a').removeClass('active');
        $(this).addClass('active');

        document.cookie = 'stm_gdpr_tab=' + $(this).data('href');
    });

});