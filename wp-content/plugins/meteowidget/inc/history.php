<?php

$MaxPioggiaH;
$MaxVentoH;
$MaxCaldoH;
$allertarecord = 0;

global $wpdb;

$resultMaxPioggia = $wpdb->get_results("
    SELECT MAX(pioggia)
    AS MaxPioggiaH
    FROM  `{$wpdb->prefix}meteofetcherBE`
    WHERE fetchtime > (NOW() - INTERVAL 1 HOUR)
    ");
foreach ($resultMaxPioggia as $value) {
    $MaxPioggiaH = $value->MaxPioggiaH;
}


$resultMaxVento = $wpdb->get_results("
    SELECT MAX(velocitavento)
    AS MaxVentoH
    FROM  `{$wpdb->prefix}meteofetcherBE`
    WHERE fetchtime > (NOW() - INTERVAL 1 HOUR)
    ");
foreach ($resultMaxVento as $value) {
    $MaxVentoH = $value->MaxVentoH;
}

$resultMaxCaldo = $wpdb->get_results("
    SELECT MAX(percepita)
    AS MaxCaldoH
    FROM  `{$wpdb->prefix}meteofetcherBE`
    WHERE fetchtime > (NOW() - INTERVAL 1 HOUR)
    ");
foreach ($resultMaxCaldo as $value) {
    $MaxCaldoH = $value->MaxCaldoH;
}


//$MaxVentoH = 30;

switch (true) {
    case $MaxVentoH >= 60:
        $allertaVento = 4;
        break;

    case $MaxVentoH >= 50:
        $allertaVento = 3;
        break;

    case $MaxVentoH >= 40:
        $allertaVento = 2;
        break;

    case $MaxVentoH >= 30:
        $allertaVento = 1;
        break;

    default:
        $allertaVento = 0;
        break;
}


switch (true) {
    case $MaxPioggiaH >= 60:
        $allertaPioggia = 4;
        break;

    case $MaxPioggiaH >= 50:
        $allertaPioggia = 3;
        break;

    case $MaxPioggiaH >= 40:
        $allertaPioggia = 2;
        break;

    case $MaxPioggiaH >= 30:
        $allertaPioggia = 1;
        break;

    default:
        $allertaPioggia = 0;
        break;
}


switch (true) {
    case $MaxCaldoH >= 40:
        $allertaCaldo = 4;
        break;

    case $MaxCaldoH >= 40:
        $allertaCaldo = 3;
        break;

    case $MaxCaldoH >= 35:
        $allertaCaldo = 2;
        break;

    case $MaxCaldoH >= 26:
        $allertaCaldo = 1;
        break;

    default:
        $allertaCaldo = 0;
        break;
}


function compareAlertHistory($a, $b, $c)
{
    $max = max($a, $b, $c);
    switch ($max) {
        case 4:
            $allertarecord = "allertarossa";
            break;
        case 3:
            $allertarecord = "allertaarancione";
            break;
        case 2:
            $allertarecord = "allertagialla";
            break;
        case 1:
            $allertarecord = "allertaverde";
            break;
        case 0:
            $allertarecord = "nessunaallerta";
            break;

    }

    return $allertarecord;
}

$allertarecord = compareAlertHistory($allertaVento, $allertaPioggia, $allertaCaldo);

//$allertarecord = "allertagialla";
$table_nameHI = $wpdb->prefix . 'meteofetcherHI';
$wpdb->insert("{$wpdb->prefix}meteofetcherHI", array("allertarecord" => $allertarecord));
$wpdb->query("DELETE FROM `{$wpdb->prefix}meteofetcherHI` WHERE fetchtime < (NOW() - INTERVAL 24 HOUR);");
error_log('Cron girato: ' . json_encode($allertarecord));