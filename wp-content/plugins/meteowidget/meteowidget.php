<?php
/**
 * @package Plugin Mete
 */
/*
Plugin Name: Meteo Plugin
Plugin URL: https://christiancannata.com/
Version: 1
Author: Christian Cannata
Description: Plugin per visualizzare le previsioni meteo
*/


defined('ABSPATH') or die('Hey, you can\t access this file, you silly human!');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));

class MeteoWidget
{
    public function register()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        add_action('admin_menu', array($this, 'addAdminPage'));
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('rest_api_init', array($this, 'registerApiEndpoints'));

        // Aggiungi azioni per cancellare il transient quando le opzioni vengono aggiornate
        add_action('update_option_meteofetcher_user', array($this, 'clearTransient'));
        add_action('update_option_meteofetcher_pass', array($this, 'clearTransient'));
        add_action('update_option_meteofetcher_api_token', array($this, 'clearTransient'));
    }

    public function clearTransient()
    {
        // Elimina il transient dei dati meteo
        delete_transient('meteofetcher_api_data');
    }

    public function activate()
    {
        $this->createTables();
        $this->fetchInfo();
        $this->cron_activation();

        //  $this->includes();
    }

    public function deactivate()
    {
        $this->cron_deactivation();
    }

    public function uninstall()
    {
    }

    public function enqueue()
    {
        wp_enqueue_script('fontawesome', '//kit.fontawesome.com/00dd5e1b66.js');
        wp_enqueue_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery'));
        wp_enqueue_style('prefix_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
        wp_enqueue_style('meteowidget_style', plugins_url('/assets/css/style.css', __FILE__));
    }

    public function settingsPageHtml()
    {
        // Verifica autorizzazioni utente
        if (!current_user_can('manage_options')) {
            return;
        }

        // Opzioni disponibili per le sezioni
        $availableSections = array(
            'temperatura' => 'Temperatura',
            'vento_umidita' => 'Vento e Umidità',
            'alba_tramonto' => 'Alba e Tramonto',
            'consigli' => 'Consigli',
            'fase_lunare' => 'Fase Lunare',
            'barra_stato' => 'Barra Stato',
        );

        ?>
        <div class="wrap">
            <h1>Impostazioni Meteo Fetcher</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('meteofetcher_settings');
                do_settings_sections('meteofetcher_settings');
                submit_button();
                ?>
            </form>

            <h2>Genera Shortcode</h2>
            <form id="shortcode-generator">
                <label for="columns">Numero di colonne:</label>
                <select id="columns" name="columns">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>

                <fieldset>
                    <legend>Seleziona le sezioni:</legend>
                    <?php foreach ($availableSections as $key => $label): ?>
                        <label>
                            <input type="checkbox" name="sections[]" value="<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($label); ?>
                        </label><br>
                    <?php endforeach; ?>
                </fieldset>

                <label for="widget-bg">Colore di sfondo del widget:</label>
                <input type="color" id="widget-bg" name="widget_bg" value="#e3e3e3"><br><br>

                <label for="section-bg">Colore di sfondo delle sezioni:</label>
                <input type="color" id="section-bg" name="section_bg" value="#ffffff"><br><br>

                <button type="button" onclick="generateShortcode()">Genera Shortcode</button>
            </form>

            <div id="shortcode-output">
                <h3>Shortcode generato:</h3>
                <input type="text" id="shortcode-result" readonly style="width: 100%;">
            </div>

            <script type="text/javascript">
                function generateShortcode() {
                    let columns = document.getElementById('columns').value;
                    let checkboxes = document.querySelectorAll('input[name="sections[]"]:checked');
                    let selectedSections = Array.from(checkboxes).map(cb => cb.value).join(',');

                    let widgetBg = document.getElementById('widget-bg').value;
                    let sectionBg = document.getElementById('section-bg').value;

                    let shortcode = `[meteo_widget columns="${columns}" sections="${selectedSections}" widget_bg="${widgetBg}" section_bg="${sectionBg}"]`;
                    document.getElementById('shortcode-result').value = shortcode;
                }
            </script>
        </div>
        <?php
    }

    public static function createTables()
    {
        global $wpdb;
        $sql = array();
        $table_nameBE = $wpdb->prefix . 'meteofetcherBE';
        $sql[] = "CREATE TABLE $table_nameBE (
		        id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
            fetchtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            dateunform varchar(50) NOT NULL,
            mysqldate DATETIME NOT NULL,
            stationName varchar(50) NOT NULL,
            location varchar(50) NOT NULL,
            direzionevento varchar(50) NOT NULL,
            temperatura FLOAT NOT NULL,
            massima FLOAT NOT NULL,
            minima FLOAT NOT NULL,
            minimatempo varchar(50) NOT NULL,
            massimatempo varchar(50) NOT NULL,
            massimatempo24 varchar(50) NOT NULL,
            minimatempo24 varchar(50) NOT NULL,
            alba varchar(50) NOT NULL,
            tramonto varchar(50) NOT NULL,
            alba24 varchar(50) NOT NULL,
            tramonto24 varchar(50) NOT NULL,
            umidita varchar(50) NOT NULL,
            velocitavento FLOAT NOT NULL,
            gradivento FLOAT NOT NULL,
            pioggia FLOAT NOT NULL,
            pioggiagiorno FLOAT NOT NULL,
            pressione FLOAT NOT NULL,
            rugiada FLOAT NOT NULL,
            percepita FLOAT NOT NULL,
            PRIMARY KEY  (id)
		        );";

        $table_nameHI = $wpdb->prefix . 'meteofetcherHI';
        $sql[] = "CREATE TABLE $table_nameHI (
                id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
                fetchtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                allertarecord varchar(50) NOT NULL,
                                PRIMARY KEY  (id)
                );";


        if (!empty($sql)) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            dbDelta($sql);
        }
    }

    public function includes()
    {

        //include(dirname(__FILE__) . '/inc/functions.php');
        //include(dirname(__FILE__) . '/inc/logic.php');
    }

    public static function makeHistory()
    {
        include(dirname(__FILE__) . '/inc/history.php');
    }

    public static function fetchInfo()
    {
        include(dirname(__FILE__) . '/inc/fetch.php');
    }


    public function addAdminPage()
    {
        add_options_page(
            'Impostazioni Meteo Fetcher',
            'Meteo Fetcher',
            'manage_options',
            'meteofetcher_settings',
            array($this, 'settingsPageHtml')
        );
    }

    public function registerSettings()
    {
        register_setting('meteofetcher_settings', 'meteofetcher_user');
        register_setting('meteofetcher_settings', 'meteofetcher_pass');
        register_setting('meteofetcher_settings', 'meteofetcher_api_token');

        add_settings_section('meteofetcher_main_section', 'Credenziali API', null, 'meteofetcher_settings');

        add_settings_field(
            'meteofetcher_user',
            'User',
            array($this, 'userFieldHtml'),
            'meteofetcher_settings',
            'meteofetcher_main_section'
        );
        add_settings_field(
            'meteofetcher_pass',
            'Password',
            array($this, 'passFieldHtml'),
            'meteofetcher_settings',
            'meteofetcher_main_section'
        );
        add_settings_field(
            'meteofetcher_api_token',
            'API Token',
            array($this, 'apiTokenFieldHtml'),
            'meteofetcher_settings',
            'meteofetcher_main_section'
        );
    }

    public function userFieldHtml()
    {
        $user = get_option('meteofetcher_user');
        echo '<input type="text" name="meteofetcher_user" value="' . esc_attr($user) . '" />';
    }

    public function passFieldHtml()
    {
        $pass = get_option('meteofetcher_pass');
        echo '<input type="text" name="meteofetcher_pass" value="' . esc_attr($pass) . '" />';
    }

    public function apiTokenFieldHtml()
    {
        $apiToken = get_option('meteofetcher_api_token');
        echo '<input type="text" name="meteofetcher_api_token" value="' . esc_attr($apiToken) . '" />';
    }

    public static function fetchInfoFromApi()
    {

        $pass = get_option('meteofetcher_pass');
        $user = get_option('meteofetcher_user');
        $apiToken = get_option('meteofetcher_api_token');

// Prova a recuperare i dati dalla cache
        $cachedData = get_transient('meteofetcher_api_data');
        if ($cachedData !== false) {
            return $cachedData; // Restituisci i dati dalla cache se disponibili
        }

// Costruisci la URL con i parametri dinamici
        $apiUrl = sprintf(
            'https://api.weatherlink.com/v1/NoaaExt.json?user=%s&pass=%s&apiToken=%s',
            urlencode($user), // Protegge eventuali caratteri speciali
            urlencode($pass),
            urlencode($apiToken)
        );

// Chiamata all'API
        $response = wp_remote_get($apiUrl);
        if (is_wp_error($response)) {
            error_log('Errore nella richiesta all\'API Weatherlink: ' . $response->get_error_message());
            return null;
        }


        // Parsing della risposta
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || $data === null) {
            error_log('Errore nel decodificare la risposta JSON dell\'API: ' . json_last_error_msg());
            return null;
        }

        $current = $data['davis_current_observation'] ?? null;
        if (!$current) {
            error_log('Dati correnti mancanti nella risposta dell\'API.');
            return null;
        }

        // Estrai i dati
        $weatherData = self::extractWeatherData($data, $current);

        // Logica di validazione dei casi
        $selectedCase = self::validateCases($weatherData['temperatura'], $weatherData['velocitaVento'], $weatherData['pressione']);

        // Calcolo di altre proprietà
        $weatherData['is_day'] = self::isDaytime($weatherData['alba24'], $weatherData['tramonto24']); // Coordinate di Roma
        $weatherData['is_raining'] = self::checkIsRaining($weatherData['pioggia']);
        $weatherData['is_chill'] = self::checkIsChill($weatherData['temperatura']);
        $weatherData['is_windy'] = self::checkIsWindy($weatherData['velocitaVento']);
        $weatherData['is_sunny'] = self::checkIsSunny($weatherData['is_day'], $weatherData['pioggia'], $weatherData['velocitaVento']);

        $baseDir = plugin_dir_url(__DIR__) . '/meteowidget/img/icons';

        $weatherData['ombrello'] = '';
        if ($weatherData['is_raining']) {
            $weatherData['ombrello'] = "<div><img src='{$baseDir}/ombrello.png' lazy ></div><div><span>Non dimenticare l'ombrello</span></div>";
        }


        $iconsTemperatura = [];

        if ($weatherData['is_raining']) {
            $iconsTemperatura[] = $baseDir . '/pioggia.png';
        } else {

            if ($weatherData['is_day']) {
                $iconsTemperatura[] = $baseDir . '/sole-nuvolo.png';

            } else {
                $iconsTemperatura[] = $baseDir . '/luna-piena.png';

            }
        }

        if ($weatherData['is_windy']) {
            $iconsTemperatura[] = $baseDir . '/vento.png';
        }


        $weatherData['icons_temperatura'] = $iconsTemperatura;

        // $icons = self::getIconsForCase($selectedCase, $weatherData['is_day']);

        $weatherData['icons'] = [];

        $weatherData['consiglio'] = self::getRandomAdvice($weatherData['is_day'], $selectedCase);
        $weatherData['cosa_portare'] = self::getCosaPortare($weatherData['is_day'], $selectedCase);
        $weatherData['cosa_portare'] = array_filter(array_map('trim', explode("\n", $weatherData['cosa_portare'])));

        $selectedCase = str_replace("caso", "", $selectedCase);
        $selectedCase = intval($selectedCase);

        $weatherData['cosa_portare_icons'] = self::getCosaPortareIcons($selectedCase, $weatherData['is_day']);

        $weatherData['statoCondizioni'] = self::getStatoCondizioni();

        // Salva i dati nei transient con scadenza di 15 minuti
        set_transient('meteofetcher_api_data', $weatherData, 5 * MINUTE_IN_SECONDS);

        return $weatherData;
    }

    private static function getLatestDataFromDb()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'meteofetcherBE';

        $result = $wpdb->get_row("
        SELECT * 
        FROM {$table_name}
        ORDER BY fetchtime DESC
        LIMIT 1
    ", ARRAY_A);

        return $result ?: []; // Restituisci un array vuoto se nessun dato è presente
    }

