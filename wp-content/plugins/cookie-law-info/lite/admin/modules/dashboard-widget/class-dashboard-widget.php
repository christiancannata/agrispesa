<?php
/**
 * Class Dashboard_Widget file.
 *
 * @package CookieYes
 */

namespace CookieYes\Lite\Admin\Modules\Dashboard_Widget;

use CookieYes\Lite\Includes\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Handles Dashboard Widget Operation
 *
 * @class       Dashboard_Widget
 * @version     3.0.0
 * @package     CookieYes
 */
class Dashboard_Widget extends Modules {
    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'cky/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = '/dashboard-widget';

    /**
     * Instance of the current class
     *
     * @var object
     */
    private static $instance;

    
    /**
     * Return the current instance of the class
     *
     * @return object
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('dashboard_widget');
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue required scripts
     */
    public function enqueue_scripts($hook) {
        if ('index.php' !== $hook) {
            return;
        }

        // Check if we have any consents logged before loading Chart.js
        $has_consents = $this->has_consent_logs();
        if ($has_consents) {
            $script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script(
                'cky-chart',
                plugin_dir_url(__FILE__) . 'assets/js/chart' . $script_suffix . '.js',
                array(),
                '4.4.1',
                true
            );
        }
    }

    /**
     * Check if there are any consent logs
     *
     * @return boolean
     */
    private function has_consent_logs() {
        $response = rest_do_request(new \WP_REST_Request('GET', '/cky/v1/consent_logs/statistics'));
        if ($response->is_error()) {
            return false;
        }
        $data = $response->get_data();
        if (!is_array($data)) {
            return false;
        }
        $total = 0;
        foreach ($data as $item) {
            if (isset($item['count'])) {
                $total += intval($item['count']);
            }
        }
        return $total > 0;
    }

    /**
     * Initialize the class
     */
    public function init() {
        add_action( 'wp_dashboard_setup', array( $this, 'add_cookieyes_dashboard_widget' ) );
    }

    /**
     * Add the CookieYes dashboard widget.
     */
    public function add_cookieyes_dashboard_widget() {
        wp_add_dashboard_widget(
            'cookieyes_dashboard_widget',
            __( 'CookieYes', 'cookie-law-info' ),
            array( $this, 'render_cookieyes_dashboard_widget' )
        );
    }

    /**
     * Render the CookieYes dashboard widget.
     */
    public function render_cookieyes_dashboard_widget() {
        $connected = false;
        if (class_exists('CookieYes\\Lite\\Admin\\Modules\\Settings\\Includes\\Settings')) {
            $settings = \CookieYes\Lite\Admin\Modules\Settings\Includes\Settings::get_instance();
            $connected = !empty($settings->get_website_id());
        } else {
            $connected = get_option('cky_webapp_connected');
        }

        if (!$connected) {
            $this->render_dashboard_widget_disconnected();
        } else {
            $this->render_dashboard_widget_connected();
        }
    }

