/* kudos to grazie a gbresci */

function validaCodiceFiscale(cf)
 {
     var validi, i, s, set1, set2, setpari, setdisp;
     if( cf == '' )  return '';
     cf = cf.toUpperCase();
     if( cf.length != 16 )
         return false;
     validi = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
     for( i = 0; i < 16; i++ ){
         if( validi.indexOf( cf.charAt(i) ) == -1 )
             return false;
     }
     set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
     set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
     setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
     setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
     s = 0;
     for( i = 1; i <= 13; i += 2 )
         s += setpari.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
     for( i = 0; i <= 14; i += 2 )
         s += setdisp.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
     if( s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0) )
         return false;
     return true;
 }


 var b_cod_exist = document.getElementById('billing_cod_fisc');

 if(b_cod_exist){

 document.getElementById('billing_cod_fisc').onchange = function () {

    var cf = document.getElementById("billing_cod_fisc").value;

    if(validaCodiceFiscale(cf)==true)
   {

    
    jQuery("#alert_wfic_cf").remove();
    jQuery('#billing_cod_fisc_field').append("<p id='alert_wfic_cf'>codice fiscale <b><span style='color:green'>formalmente valido</span></b></p>");
    setTimeout(function(){
        jQuery('#alert_wfic_cf').remove();
    }, 3000);
    //setTimeout(function() { alert('Codice Fiscale '+cf+' formalmente valido'); }, 2000);
       }
   else
   {

    jQuery("#alert_wfic_cf").remove();
    jQuery('#billing_cod_fisc_field').append("<p id='alert_wfic_cf'>codice fiscale formalmente <b><span style='color:red'>NON valido</span></b></p>");
    setTimeout(function(){
        jQuery('#alert_wfic_cf').remove();
    }, 3000);
    //setTimeout(function() { alert('Codice Fiscale '+cf+' formalmente NON valido'); }, 2000);
   }

}

};




var p_iva_exist = document.getElementById('billing_partita_iva');

 if(p_iva_exist){

 document.getElementById('billing_partita_iva').onchange = function () {

    var pi = document.getElementById("billing_partita_iva").value;

    if(pi) { 

    if( pi == '' )  return '';
    if( pi.length != 11 )
            alert("La lunghezza della partita IVA non è\n" +
            "corretta: la partita IVA dovrebbe essere lunga\n" +
            "esattamente 11 caratteri.\n");


    validi = "0123456789";
    for( i = 0; i < 11; i++ ){
        if( validi.indexOf( pi.charAt(i) ) == -1 )
        alert("La partita IVA contiene un carattere non valido `" +
                pi.charAt(i) + "'.\nI caratteri validi sono numeri.\n");
    }
    s = 0;
    for( i = 0; i <= 9; i += 2 )
        s += pi.charCodeAt(i) - '0'.charCodeAt(0);
    for( i = 1; i <= 9; i += 2 ){
        c = 2*( pi.charCodeAt(i) - '0'.charCodeAt(0) );
        if( c > 9 )  c = c - 9;
        s += c;
    }
    if( ( 10 - s%10 )%10 != pi.charCodeAt(10) - '0'.charCodeAt(0) )
    alert("La partita IVA non è valida:\n" +
            "il codice di controllo non corrisponde.\n");
    return '';

} }

}