// Funzione per estrarre i dati meteo
    private static function extractWeatherData($data, $current)
    {
        // Recupera l'ultimo dato valido dal database
        $fallbackData = self::getLatestDataFromDb();

        // Estrai e calcola i dati necessari
        $stationName = $current['station_name'] ?? $fallbackData['stationName'] ?? 'Sconosciuto';
        $location = $data['location'] ?? $fallbackData['location'] ?? 'Non specificato';
        $dateUnform = $data['observation_time_rfc822'] ?? $fallbackData['dateUnform'] ?? null;
        $mysqldate = $dateUnform ? date("Y-m-d H:i:s", strtotime($dateUnform . '+1 hours')) : ($fallbackData['mysqldate'] ?? null);

        // Conversioni con fallback
        $temperatura = $data['temp_c'] ?? $fallbackData['temperatura'] ?? null;
        $massima = isset($current['temp_day_high_f']) ? self::fahToCel($current['temp_day_high_f']) : ($fallbackData['massima'] ?? null);
        $massimaTempo = $current['temp_day_high_time'] ?? $fallbackData['massimatempo'] ?? null;
        $minima = isset($current['temp_day_low_f']) ? self::fahToCel($current['temp_day_low_f']) : ($fallbackData['minima'] ?? null);
        $minimaTempo = $current['temp_day_low_time'] ?? $fallbackData['minimatempo'] ?? null;
        $massimaTempo24 = $massimaTempo ? date("H:i", strtotime($massimaTempo)) : ($fallbackData['massimatempo24'] ?? null);
        $minimaTempo24 = $minimaTempo ? date("H:i", strtotime($minimaTempo)) : ($fallbackData['minimatempo24'] ?? null);
        $alba = $current['sunrise'] ?? $fallbackData['alba'] ?? null;
        $tramonto = $current['sunset'] ?? $fallbackData['tramonto'] ?? null;
        $alba24 = $alba ? date("H:i", strtotime($alba)) : ($fallbackData['alba24'] ?? null);
        $tramonto24 = $tramonto ? date("H:i", strtotime($tramonto)) : ($fallbackData['tramonto24'] ?? null);
        $velocitaVento = isset($data['wind_mph']) ? self::mphToKmh($data['wind_mph']) : ($fallbackData['velocitavento'] ?? null);

        // Traduzioni direzione vento
        $traduzioniDirezioneVento = [
            "North" => "Nord", "Northeast" => "Nord-Est", "East" => "Est", "Southeast" => "Sud-Est",
            "South" => "Sud", "Southwest" => "Sud-Ovest", "West" => "Ovest", "Northwest" => "Nord-Ovest",
            "North-northeast" => "Nord-Nord-Est", "East-northeast" => "Est-Nord-Est", "East-southeast" => "Est-Sud-Est",
            "South-southeast" => "Sud-Sud-Est", "South-southwest" => "Sud-Sud-Ovest", "West-southwest" => "Ovest-Sud-Ovest",
            "West-northwest" => "Ovest-Nord-Ovest", "North-northwest" => "Nord-Nord-Ovest",
        ];

        $direzioneVento = $traduzioniDirezioneVento[$data['wind_dir']] ?? $fallbackData['direzionevento'] ?? 'Sconosciuto';
        $gradiVento = $data['wind_degrees'] ?? $fallbackData['gradivento'] ?? null;
        $umidita = $data['relative_humidity'] ?? $fallbackData['umidita'] ?? null;
        $pioggia = isset($current['rain_rate_in_per_hr']) ? self::inchToMm($current['rain_rate_in_per_hr']) : ($fallbackData['pioggia'] ?? null);
        $pioggiagiorno = isset($current['rain_day_in']) ? self::inchToMm($current['rain_day_in']) : ($fallbackData['pioggiagiorno'] ?? null);
        $pressione = $data['pressure_mb'] ?? $fallbackData['pressione'] ?? null;
        $rugiada = $data['dewpoint_c'] ?? $fallbackData['rugiada'] ?? null;
        $percepita = $data['heat_index_c'] ?? $fallbackData['percepita'] ?? null;

        $faseLunare = self::calculateMoonPhase($mysqldate);

        // Preparazione degli alert
        $allertaVento = self::getAllertaVento($velocitaVento);
        $allertaPioggia = self::getAllertaPioggia();
        $allertaCaldo = self::getAllertaCaldoLevel($percepita);

        $allerta = self::compareAlert($allertaVento, $allertaPioggia, $allertaCaldo)[0] ?? '';
        $alertIntro = self::compareAlert($allertaVento, $allertaPioggia, $allertaCaldo)[1] ?? '';
        $allertaDetail = self::getAllertaDetail('caldo', $allertaCaldo)
            ?? self::getAllertaDetail('pioggia', $allertaPioggia)
            ?? self::getAllertaDetail('vento', $allertaVento)
            ?? '';

        // Formatta la data
        $dataFormatted = $dateUnform ? (new DateTime($dateUnform))->format('d-m-Y H:i') : null;

        return [
            'mysqldate' => $mysqldate, 'dateUnform' => $dateUnform, 'stationName' => $stationName,
            'allerta' => $allerta, 'allertaIntro' => $alertIntro, 'location' => $location,
            'data' => $dataFormatted, 'temperatura' => $temperatura, 'massima' => $massima,
            'allertaDetail' => $allertaDetail, 'massimaTempo' => $massimaTempo, 'massimaTempo24' => $massimaTempo24,
            'minima' => $minima, 'minimaTempo' => $minimaTempo, 'minimaTempo24' => $minimaTempo24,
            'alba' => $alba, 'faseLunare' => $faseLunare, 'tramonto' => $tramonto,
            'alba24' => $alba24, 'tramonto24' => $tramonto24, 'velocitaVento' => $velocitaVento,
            'direzioneVento' => $direzioneVento, 'gradiVento' => $gradiVento, 'umidita' => $umidita,
            'pioggia' => $pioggia, 'pioggiagiorno' => $pioggiagiorno, 'pressione' => $pressione,
            'rugiada' => $rugiada, 'percepita' => $percepita
        ];
    }


    // Metodo privato statico per calcolare il livello di allerta pioggia
    private static function getAllertaPioggiaLevel(float $maxPioggiaH): int
    {
        if ($maxPioggiaH >= 60) {
            return 4;
        } elseif ($maxPioggiaH >= 50) {
            return 3;
        } elseif ($maxPioggiaH >= 40) {
            return 2;
        } elseif ($maxPioggiaH >= 30) {
            return 1;
        }

        return 0;
    }


    private static function compareAlert($a, $b, $c)
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

    // Metodo pubblico statico per ottenere l'allerta pioggia
    public static function getAllertaPioggia(): int
    {
        global $wpdb;

        // Query per ottenere il massimo valore di pioggia dell'ultima ora
        $result = $wpdb->get_var("
            SELECT MAX(pioggia) 
            FROM `{$wpdb->prefix}meteofetcherBE` 
            WHERE fetchtime > (NOW() - INTERVAL 1 HOUR)
        ");

        $maxPioggiaH = floatval($result); // Assicurati che il risultato sia un float

        // Calcolo del livello di allerta pioggia
        return self::getAllertaPioggiaLevel($maxPioggiaH);
    }


    public static function getAllertaDetail(string $tipoAllerta, int $livelloAllerta): string
    {
        $messages = [
            'vento' => [
                4 => '<p class="alertmessage"><i class="fas fa-wind wind"></i> Possibile caduta grossi oggetti.</p>',
                3 => '<p class="alertmessage"><i class="fas fa-wind wind"></i> Possibile caduta piccoli oggetti.</p>',
                2 => '<p class="alertmessage"><i class="fas fa-wind wind"></i> Prestare Attenzione caduta fogliame.</p>',
                1 => '<p class="alertmessage"><i class="fas fa-wind wind"></i> Coprirsi bene.</p>',
            ],
            'pioggia' => [
                4 => '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Pericolo piccoli smottamenti.</p>',
                3 => '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Possibili problemi alla circolazione stradale.</p>',
                2 => '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Possibili rigagnoli per le strade.</p>',
                1 => '<p class="alertmessage"><i class="fas fa-cloud-rain cloud"></i> Ombrello da non Dimenticare.</p>',
            ],
            'caldo' => [
                4 => '<p class="alertmessage"><i class="fas fa-sun cloud"></i> Evitare di muoversi, rischio elevato di colpo di calore e di disidratazione.</p>',
                3 => '<p class="alertmessage"><i class="fas fa-sun cloud"></i> Disagio fisico elevato, evitare tutte le attività fisiche, moderato rischio di colpo di calore e di crampi annessi.</p>',
                2 => '<p class="alertmessage"><i class="fas fa-sun cloud"></i> Disagio moderato, evitare grossi sforzi. Basso rischio di colpo di calore</p>',
                1 => '<p class="alertmessage"><i class="fas fa-sun cloud"></i> Lieve disagio.</p>',
            ],
        ];

        // Controlla se il tipo di allerta e il livello sono definiti
        return $messages[$tipoAllerta][$livelloAllerta] ?? '';
    }

    private static function getAllertaVento($velocitaVento): int
    {
        if ($velocitaVento >= 60) {
            return 4;
        } elseif ($velocitaVento >= 50) {
            return 3;
        } elseif ($velocitaVento >= 40) {
            return 2;
        } elseif ($velocitaVento >= 30) {
            return 1;
        }

        return 0;
    }

    private static function calculateMoonPhase($date)
    {
        // Data di riferimento: Nuova luna il 6 gennaio 2000 alle 18:14 UTC
        $baseDate = strtotime("2000-01-06 18:14:00");
        $targetDate = strtotime($date);
        $daysSinceBase = ($targetDate - $baseDate) / (24 * 3600);

        // Durata media di un ciclo lunare
        $moonCycle = 29.53;

        // Calcolo dell'età della luna
        $age = $daysSinceBase % $moonCycle;
        if ($age < 0) {
            $age += $moonCycle; // Assicurati che l'età sia sempre positiva
        }

        // Determina la fase della luna
        if ($age < 1.84566) {
            return "luna nuova";
        } elseif ($age < 5.53699) {
            return "crescente a falce";
        } elseif ($age < 9.22833) {
            return "primo quarto";
        } elseif ($age < 12.91966) {
            return "gibbosa crescente";
        } elseif ($age < 16.611) {
            return "luna piena";
        } elseif ($age < 20.30233) {
            return "gibbosa calante";
        } elseif ($age < 23.99366) {
            return "ultimo quarto";
        } elseif ($age < 27.68499) {
            return "calante a falce";
        } else {
            return "luna nuova"; // Ciclo si ripete
        }
    }

    private static function checkIsRaining($pioggia)
    {
        if ($pioggia >= 0.1) {
            return true;
        } else {
            return false;
        }
    }

    private static function checkIsChill($temperatura)
    {
        if ($temperatura <= 1) {
            return true;
        } else {
            return false;
        }
    }

    private static function checkIsWindy($vento)
    {
        if ($vento >= 30) {
            return true;
        } else {
            return false;
        }
    }

    private static function checkIsSunny($isDaytime, $pioggia, $vento)
    {
        // Controlla che sia giorno, non piova e il vento sia moderato
        if ($isDaytime && $pioggia < 0.1 && $vento < 30) {
            return true; // È soleggiato
        } else {
            return false; // Non è soleggiato
        }
    }


    private static function isDaytime($alba, $tramonto)
    {

        // Ottieni l'ora attuale come timestamp
        $currentTime = time();

        // Converte alba e tramonto in timestamp, se non lo sono già
        $albaTimestamp = strtotime($alba);
        $tramontoTimestamp = strtotime($tramonto);

        // Verifica se l'ora attuale è tra l'alba e il tramonto
        if ($currentTime >= $albaTimestamp && $currentTime <= $tramontoTimestamp) {
            return true; // È giorno
        } else {
            return false; // È notte
        }
    }

// Funzione per validare i casi in modo più compatto
    private static function validateCases($temperatura, $velocitaVento, $pressione)
    {
        // Struttura dell'array $ranges:
        // 'nomeCaso' => [tempMin, tempMax, ventMin, ventMax, pressMin, pressMax]
        $ranges = [
            'caso1' => [0, 10, 0, 20, 980, 1010],  // Caso 1: temperatura tra 0 e 10, vento tra 0 e 20, pressione tra 980 e 1010
            'caso2' => [10, 15, 0, 20, 980, 1010], // Caso 2: temperatura tra 10 e 15, vento tra 0 e 20, pressione tra 980 e 1010
            'caso3' => [15, 20, 0, 20, 1000, 1020], // Caso 3: temperatura tra 15 e 20, vento tra 0 e 20, pressione tra 1000 e 1020
            'caso4' => [20, 25, 0, 50, 1000, 1040], // Caso 4: temperatura tra 20 e 25, vento tra 0 e 50, pressione tra 1000 e 1040
            'caso5' => [25, 30, 0, 50, 1000, 1040], // Caso 5: temperatura tra 25 e 30, vento tra 0 e 50, pressione tra 1000 e 1040
            'caso6' => [30, 35, 0, 50, 1000, 1040], // Caso 6: temperatura tra 30 e 35, vento tra 0 e 50, pressione tra 1000 e 1040
            'caso7' => [35, 40, 0, 50, 1000, 1040], // Caso 7: temperatura tra 35 e 40, vento tra 0 e 50, pressione tra 1000 e 1040
        ];

        // Itera sui casi e valida ciascuno
        foreach ($ranges as $key => [$tempMin, $tempMax, $ventMin, $ventMax, $pressMin, $pressMax]) {
            if (self::validaCaso($tempMin, $tempMax, $temperatura, $ventMin, $ventMax, $velocitaVento, $pressMin, $pressMax, $pressione)) {
                return $key; // Restituisci il primo caso valido
            }
        }

        return 'caso1'; // Caso predefinito se nessuno è valido
    }

// Funzione di validazione del singolo caso
    private static function validaCaso($tempMin, $tempMax, $temperatura, $ventMin, $ventMax, $velocitaVento, $pressMin, $pressMax, $pressione)
    {
        return ($temperatura > $tempMin && $temperatura <= $tempMax) &&
            ($velocitaVento > $ventMin && $velocitaVento <= $ventMax) &&
            ($pressione > $pressMin && $pressione <= $pressMax);
    }

// Funzione per ottenere icone in base al caso
    private static function getIconsForCase($case, $isDay)
    {
        $iconSets = [
            'caso1' => ['icon1.png', 'icon2.png'],
            'caso2' => ['icon3.png', 'icon4.png'],
            // Definisci altri set di icone per i vari casi
        ];
        return $isDay ? $iconSets[$case] : array_map(function ($icon) {
            return 'night-' . $icon;
        }, $iconSets[$case]);
    }

// Funzione per ottenere un consiglio casuale
    private static function getRandomAdvice($isDay, $dir)
    {
        $subfolder = $isDay ? 'giorno' : 'notte';

        // Usa __DIR__ per ottenere la directory corrente del file
        $files = glob(__DIR__ . '/inc/consigli/' . $subfolder . '/' . $dir . '/*.txt');

        // Debugging: mostra il percorso completo per verificare
        // die(var_dump(__DIR__ . '/inc/consigli/' . $dir . '/*.txt'));

        if (!$files) return 'Nessun consiglio disponibile.';
        $file = $files[array_rand($files)];

        return file_get_contents($file);
    }

    private static function getStatoCondizioni()
    {
        global $wpdb;

        // Ottieni il prefisso della tabella
        $table_name = $wpdb->prefix . 'meteofetcherHI';

        // Ottieni la data di oggi (formato YYYY-MM-DD)
        $oggi = date('Y-m-d');

        // Query per ottenere l'ultimo stato di allerta per ogni ora del giorno corrente
        $query = $wpdb->prepare("
        SELECT 
            HOUR(fetchtime) AS ora, 
            allertarecord 
        FROM $table_name 
        WHERE DATE(fetchtime) = %s 
        ORDER BY fetchtime DESC
    ", $oggi);

        // Esegui la query
        $results = $wpdb->get_results($query);

        // Array per contenere l'ultimo stato di allerta per ogni ora
        $allertaPerOre = [];

        // Itera sui risultati e prendi solo l'ultimo record per ogni ora
        foreach ($results as $row) {
            $ora = intval($row->ora);
            // Se l'ora non è già presente, aggiungila con il relativo stato di allerta
            if (!isset($allertaPerOre[$ora])) {
                $allertaPerOre[$ora] = $row->allertarecord;
            }
        }

        // Genera il box HTML
        $html = '<div class="allerta-box">';

        // Cicla le 24 ore, usando lo stato di allerta se disponibile o un valore di default
        for ($ora = 0; $ora < 24; $ora++) {
            $statoAllerta = $allertaPerOre[$ora] ?? 'nessunaallerta'; // Default: nessuna allerta
            $html .= sprintf(
                '<div class="cella %s">%02d</div>',
                esc_attr($statoAllerta),
                $ora
            );
        }

        $html .= '</div>';

        return $html;
    }

// Metodo per calcolare lo stato di allerta (da implementare in base alla tua logica)
    private static function calcolaStatoAllerta($ora)
    {
        // Logica fittizia: alterna gli stati di allerta in base all'ora
        $statiPossibili = ['allertarossa', 'allertagialla', 'allertaverde', 'allertaarancione'];
        return $statiPossibili[$ora % count($statiPossibili)];
    }

    private static function getCosaPortareIcons($caso, $isDay)
    {
        // Ottieni il percorso base del plugin
        $baseDir = plugin_dir_url(__DIR__) . '/meteowidget/img/icons';

        // Definisci le icone con il percorso completo
        $cappello = $baseDir . '/cappello.png';
        $vestito = $baseDir . '/maxigonna.png';
        $short = $baseDir . '/short.png';
        $occhiali = $baseDir . '/occhiali.png';
        $camicia = $baseDir . '/camicia.png';
        $jeans = $baseDir . '/jeans.png';
        $sandali = $baseDir . '/sandali.png';
        $acqua = $baseDir . '/bottiglia.png';
        $maglialana = $baseDir . '/magliaLana.png';
        $decollete = $baseDir . '/decollete.png';
        $ombrello = $baseDir . '/ombrello.png';
        $cappelloInv = $baseDir . '/cappelloinv.png';
        $giubbotto = $baseDir . '/giubbotto.png';
        $sciarpa = $baseDir . '/sciarpa.png';
        $stivali = $baseDir . '/stivali.png';
        $tshirt = $baseDir . '/tshirt.png';
        $sneakers = $baseDir . '/sneakers.png';
        $cardigan = $baseDir . '/cardigan.png';

        // Definisci le icone da portare per ogni caso e momento della giornata
        $icons = [
            1 => [
                'DAY' => [$cappelloInv, $giubbotto, $sciarpa, $stivali],
                'NIGHT' => [$cappelloInv, $giubbotto, $sciarpa, $stivali]
            ],
            2 => [
                'DAY' => [$maglialana, $stivali, $occhiali, $jeans],
                'NIGHT' => [$maglialana, $camicia, $stivali, $jeans]
            ],
            3 => [
                'DAY' => [$camicia, $decollete, $occhiali, $acqua],
                'NIGHT' => [$camicia, $decollete, $occhiali, $acqua]
            ],
            4 => [
                'DAY' => [$camicia, $tshirt, $sneakers, $cardigan],
                'NIGHT' => [$camicia, $tshirt, $sneakers, $cardigan]
            ],
            5 => [
                'DAY' => [$occhiali, $camicia, $sneakers, $sandali],
                'NIGHT' => [$occhiali, $camicia, $sneakers, $sandali]
            ],
            6 => [
                'DAY' => [$vestito, $short, $cappello, $occhiali],
                'NIGHT' => [$vestito, $short, $cappello, $occhiali]
            ],
            7 => [
                'DAY' => [$cappello, $sandali, $short, $occhiali],
                'NIGHT' => [$cappello, $sandali, $short, $occhiali]
            ]
        ];

        // Verifica se il caso è definito, altrimenti restituisci un array vuoto
        if (!isset($icons[$caso])) {
            return [];
        }

        // Restituisci le icone per il giorno o la notte in base a `$isDay`
        return $isDay ? $icons[$caso]['DAY'] : $icons[$caso]['NIGHT'];
    }

    private static function getAllertaCaldoLevel($percepita): int
    {
        if ($percepita >= 40) {
            return 4;
        } elseif ($percepita >= 35) {
            return 3;
        } elseif ($percepita >= 30) {
            return 2;
        } elseif ($percepita >= 26) {
            return 1;
        }

        return 0;
    }


    private static function getCosaPortare($isDay, $dir)
    {
        $subfolder = $isDay ? 'giorno' : 'notte';
        $filePath = __DIR__ . '/inc/casi/' . $subfolder . '/' . $dir . '.txt';

        // Controlla se il file esiste, altrimenti restituisci una stringa vuota
        if (!file_exists($filePath)) {
            return '';
        }

        return file_get_contents($filePath);
    }


    private static function fahToCel($float)
    {
        return round((($float - 32) / 1.8), 1);
    }

    private static function mphToKmh($float)
    {
        return round(($float * 1.609), 1);
    }

    private static function inchToMm($float)
    {
        return round(($float * 2.54 * 10), 2);
    }

    public function cron_activation()
    {
        if (!wp_next_scheduled('cronFetch')) {
            wp_schedule_event(time(), 'five_minutes', 'cronFetch');
            error_log('cronFetch registrato con successo');
        } else {
            error_log('cronFetch è già registrato');
        }

        if (!wp_next_scheduled('triggerHistory')) {
            wp_schedule_event(time(), 'hourly', 'triggerHistory');
            error_log('triggerHistory registrato con successo');
        } else {
            error_log('triggerHistory è già registrato');
        }
    }

    public function cron_deactivation()
    {
        // Rimuovi i cron job registrati
        $fetchTimestamp = wp_next_scheduled('cronFetch');
        if ($fetchTimestamp) {
            wp_unschedule_event($fetchTimestamp, 'cronFetch');
        }

        $historyTimestamp = wp_next_scheduled('triggerHistory');
        if ($historyTimestamp) {
            wp_unschedule_event($historyTimestamp, 'triggerHistory');
        }
    }


    public function registerApiEndpoints()
    {
        register_rest_route('meteofetcher/v1', '/weather-data', array(
            'methods' => 'GET',
            'callback' => array($this, 'getWeatherData')
        ));
    }

    public function getWeatherData()
    {
        // Recupera i dati meteo utilizzando il metodo fetchInfoFromApi
        $weatherData = self::fetchInfoFromApi();

        // Verifica se i dati sono stati recuperati correttamente
        if (is_null($weatherData)) {
            return new WP_Error(
                'api_error',
                'Impossibile recuperare i dati meteo.',
                array('status' => 500)
            );
        }

        // Restituisci i dati meteo in formato JSON
        return rest_ensure_response($weatherData);
    }


}


//FINE CLASSE

//INIZIO FUNZIONI


if(!function_exists('five_minutes')){
    function five_minutes($schedules)
    {
        $schedules['five_minutes'] = array(
            'interval' => 300,
            'display' => __('Ogni cinque minuti')
        );
        return $schedules;
    }

}


if (class_exists('MeteoWidget')) {
    $meteoFetcherWidget = new MeteoWidget();
    $meteoFetcherWidget->register();
}

function meteo_parser_new($atts = array())
{
    // Attributi di default per lo shortcode
    $args = shortcode_atts(
        array(
            'columns' => 1, // Numero di colonne (default 1)
            'sections' => 'temperatura,vento_umidita,alba_tramonto,consigli,fase_lunare,barra_stato', // Sezioni abilitate di default
            'widget_bg' => '#e3e3e3', // Sfondo predefinito per il widget
            'section_bg' => '#ffffff' // Sfondo predefinito per le sezioni
        ),
        $atts
    );

    $unique_id = 'meteo-widget-' . uniqid();
    $columns = intval($args['columns']); // Converte in intero il numero di colonne
    $sections = explode(',', $args['sections']); // Converte le sezioni in array
    $widgetBg = esc_attr($args['widget_bg']);
    $sectionBg = esc_attr($args['section_bg']);
    $baseDir = plugin_dir_url(__FILE__) . 'img/icons';

    ob_start();
    ?>
    <style>

        <?php echo esc_attr("#".$unique_id); ?>
        {
            background:
        <?php echo $widgetBg; ?>
        ;
            padding: 10px
        ;
            border-radius: 10px
        ;
            display: grid
        ;
            gap: 10px
        ;
        }


        /* Layout per desktop */
        @media (min-width: 769px) {
        <?php echo esc_attr("#".$unique_id); ?> {
            grid-template-columns: repeat(<?php echo $columns; ?>, 1fr);
        }
        }

        /* Layout per tablet */
        @media (max-width: 768px) {
        <?php echo esc_attr("#".$unique_id); ?> {
            grid-template-columns: repeat(<?php echo min($columns, 2); ?>, 1fr);
        }
        }

        /* Layout per mobile */
        @media (max-width: 480px) {
        <?php echo esc_attr("#".$unique_id); ?> {
            grid-template-columns: repeat(<?php echo ($columns >= 3) ? 2 : $columns; ?>, 1fr);
        }
        }


        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-section {
            background: <?php echo $sectionBg; ?>;
            padding: 10px;
            border-radius: 10px;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-section h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px !important;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-section p {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 16px !important;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-section .temperatura-big {
            text-align: center !important;
            font-size: 32px !important;
            font-weight: bold !important;
            display: flex;
            margin-bottom: 20px;
        }

        @media (max-width: 500px) {
        <?php echo esc_attr("#".$unique_id); ?> .meteo-section .temperatura-big {
            flex-direction: column; /* Disposizione in colonna su schermi piccoli */
            align-items: center; /* Centra gli elementi */
        }

        <?php echo esc_attr("#".$unique_id); ?>
            .temperatura-title, <?php echo esc_attr("#".$unique_id); ?> .temperatura-icons {
                width: 100% !important;
                text-align: center;
                justify-content: center;

            }

        }

        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-section h3 {
            text-align: center !important;
        }


        @media (max-width: 480px) {
        <?php echo esc_attr("#".$unique_id); ?> .full-width {
            grid-column: span <?php echo ($columns >= 3) ? 2 : $columns; ?> !important;
        }
        }

        @media (min-width: 500px) {
        <?php echo esc_attr("#".$unique_id); ?> .full-width {
            grid-column: span <?php echo $columns; ?> !important;
        }
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .cosa-portare-container {
            display: block;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .column-icons {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap; /* Permette agli elementi di andare a capo */
        }

        .column-texts {
            flex: 1; /* Occupa tutto lo spazio rimanente */
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .cosa-portare-icon {
            width: 100%; /* Dimensione dell'icona */
            max-width: 50px;
            height: auto;
            margin: 0px !important;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .temperatura-title, <?php echo esc_attr("#".$unique_id); ?> .temperatura-icons {

        }

        .temperatura-title {
            margin-right: 15px;
        }

        .small-text {
            font-size: 10px;
            line-height: 10px;
        }

        .alba-tramonto-container {
            text-align: center;
        }

        .temperatura-title {
            text-align: left;
        }

        .temperatura-icons {
            display: flex;
        }

        .meteo-ombrello {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .meteo-ombrello img {
            margin: 0 auto;
            width: 35px !important;
        }

        .alba-tramonto-icon {
            width: 80%;
            margin: 0 auto !important;
        }

        .temperatura-icons img {
            width: 48px;
            margin: 0px !important;
        }

        .alba-tramonto-wrapper {
            display: flex;
        }

        .cella {
            padding: 5px;
            font-size: 10px;
            text-align: center;
        }

        @media (max-width: 650px) {
            .alba-tramonto-wrapper {
                flex-direction: column; /* Disposizione in colonna su schermi piccoli */
                align-items: center; /* Centra gli elementi */
                gap: 10px; /* Riduce lo spazio tra gli elementi */
            }
        }


        <?php echo esc_attr("#".$unique_id); ?>
        .column-texts p {
            margin: 0 0 10px;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-info {
            display: flex;
            align-items: center; /* Allinea l'icona e il testo verticalmente al centro */
            gap: 8px; /* Spazio tra l'icona e il testo */
        }

        .meteo-allerta-detail {
            text-align: center;
            padding-top: 5px;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }

        .meteo-allerta {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: white;
            margin-top: 10px;
            padding-bottom: 10px;
        }

        .allerta-box {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            gap: 0px;
            margin-top: 10px;
        }


        /* Media query per dispositivi mobili */
        @media (max-width: 768px) {
            .allerta-box {
                grid-template-columns: repeat(12, 1fr); /* 12 colonne per riga */
                grid-auto-rows: auto; /* Adatta automaticamente l'altezza delle righe */
            }
        }

        .alertmessage {
            color: white;
        }

        .allertarossa {
            background: #a62a2a;
        }

        .allertaverde {
            background: #3f913f;
        }

        .allertagialla {
            background: #e0bd22;
            color: black;
        }

        .allertaarancione {
            background: #d36c05;
        }

        .meteo-info .content {
            display: flex;
            width: 75%;
            text-align: center;
            font-size: 1vw;
            align-items: center; /* Centra orizzontalmente */
            justify-content: space-between;
        }

        .block.small-text {
            font-weight: 600;
        }

        .meteo-fase-lunare span {
            text-align: center;
            width: 100%;
            display: block;
            margin-top: 10px;
            font-size: 18px;
        }

        @media (max-width: 480px) {
            .meteo-info .content {
                font-size: 12px; /* Font-size aumentato per schermi piccoli */
                line-height: 12px;
            }

            .meteo-info .small-text {
                margin-bottom: 3px;
            }

        }

        .meteo-info .content > div {
            width: 50%;
        }

        .meteo-info .content .block {
            display: block;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .meteo-info .img {
            width: 25%;
            text-align: center;
            font-weight: bold;
            font-size: 14px;

            display: grid; /* Imposta il contenitore come inline-flex */
            align-items: center; /* Allinea verticalmente l'immagine e il testo */
            gap: 0px; /* Riduce lo spazio tra img e span */

        }


        .img img, .img span {
            display: inline; /* Imposta entrambi gli elementi come inline */
        }

        .meteo-info hr, .meteo-section hr {
            width: 100%;
            background: #e3e3e3;
            margin-top: 3px;
            margin-bottom: 3px;
        }


        <?php echo esc_attr("#".$unique_id); ?>
        .small-icon {
            width: 35px; /* Imposta la larghezza dell'icona */
            margin: 0 auto !important;
        }

        <?php echo esc_attr("#".$unique_id); ?>
        .fase-lunare-icon {
            margin: 0 auto;
            width: 120px;
        }

        @media (max-width: 800px) {
        <?php echo esc_attr("#".$unique_id); ?> .meteo-info {
            display: flex;
            flex-direction: column; /* Organizza in colonna */
            align-items: center; /* Centra il contenuto orizzontalmente */
            text-align: center; /* Allinea il testo */
            gap: 10px; /* Spaziatura tra gli elementi */
        }

        <?php echo esc_attr("#".$unique_id); ?> .meteo-info .img {
                                                    width: auto; /* Permette all'immagine di essere dimensionata automaticamente */
                                                }

        <?php echo esc_attr("#".$unique_id); ?> .meteo-info .content {
                                                    display: flex; /* Dividi il contenuto in due colonne */
                                                    flex-direction: row; /* Ripristina il layout orizzontale */
                                                    justify-content: space-between; /* Spaziatura tra i due blocchi */
                                                    width: 100%; /* Assicura che occupi tutta la larghezza */
                                                }

        <?php echo esc_attr("#".$unique_id); ?> .meteo-info .content > div {
                                                    width: 50%; /* Ogni colonna occupa il 50% */
                                                    text-align: center; /* Allinea i testi */
                                                }
        }

    </style>
    <div id="<?php echo esc_attr($unique_id); ?>" class="meteo-widget">

        <?php foreach ($sections as $section): ?>
            <?php if ($section !== 'barra_stato'): // Escludi barra di stato per renderizzarla dopo ?>
                <div class="meteo-section">
                    <?php switch ($section):
                        case 'temperatura': ?>
                            <div class="temperatura-big">
                                <div class="temperatura-title">
                                    <span class="meteo-temperatura">...</span>°C
                                </div>
                                <div class="temperatura-icons">

                                </div>
                            </div>
                            <p>Minima: <span class="meteo-minima">...</span>°C alle <span
                                        class="meteo-minima-tempo">...</span></p>
                            <p>Massima: <span class="meteo-massima">...</span>°C alle <span class="meteo-massima-tempo">...</span>
                            </p>
                            <hr>
                            <p>Ultimo aggiornamento: <span class="meteo-ultimo-aggiornamento">...</span></p>
                            <p>Rilevazione effettuata a: <span class="meteo-rilevazione-effettuata">...</span></p>
                            <hr>
                            <span class="meteo-allerta"></span>
                            <div class="meteo-allerta-detail"></div>

                            <p><span class="meteo-ombrello"></span></p>

                            <?php break;
                        case 'vento_umidita': ?>
                            <div class="meteo-info">
                                <div class="img">
                                    <img src="<?php echo $baseDir; ?>/vento.png" alt="Icona vento" class="small-icon">
                                    <span>Vento</span>
                                </div>
                                <div class="content">
                                    <div>
                                        <span class="block small-text">Velocità</span>
                                        <div>
                                            <span class="meteo-vento">...</span>km/h
                                        </div>
                                    </div>
                                    <div>
                                        <span class="block small-text">Direzione</span>
                                        <span class="meteo-direzione"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="meteo-info">
                                <hr>
                            </div>
                            <div class="meteo-info">
                                <div class="img">
                                    <img src="<?php echo $baseDir; ?>/umidita.png" alt="Icona umidità"
                                         class="small-icon">
                                    <span>Umidità</span>

                                </div>
                                <div class="content">

                                    <div>
                                        <span class="block small-text small-text">&nbsp;</span>
                                        <span class="meteo-umidita">...</span>%
                                    </div>
                                    <div>
                                        <span class="block small-text small-text">Punto Rugiada</span>
                                        <span class="block meteo-rugiada"></span>
                                    </div>

                                </div>
                            </div>
                            <div class="meteo-info">
                                <hr>
                            </div>
                            <div class="meteo-info">
                                <div class="img">

                                    <img src="<?php echo $baseDir; ?>/pioggia.png" alt="Icona pioggia"
                                         class="small-icon">

                                    <span>Pioggia</span>

                                </div>

                                <div class="content">

                                    <div>
                                        <span class="block small-text">In questo momento</span>
                                        <div class="block">
                                            <span class="meteo-pioggia">...</span>mm/h
                                        </div>
                                    </div>
                                    <div>
                                        <span class="block small-text">Giornaliera</span>
                                        <span class="block meteo-pioggiagiorno">...</span>mm
                                    </div>
                                </div>
                            </div>
                            <div class="meteo-info">
                                <hr>
                            </div>
                            <div class="meteo-info">
                                <div class="img">

                                    <img src="<?php echo $baseDir; ?>/pressione.png" alt="Icona pressione"
                                         class="small-icon">
                                    <span>Pressione</span>
                                </div>
                                <div class="content">
                                    <div>
                                        <span class="meteo-pressione">...</span> millibar
                                    </div>
                                    <div>
                                        <span class="block small-text">Percepita</span>
                                        <span class="meteo-percepita">...</span>°C
                                    </div>
                                </div>
                            </div>
                            <?php break;
                        case 'alba_tramonto': ?>
                            <div class="alba-tramonto-wrapper">
                                <div class="alba-tramonto-container">
                                    <p>Alba:</p>
                                    <img lazy src="<?php echo $baseDir ?>/alba.png" class="alba-tramonto-icon">
                                    <span class="meteo-alba">...</span>
                                </div>
                                <div class="alba-tramonto-container">
                                    <p>Tramonto:</p>
                                    <img lazy src="<?php echo $baseDir ?>/tramonto.png" class="alba-tramonto-icon">
                                    <span class="meteo-tramonto">...</span>
                                </div>
                            </div>
                            <?php break;
                        case 'cosa_portare': ?>
                            <h3>Portate con voi</h3>
                            <p class="meteo-cosa-portare">...</p>
                            <?php break;
                        case 'consigli': ?>
                            <h3>Consigli</h3>
                            <p class="meteo-consiglio">...</p>
                            <?php break;

                        case 'fase_lunare': ?>
                            <!-- Sezione Fase Lunare -->
                            <h3>Fase Lunare</h3>
                            <p class="meteo-fase-lunare">
                                <img src="" alt="Fase lunare" class="fase-lunare-icon">
                                <span></span>
                            </p>
                            <?php break;
                    endswitch; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (in_array('barra_stato', $sections)): ?>
            <div class="meteo-section full-width status-bar">
                <p>Stato delle condizioni aggiornato: Ultime 24 ore</p>
                <div class="stato-condizioni"></div>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        (function () {
            const widgetDiv = document.getElementById("<?php echo esc_js($unique_id); ?>");
            const fetchInterval = 300000; // 5 minuti

            function fetchWeatherData() {
                fetch('<?php echo esc_url(rest_url('meteofetcher/v1/weather-data')); ?>')
                    .then(response => response.json())
                    .then(data => {
                        <?php if (in_array('temperatura', $sections)): ?>
                        widgetDiv.querySelector(".meteo-temperatura").innerText = data.temperatura;

                        let iconColumn = ''
                        data.icons_temperatura.forEach((icon, index) => {
                            // Aggiunge l'icona alla prima colonna
                            iconColumn += `<img src="${icon}" class="temperatura-icon">`;
                        });
                        widgetDiv.querySelector(".temperatura-icons").innerHTML = iconColumn;


                        widgetDiv.querySelector(".meteo-minima").innerText = data.minima;
                        widgetDiv.querySelector(".meteo-minima-tempo").innerText = data.minimaTempo24;
                        widgetDiv.querySelector(".meteo-massima").innerText = data.massima;
                        widgetDiv.querySelector(".meteo-massima-tempo").innerText = data.massimaTempo24;
                        widgetDiv.querySelector(".meteo-rilevazione-effettuata").innerText = data.location
                        widgetDiv.querySelector(".meteo-ombrello").innerHTML = data.ombrello
                        widgetDiv.querySelector(".meteo-ultimo-aggiornamento").innerText = data.data

                        if (data.allerta && data.allerta != '') {
                            widgetDiv.querySelector(".meteo-allerta").classList.add(data.allerta);
                            widgetDiv.querySelector(".meteo-allerta-detail").classList.add(data.allerta);
                        }

                        widgetDiv.querySelector(".meteo-allerta").innerHTML = data.allertaIntro
                        widgetDiv.querySelector(".meteo-allerta-detail").innerHTML = data.allertaDetail



                        <?php endif; ?>


                        <?php if (in_array('barra_stato', $sections)): ?>
                        widgetDiv.querySelector(".stato-condizioni").innerHTML = data.statoCondizioni;
                        <?php endif; ?>

                        <?php if (in_array('vento_umidita', $sections)): ?>
                        widgetDiv.querySelector(".meteo-vento").innerText = data.velocitaVento;
                        widgetDiv.querySelector(".meteo-umidita").innerText = data.umidita;
                        widgetDiv.querySelector(".meteo-pioggia").innerText = data.pioggia;
                        widgetDiv.querySelector(".meteo-pressione").innerText = data.pressione;
                        widgetDiv.querySelector(".meteo-percepita").innerText = data.percepita;
                        widgetDiv.querySelector(".meteo-pioggiagiorno").innerText = data.pioggiagiorno;
                        widgetDiv.querySelector(".meteo-rugiada").innerText = data.rugiada;
                        widgetDiv.querySelector(".meteo-direzione").innerText = data.direzioneVento;




                        <?php endif; ?>

                        <?php if (in_array('alba_tramonto', $sections)): ?>
                        widgetDiv.querySelector(".meteo-alba").innerText = data.alba24;
                        widgetDiv.querySelector(".meteo-tramonto").innerText = data.tramonto24;
                        <?php endif; ?>

                        <?php if (in_array('fase_lunare', $sections)): ?>
                        const faseLunare = data.faseLunare || 'N/A';
                        const imgBaseDir = "<?php echo $baseDir; ?>"; // Percorso di base per le immagini

// Seleziona l'elemento dell'immagine e dello span
                        const faseLunareImg = widgetDiv.querySelector(".meteo-fase-lunare img");
                        const faseLunareText = widgetDiv.querySelector(".meteo-fase-lunare span");

// Imposta il percorso dell'immagine, l'attributo alt e aggiorna lo span
                        if (faseLunare === 'N/A') {
                            faseLunareImg.style.display = 'none'; // Nasconde l'immagine se la fase è N/A
                            if (faseLunareText) {
                                faseLunareText.textContent = 'Fase lunare non disponibile';
                            }
                        } else {
                            const altText = `Luna ${faseLunare}`;
                            const faseLunareSlug = faseLunare.replace(/\s+/g, '-'); // Sostituisce gli spazi con trattini
                            faseLunareImg.src = `${imgBaseDir}/${faseLunareSlug}.png`;
                            faseLunareImg.alt = altText;
                            faseLunareImg.style.display = 'block';

                            if (faseLunareText) {
                                faseLunareText.textContent = altText; // Aggiorna lo span con l'attributo alt
                            }
                        }
                        <?php endif; ?>

                        // Nuove sezioni da aggiungere
                        <?php if (in_array('consigli', $sections)): ?>
                        if (widgetDiv.querySelector(".meteo-consiglio")) {
                            widgetDiv.querySelector(".meteo-consiglio").innerHTML = data.consiglio;
                        }
                        <?php endif; ?>

                        <?php if (in_array('cosa_portare', $sections)): ?>
                        // Assicurati di avere `data` come oggetto JSON contenente i dati restituiti dalla chiamata AJAX
                        if (widgetDiv.querySelector(".meteo-cosa-portare")) {
                            const cosaPortareContainer = widgetDiv.querySelector(".meteo-cosa-portare");

                            // Creazione delle due colonne
                            let iconColumn = '<div class="column-icons">';
                            let textColumn = '<div class="column-texts">';

                            // Loop attraverso `cosa_portare` e `cosa_portare_icons` per popolare le colonne
                            data.cosa_portare.forEach((item, index) => {
                                // Aggiunge la descrizione alla seconda colonna
                                textColumn += `<p>${item}</p>`;
                            });

                            data.cosa_portare_icons.forEach((item, index) => {
                                // Aggiunge l'icona alla prima colonna
                                iconColumn += `<img src="${data.cosa_portare_icons[index]}" alt="Icona ${index + 1}" class="cosa-portare-icon">`;
                            });

                            // Chiudi i div delle colonne
                            iconColumn += '</div>';
                            textColumn += '</div>';

                            // Imposta il contenuto HTML del container con le colonne create
                            cosaPortareContainer.innerHTML = `
        <div class="cosa-portare-container">
           <div class="icons-box"> ${iconColumn}</div>
           <div class="column-texts">${textColumn}</div>
        </div>
    `;
                        }
                        <?php endif; ?>


                    })
                    .catch(error => console.error('Errore nel recupero dei dati meteo:', error));
            }

            fetchWeatherData(); // Chiamata iniziale
            setInterval(fetchWeatherData, fetchInterval); // Polling ogni 5 minuti
        })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('meteo_widget', 'meteo_parser_new');


add_filter('cron_schedules', 'five_minutes');

add_action('cronFetch', array($meteoFetcherWidget, 'fetchInfo'));
add_action('triggerHistory', array($meteoFetcherWidget, 'makeHistory'));

register_activation_hook(__FILE__, array($meteoFetcherWidget, 'activate'));

register_deactivation_hook(__FILE__, array($meteoFetcherWidget, 'deactivate'));