    /**
     * Render the widget for disconnected state.
     */
    private function render_dashboard_widget_disconnected() {
        ?>
        <div class="cky-consent-chart-section">
            <div class="cky-consent-chart cky-blur">
                <img 
                    src="<?php echo esc_url(CKY_PLUGIN_URL . 'admin/dist/img/trends.png'); ?>" 
                    alt="Consent Trends Dummy Chart"
                    style="display:block;width:100%;height:auto;"
                />
                <div class="cky-modal-overlay">
                    <div class="cky-modal-content">
                        <b><?php esc_html_e('Get cookie consent insights in your Dashboard!', 'cookie-law-info'); ?></b>
                        <p><?php esc_html_e('Track your consent rates and unlock advanced features that keep your site in check.', 'cookie-law-info'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cookie-law-info')); ?>" class="button button-primary">
                            <?php esc_html_e('Connect to CookieYes Web App', 'cookie-law-info'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .cky-consent-chart-section {
                position: relative;
                min-height: 250px;
                margin: 0px -12px -12px -12px;
            }
            .cky-modal-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }
            .cky-modal-content {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0px 2px 12.3px 0px rgba(114, 174, 230, 0.25);
                padding: 32px 8px;
                max-width: 350px;
                text-align: center;
                z-index: 11;
            }
            .cky-modal-content b {
                display: block;
                margin-bottom: 12px;
                font-size: 16px;
                font-weight: 600;
                line-height: 24px;
            }
            .cky-modal-content p {
                font-size: 13px;
                font-weight: 400;
                line-height: 18px;
                margin-bottom: 20px;
            }
            .cky-modal-content .button.button-primary {
                padding: 8px 16px;
                line-height: normal;
                font-size: 15px;
            }
        </style>
        <?php
    }

    /**
     * Render the widget for connected state.
     */
    private function render_dashboard_widget_connected() {
        $has_consents = $this->has_consent_logs();
        ?>
        <div class="cky-consent-chart-widget" id="cky-dashboard-widget-chart">
            <div class="cky-chart-container" id="cky-dashboard-widget-chart-container">
                <?php if ($has_consents): ?>
                    <canvas id="cky-pie-chart-widget" width="320" height="320" style="display:none;width:100%;height:auto;"></canvas>
                    <div class="cky-center-total-consents" style="display:none;">
                        <span class="cky-center-total-consents-value"></span>
                        <div class="cky-center-total-consents-label">Total Consents</div>
                    </div>
                <?php endif; ?>
                <div id="cky-no-consents-placeholder" style="display:<?php echo $has_consents ? 'none' : 'block'; ?>;text-align:center;padding:40px 0;">
                    <svg width="110" height="110" viewBox="0 0 110 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="55" cy="55" r="55" fill="#E5E7EA"/>
                        <circle cx="38" cy="54" r="9" fill="#FFFFFF"/>
                        <circle cx="55" cy="80" r="5" fill="#FFFFFF"/>
                        <circle cx="75" cy="45" r="11" fill="#FFFFFF"/>
                        <circle cx="55" cy="30" r="5" fill="#FFFFFF"/>
                        
                    </svg>
                    <p style="font-size:20px;color:#656178;margin-top:20px;">No consents were logged</p>
                </div>
            </div>
            <?php if ($has_consents): ?>
                <div class="cky-consent-legend" id="cky-consent-legend" style="display:none;">
                    <div class="cky-legend-item"><span class="cky-legend-color cky-legend-accepted"></span>Accepted</div>
                    <div class="cky-legend-item"><span class="cky-legend-color cky-legend-rejected"></span>Rejected</div>
                    <div class="cky-legend-item"><span class="cky-legend-color cky-legend-partial"></span>Partially Accepted</div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($has_consents): ?>
        <script>
        (async function(){
            try {
                const response = await fetch('<?php echo esc_url_raw(rest_url() . "cky/v1/consent_logs/statistics"); ?>', {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': '<?php echo esc_js(wp_create_nonce('wp_rest')); ?>'
                    }
                });
                const data = await response.json();
                function getCount(consents, type) {
                    let consent = false;
                    if (typeof consents === "object") {
                        consent = consents.find(function(item) {
                            return item.type === type;
                        });
                    }
                    return (consent && consent.count) || 0;
                }
                const accepted = getCount(data, 'accepted');
                const rejected = getCount(data, 'rejected');
                const partial = getCount(data, 'partial');
                const responseArr = [accepted, rejected, partial];
                const total = accepted + rejected + partial;

                if (total === 0) {
                    document.getElementById('cky-no-consents-placeholder').style.display = 'block';
                } else {
                    document.getElementById('cky-pie-chart-widget').style.display = 'block';
                    document.querySelector('.cky-center-total-consents').style.display = 'block';
                    document.getElementById('cky-consent-legend').style.display = 'flex';
                    document.querySelector('.cky-center-total-consents-value').textContent = total;

                    const ctx = document.getElementById('cky-pie-chart-widget').getContext('2d');
                    new window.Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Accepted', 'Rejected', 'Partially Accepted'],
                            datasets: [{
                                data: responseArr,
                                backgroundColor: ['rgba(51, 168, 129, 0.5)', 'rgba(236, 74, 94, 0.5)', 'rgba(68, 147, 249, 0.5)'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            cutout: '80%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: '#656178',
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.raw;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${context.label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            } catch (e) {
                const chartDiv = document.getElementById('cky-dashboard-widget-chart');
                if (chartDiv) {
                    chartDiv.innerHTML = '<p style="color:red;">Unable to load consent trends.</p>';
                }
            }
        })();
        </script>
        <?php endif; ?>
        <style>
    .cky-consent-chart-widget {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 380px;
    }
    .cky-chart-container {
        position: relative;
        width: 320px;
        height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    #cky-pie-chart-widget {
        position: absolute;
        width: 100% !important;
        height: auto !important;
    }
    .cky-center-total-consents {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        pointer-events: none;
        z-index: 1;
        height: fit-content;
        width: fit-content;
    }
    .cky-center-total-consents-value {
        font-size: 4em;
        font-weight: 700;
        line-height: 1;
        color: #111;
        display: block;
        margin-bottom: 5px;
    }
    .cky-center-total-consents-label {
        font-size: 1.5em;
        font-weight: 400;
        color: #111;
        white-space: nowrap;
    }
    .cky-consent-legend {
    margin-left: 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 50px;
}
    .cky-legend-item {
        display: flex;
        align-items: center;
    }
    .cky-legend-color {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .cky-legend-accepted {
        background: rgba(51, 168, 129, 0.5);
    }
    .cky-legend-rejected {
        background: rgba(236, 74, 94, 0.5);
    }
    .cky-legend-partial {
        background: rgba(68, 147, 249, 0.5);
    }
</style> 
        <?php
    }
}

// Initialize the widget module
Dashboard_Widget::get_instance()->init(); 