<?php

//Validazione dei casi
$caso1 = $caso2 = $caso3 = $caso4 = $caso5 = $caso6 = $caso7 = false;
$consiglio;
$dir;
$isDayTime = false;
$isRaining = false;
$isChill = false;
$dayTimeCheck = false;
$caso1 = validaCaso(0, 10, $temperatura, 0, 20, $velocitaVento, 980, 1010, $pressione);
$caso2 = validaCaso(10, 15, $temperatura, 0, 20, $velocitaVento, 980, 1010, $pressione);
$caso3 = validaCaso(15, 20, $temperatura, 0, 20, $velocitaVento, 1000, 1020, $pressione);
$caso4 = validaCaso(20, 25, $temperatura, 0, 50, $velocitaVento, 1000, 1040, $pressione);
$caso5 = validaCaso(25, 30, $temperatura, 0, 50, $velocitaVento, 1000, 1040, $pressione);
$caso6 = validaCaso(30, 35, $temperatura, 0, 50, $velocitaVento, 1000, 1040, $pressione);
$caso7 = validaCaso(35, 40, $temperatura, 0, 50, $velocitaVento, 1000, 1040, $pressione);
//debug su casi: almeno un caso deve essere valido, altrimenti sputa l'errrore

if (!($caso1 || $caso2 || $caso3 || $caso4 || $caso5 || $caso6 || $caso7)) {
    //echo "nessun caso validato!!!";
    $caso1 = True;
}

//Logica delle icone

if ($colore == "bianco") {
    $baseicon = "/blue";
} else {
    $baseicon = "/white";
}

$cappello = '/img' . $baseicon . '/cappello.png';
$vestito = '/img' . $baseicon . '/maxigonna.png';
$short = '/img' . $baseicon . '/short.png';
$occhiali = '/img' . $baseicon . '/occhiali.png';

$camicia = '/img' . $baseicon . '/camicia.png';
$jeans = '/img' . $baseicon . '/jeans.png';
$sandali = '/img' . $baseicon . '/sandali.png';
$acqua = '/img' . $baseicon . '/bottiglia.png';

$maglialana = '/img' . $baseicon . '/magliaLana.png';
$decollete = '/img' . $baseicon . '/decollete.png';
$ombrello = '/img' . $baseicon . '/ombrello.png';

$cappelloInv = '/img' . $baseicon . '/cappelloinv.png';
$giubbotto = '/img' . $baseicon . '/giubbotto.png';
$sciarpa = '/img' . $baseicon . '/sciarpa.png';
$stivali = '/img' . $baseicon . '/stivali.png';

$tshirt = '/img' . $baseicon . '/tshirt.png';
$sneakers = '/img' . $baseicon . '/sneakers.png';
$cardigan = '/img' . $baseicon . '/cardigan.png';


$dayTimeCheck = checkDayTime($currentTime, $startTime, $endTime);
$isRaining = checkIsRaining($pioggia);
$isWindy = checkIsWindy($velocitaVento);
$isChill = checkIsChill($temperatura);


if ($dayTimeCheck) {
    $pathCaso = plugin_dir_path(__FILE__) . 'casi/giorno/';
    $pathConsigli = plugin_dir_path(__FILE__) . 'consigli/giorno/';
    $ic1 = array($cappelloInv, $giubbotto, $sciarpa, $stivali);
    $ic2 = array($maglialana, $stivali, $occhiali, $jeans);
    $ic3 = array($camicia, $decollete, $occhiali, $acqua);
    $ic4 = array($camicia, $tshirt, $sneakers, $cardigan);
    $ic5 = array($occhiali, $camicia, $sneakers, $sandali);
    $ic6 = array($vestito, $short, $cappello, $occhiali);
    $ic7 = array($cappello, $sandali, $short, $occhiali);
} else {
    $pathCaso = plugin_dir_path(__FILE__) . 'casi/notte/';
    $pathConsigli = plugin_dir_path(__FILE__) . 'consigli/notte/';
    $ic1 = array($cappelloInv, $giubbotto, $sciarpa, $stivali);
    $ic2 = array($maglialana, $camicia, $stivali, $jeans);
    $ic3 = array($camicia, $decollete, $occhiali, $acqua);
    $ic4 = array($camicia, $tshirt, $sneakers, $cardigan);
    $ic5 = array($occhiali, $camicia, $sneakers, $sandali);
    $ic6 = array($vestito, $short, $cappello, $occhiali);
    $ic7 = array($cappello, $sandali, $short, $occhiali);
}


if ($caso1) {
    $src = $pathCaso . 'caso1.txt';
    $portate = file_get_contents($src);
    $ic = $ic1;
    $dir = $pathConsigli . 'caso1/';
}
if ($caso2) {
    $src = $pathCaso . 'caso2.txt';
    $portate = file_get_contents($src);
    $ic = $ic2;
    $dir = $pathConsigli . 'caso2/';
}
if ($caso3) {
    $src = $pathCaso . 'caso3.txt';
    $portate = file_get_contents($src);
    $ic = $ic3;
    $dir = $pathConsigli . 'caso3/';
}
if ($caso4) {
    $src = $pathCaso . 'caso4.txt';
    $portate = file_get_contents($src);
    $ic = $ic4;
    $dir = $pathConsigli . 'caso4/';
}
if ($caso5) {
    $src = $pathCaso . 'caso5.txt';
    $portate = file_get_contents($src);
    $ic = $ic5;
    $dir = $pathConsigli . 'caso5/';
}
if ($caso6) {
    $src = $pathCaso . 'caso6.txt';
    $portate = file_get_contents($src);
    $ic = $ic6;
    $dir = $pathConsigli . 'caso6/';
}
if ($caso7) {
    $src = $pathCaso . 'caso7.txt';
    $portate = file_get_contents($src);
    $ic = $ic7;
    $dir = $pathConsigli . 'caso7/';
}


?>
