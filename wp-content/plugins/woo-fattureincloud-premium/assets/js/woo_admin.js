jQuery(document).ready(function(){
    jQuery('.woo-datepicker').datepicker({ dateFormat: 'dd/mm/yy'});
    jQuery('.woo-datepicker').datepicker('setDate', new Date());
         
});

jQuery(document).ready(function(){
    jQuery('.woo-datepicker-corr').datepicker({ dateFormat: 'dd/mm/yy'});
    //jQuery('.woo-datepicker').datepicker('setDate', new Date());
         
});

jQuery(document).ready(function(){
    jQuery('.woo-datepicker-corr-wfic').datepicker({ dateFormat: 'yy-mm-dd'});
    //jQuery('.woo-datepicker').datepicker('setDate', new Date());
         
});


function funzione_04 () {

    var radio01 = document.getElementById("woofic_fattura_normale_checkout");
    var radio02 = document.getElementById("woofic_fattura_elettronica_checkout");
    var radio03 = document.getElementById("woofic_fattura_normelettr_checkout");

    var radio04 = document.getElementById("fattureincloud_status_order_completed");
    var radio05 = document.getElementById("fattureincloud_status_order_onhold");
    var radio06 = document.getElementById("fattureincloud_status_order_processing");


    var radio07 = document.getElementById("fatturaelettronica_auto_save");
    var radio08 = document.getElementById("fattura_auto_save");
    var radio09 = document.getElementById("ricevuta_auto_save");



    
    if ((radio01.checked == true || radio02.checked == true || radio03.checked == true) && (
        /*radio04.checked == true || radio05.checked == true || radio06.checked == true 
        || */radio07.checked == true || radio08.checked == true || radio09.checked == true )
        ) {
    
        document.getElementById("fattureincloud_auto_save_nulla").checked = true;
        /*document.getElementById("fattureincloud_status_order_nulla").checked = true;*/
        document.getElementById("wfic_autocreate_nocliente").style.backgroundColor="#ff9494";
        /*document.getElementById("wfic_autocreate_stato").style.backgroundColor="#ff9494";*/
        document.getElementById("wfic_cliente_scelta").style.backgroundColor="#cbff94";
        
    }

    else {
        
        document.getElementById("wfic_cliente_scelta").style.backgroundColor="#e5e5e5";
        document.getElementById("wfic_autocreate_nocliente").style.backgroundColor="white";
        document.getElementById("wfic_autocreate_stato").style.backgroundColor="white";

    }
    

}



function funzione_02 () {

    var radio01_f2 = document.getElementById("fatturaelettronica_auto_save");
    var radio02_f2 = document.getElementById("fattura_auto_save");
    var radio03_f2 = document.getElementById("ricevuta_auto_save");


    var radio04_f2 = document.getElementById("woofic_fattura_normale_checkout");
    var radio05_f2 = document.getElementById("woofic_fattura_elettronica_checkout");
    var radio06_f2 = document.getElementById("woofic_fattura_normelettr_checkout");
    var radio07_f2 = document.getElementById("woofic_fattura_nulla_noricev_checkout");
    

    
    if ((radio01_f2.checked == true || radio02_f2.checked == true || radio03_f2.checked == true) && 
    (radio04_f2.checked == true || radio05_f2.checked == true || radio06_f2.checked == true)) {

        radio04_f2.checked = false;
        radio05_f2.checked = false;
        radio06_f2.checked = false;
        radio07_f2.checked = true;

        document.getElementById("wfic_autocreate_nocliente").style.backgroundColor="#cbff94";
        document.getElementById("wfic_autocreate_stato").style.backgroundColor="#cbff94";
        document.getElementById("wfic_cliente_scelta").style.backgroundColor="#ff9494";
          

   } else {

        document.getElementById("wfic_cliente_scelta").style.backgroundColor="#e5e5e5";
        document.getElementById("wfic_autocreate_nocliente").style.backgroundColor="white";
        document.getElementById("wfic_autocreate_stato").style.backgroundColor="white";
        

    }
    

}

function funzione_03 () {

    var radio01_f3 = document.getElementById("fatturaelettronica_auto_save");
    var radio02_f3 = document.getElementById("fattura_auto_save");
    var radio03_f3 = document.getElementById("ricevuta_auto_save");

    var radio04_f3 = document.getElementById("woofic_fattura_normale_checkout");
    var radio05_f3 = document.getElementById("woofic_fattura_elettronica_checkout");
    var radio06_f3 = document.getElementById("woofic_fattura_normelettr_checkout");
    var radio07_f3 = document.getElementById("woofic_fattura_nulla_noricev_checkout");
    
    if ((radio01_f3.checked == true || radio02_f3.checked == true || radio03_f3.checked == true) && 
    (radio04_f3.checked == true || radio05_f3.checked == true || radio06_f3.checked == true)) {

        radio04_f3.checked = false;
        radio05_f3.checked = false;
        radio06_f3.checked = false;
        radio07_f3.checked = true;

       

        document.getElementById("wfic_autocreate_nocliente").style.backgroundColor="#cbff94";
        document.getElementById("wfic_autocreate_stato").style.backgroundColor="#cbff94";
        document.getElementById("wfic_cliente_scelta").style.backgroundColor="#ff9494";

        
    } else {

        document.getElementById("wfic_cliente_scelta").style.backgroundColor="#e5e5e5";
        document.getElementById("wfic_autocreate_nocliente").style.backgroundColor="white";
        document.getElementById("wfic_autocreate_stato").style.backgroundColor="white";
        

    }
    

}



function check_ita_field () {
    var checkbox = document.getElementById('fattureincloud_partiva_codfisc');
    if (checkbox.checked == false) {
                
        document.getElementById("wfic_voci_chkout").style.backgroundColor="#ff9494";

        jQuery(".messaggio_disattiv").append('<h3>Questa funzionalità è necessaria al funzionamento del plugin WFP</h3>');


       
    } else {

        document.getElementById("wfic_voci_chkout").style.backgroundColor="#cbff94";

        jQuery( ".messaggio_disattiv" ).empty();
    }
}


function funzione_16_a() {

    var chk_simple = document.getElementById("woo_fic_cf_chk");
    var chk_hard = document.getElementById("woo_fic_cf_chk_hard");
    
        if (chk_simple.checked) {
        
            chk_hard.checked = false;
    
        }
    
}
        
function funzione_16_b() {
        
    var chk_hard = document.getElementById("woo_fic_cf_chk_hard");
    var chk_simple = document.getElementById("woo_fic_cf_chk");
    
    if (chk_hard.checked) {
    
        chk_simple.checked = false;
            
            
    }
    
}

