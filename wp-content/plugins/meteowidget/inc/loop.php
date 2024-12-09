<?php

$currentTime = (new DateTime())->modify('+1 hours');
$startTime = new DateTime('07:00');
$endTime = new DateTime('18:59');

global $wpdb;

$result = $wpdb->get_results ( "
    SELECT *
    FROM  `{$wpdb->prefix}meteofetcherBE`
    ORDER BY `fetchtime` DESC
    LIMIT 1
    " );

foreach ( $result as $value )
{
   $stationName = $value->stationName;
   $dateUnform = $value->dateunform;
   $location = $value->location;
   $alba24 = $value->alba24;
   $tramonto24 = $value->tramonto24;
   $temperatura = $value->temperatura;
   $minima = $value->minima;
   $massima = $value->massima;
   $massimaTempo24 = $value->massimatempo24;
   $minimaTempo24 = $value->minimatempo24;
   $velocitaVento = $value->velocitavento;
   $pressione = $value->pressione;
   $pioggia = $value->pioggia;
   $direzioneVento = $value->direzionevento;
   $umidita = $value->umidita;
   $rugiada = $value->rugiada;
   $pioggiagiorno = $value->pioggiagiorno;
   $percepita = $value->percepita;

}

include(MY_PLUGIN_PATH. './inc/functions.php');
include(MY_PLUGIN_PATH. './inc/logic.php');
