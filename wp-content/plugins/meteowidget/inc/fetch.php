<?php
// Recupera i parametri dalle opzioni di WordPress
$pass = get_option('meteofetcher_pass');
$user = get_option('meteofetcher_user');
$apiToken = get_option('meteofetcher_api_token');

// Costruisci l'URL dell'API con i parametri dinamici
$apiUrl = sprintf(
    'https://api.weatherlink.com/v1/NoaaExt.json?user=%s&pass=%s&apiToken=%s',
    urlencode($user), // Protegge eventuali caratteri speciali
    urlencode($pass),
    urlencode($apiToken)
);

// Effettua la richiesta API
$json = file_get_contents($apiUrl);

if ($json === false) {
    error_log('Impossibile recuperare i dati dall\'API Weatherlink.');
    return; // Esci se non riesce a recuperare i dati
}

$data = json_decode($json, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    error_log('Errore nel decodificare la risposta JSON dell\'API: ' . json_last_error_msg());
    return; // Esci se c'è un errore nel JSON
}

$current = $data['davis_current_observation'] ?? null;

if (!$current) {
    error_log('Dati correnti mancanti nella risposta dell\'API.');
    return; // Esci se i dati sono nulli o incompleti
}

$stationName = $current['station_name'];
$location = $data['location'];
$dateUnform = $data['observation_time_rfc822'];

$mysqldate = date("Y-m-d H:i:s", strtotime($dateUnform . '+1 hours'));

// Verifica i dati prima dell'inserimento
if (empty($mysqldate) || empty($stationName)) {
    error_log('Alcuni campi obbligatori sono vuoti.');
    return; // Esci se ci sono dati mancanti
}

$currentTime = (new DateTime())->modify('+2 hours');
$startTime = new DateTime('07:00');
$endTime = new DateTime('18:59');

$temperatura = $data['temp_c'];
$massima = fahToCel($current['temp_day_high_f']);
$massimaTempo = $current['temp_day_high_time'];
$minima = fahToCel($current['temp_day_low_f']);
$minimaTempo = $current['temp_day_low_time'];
$massimaTempo24 = date("H:i", strtotime($massimaTempo));
$minimaTempo24 = date("H:i", strtotime($minimaTempo));

$alba = $current['sunrise'];
$tramonto = $current['sunset'];

$alba24 = date("H:i", strtotime($alba));
$tramonto24 = date("H:i", strtotime($tramonto));

$velocitaVento = mphToKmh($data['wind_mph']);
$direzioneVento = $data['wind_dir'];
$gradiVento = $data['wind_degrees'];

$umidita = $data['relative_humidity'];
$pioggia = inchToMm($current['rain_rate_in_per_hr']);
$pioggiagiorno = inchToMm($current['rain_day_in']);
$pressione = $data['pressure_mb'];
$rugiada = $data['dewpoint_c'];
$percepita = $data['heat_index_c'];

// Funzioni di conversione
function fahToCel($float) {
    return round((($float - 32) / 1.8), 1);
}

function mphToKmh($float) {
    return round(($float * 1.609), 1);
}

function inchToMm($float) {
    return round(($float * 2.54 * 10), 2);
}

global $wpdb;

// Inserisce i dati nel database
$wpdb->insert($wpdb->prefix . 'meteofetcherBE', array(
    "mysqldate" => $mysqldate,
    "dateUnform" => $dateUnform,
    "stationName" => $stationName,
    "location" => $location,
    "direzionevento" => $direzioneVento,
    "temperatura" => $temperatura,
    "massima" => $massima,
    "minima" => $minima,
    "minimatempo" => $minimaTempo,
    "massimatempo" => $massimaTempo,
    "massimatempo24" => $massimaTempo24,
    "minimatempo24" => $minimaTempo24,
    "alba" => $alba,
    "tramonto" => $tramonto,
    "alba24" => $alba24,
    "tramonto24" => $tramonto24,
    "velocitavento" => $velocitaVento,
    "gradivento" => $gradiVento,
    "pioggia" => $pioggia,
    "pioggiagiorno" => $pioggiagiorno,
    "pressione" => $pressione,
    "rugiada" => $rugiada,
    "percepita" => $percepita,
    "umidita" => $umidita
));

// Elimina i dati più vecchi di 24 ore
$wpdb->query("DELETE FROM `{$wpdb->prefix}meteofetcherBE` WHERE fetchtime < (NOW() - INTERVAL 24 HOUR)");