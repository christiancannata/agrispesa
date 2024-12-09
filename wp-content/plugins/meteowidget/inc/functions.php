<?php


$lastUpdateDay = date("d/m/Y", strtotime($dateUnform));
$lastUpdateHour = date("H:i:s", strtotime($dateUnform . '+1 hours'));

function random_consiglio($dir)
{
    $files = glob($dir . '*.*');
    $file = array_rand($files);
    $consiglio = file_get_contents($files[$file]);
    echo $consiglio;
    $currentTime = (new DateTime())->modify('+1 hours');
}

function validaCaso($tempMin, $tempMax, $temperatura, $ventMin, $ventMax, $velocitaVento, $pressMin, $pressMax, $pressione)
{
    if (($temperatura > $tempMin && $temperatura <= $tempMax) && ($velocitaVento > $ventMin && $velocitaVento <= $ventMax) && ($pressione > $pressMin && $pressione <= $pressMax)) {
        return true;
    }
}

function ciclaCaso($gruppoIcone)
{
    foreach ($gruppoIcone as $icona) {
        echo '<div class="col-lg-3 col-md-3 col-3"><img src="';
        echo plugin_dir_url(dirname(__FILE__)) . $icona;
        echo '"></div>';
    }
}

$displayDate = $currentTime->format('H:i');

function checkDayTime($currentTime, $startTime, $endTime)
{
    if ($currentTime >= $startTime && $currentTime <= $endTime) {
        return true;
    } else {
        return false;
    };
}

function checkIsRaining($pioggia)
{
    if ($pioggia >= 0.1) {
        return true;
    } else {
        return false;
    }
}

function checkIsWindy($vento)
{
    if ($vento >= 30) {
        return true;
    } else {
        return false;
    }
}



function isDaytime($latitude, $longitude)
{
    // Ottiene gli orari di alba e tramonto per la data corrente
    $sunInfo = date_sun_info(time(), $latitude, $longitude);

    // Ottiene l'ora attuale
    $currentTime = time();

    // Verifica se l'ora attuale è tra l'alba e il tramonto
    if ($currentTime >= $sunInfo['sunrise'] && $currentTime <= $sunInfo['sunset']) {
        return true; // È giorno
    } else {
        return false; // È notte
    }
}


function checkIsChill($temperatura)
{
    if ($temperatura <= 1) {
        return true;
    } else {
        return false;
    }
}

//$temperatura = -10;
//$pioggia = 50;
//$velocitaVento = 78;

//$percepita = 49;

$allerta = "";
$allertaCaldo = 0;
$allertaPioggia = 0;
$allertaVento = 0;
$MaxPioggiaH;
$alertIntro = "";
$alertmessagev = "";
$alertmessagep = "";
$alertmessagec = "";

switch (true) {
    case $velocitaVento >= 60:
        $allertaVento = 4;
        break;

    case $velocitaVento >= 50:
        $allertaVento = 3;
        break;

    case $velocitaVento >= 40:
        $allertaVento = 2;
        break;

    case $velocitaVento >= 30:
        $allertaVento = 1;
        break;

    default:
        $allertaVento = 0;
        break;
}


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
    case $percepita >= 40:
        $allertaCaldo = 4;
        break;

    case $percepita >= 35:
        $allertaCaldo = 3;
        break;

    case $percepita >= 30:
        $allertaCaldo = 2;
        break;

    case $percepita >= 26:
        $allertaCaldo = 1;
        break;

    default:
        $allertaCaldo = 0;
        break;
}


if ($allertaVento != 0) {
    switch ($allertaVento) {
        case 4:

            $alertmessagev = '<p class="alertmessage"><i class="fas fa-wind wind"></i> Possibile caduta grossi oggetti.</p>';
            break;
        case 3:

            $alertmessagev = '<p class="alertmessage"><i class="fas fa-wind wind"></i> Possibile caduta piccoli oggetti.</p';
            break;
        case 2:

            $alertmessagev = '<p class="alertmessage"><i class="fas fa-wind wind"></i> Prestare Attenzione caduta fogliame.</p>';
            break;
        case 1:
            $alertmessagev = '<p class="alertmessage"><i class="fas fa-wind wind"></i> Coprirsi bene.</p>';
            break;
    }
}


if ($allertaPioggia != 0) {
    switch ($allertaPioggia) {
        case 4:
            $alertmessagep = '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Pericolo piccoli smottamenti.</p>';
            break;
        case 3:
            $alertmessagep = '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Possibili problemi alla circolazione stradale.</p>';
            break;
        case 2:
            $alertmessagep = '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Possibili rigagnoli per le strade.</p>';
            break;
        case 1:
            $alertmessagep = '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Ombrello da non Dimenticare.</p>';
            break;
    }
}


if ($allertaCaldo != 0) {
    switch ($allertaCaldo) {
        case 4:
            $alertmessagec = '<p class="alertmessage"><i class="fas fa-sun cloud"></i>  Evitare di muoversi, rischio elevato di colpo di calore e di disidratazione.</p>';
            break;
        case 3:
            $alertmessagec = '<p class="alertmessage"><i class="fas fa-sun cloud"></i> Disagio fisico elevato, evitare tutte le attività fisiche, moderato rischio di colpo di calore e di crampi annessi.</p>';
            break;
        case 2:
            $alertmessagec = '<p class="alertmessage"><i class="fas fa-sun cloud"></i> Disagio moderato, evitare grossi sforzi.
Basso rischio di colpo di calore</p>';
            break;
        case 1:
            $alertmessagec = '<p class="alertmessage"><i class="fas fa-sun cloud"></i>  Lieve disagio.</p>';
            break;
    }
}

function compareAlert($a, $b, $c)
{
    $max = max($a, $b, $c);
    switch ($max) {
        case 4:
            $allerta = "allertarossa";
            $alertIntro = "<strong>Stato di Attenzione Rossa</strong>";
            break;
        case 3:
            $allerta = "allertaarancione";
            $alertIntro = "<strong>Stato di Attenzione Arancione</strong>";
            break;
        case 2:
            $allerta = "allertagialla";
            $alertIntro = "<strong>Stato di Attenzione Gialla</strong>";
            break;
        case 1:
            $allerta = "allertaverde";
            $alertIntro = "<strong>Stato di Attenzione Verde</strong>";
            break;
        default :
            return;

    }

    return array($allerta, $alertIntro);

}

$allerta = compareAlert($allertaVento, $allertaPioggia, $allertaCaldo)[0];
$alertIntro = compareAlert($allertaVento, $allertaPioggia, $allertaCaldo)[1];
