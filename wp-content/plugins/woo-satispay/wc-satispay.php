<?php
if (!defined('ABSPATH')) {
  exit;
}

require_once(__DIR__ . '/satispay-sdk/init.php');

class WC_Satispay extends WC_Payment_Gateway {
  public function __construct() {
    $this->id                   = 'satispay';
    $this->method_title         = __('Satispay', 'woo-satispay');
    $this->order_button_text    = __('Pay with Satispay', 'woo-satispay');
    $this->method_description   = __('Do it smart. Choose Satispay and pay with a tap!', 'woo-satispay');
    $this->has_fields           = false;
    $this->supports             = array(
      'products',
      'refunds'
    );

    $this->title                = $this->method_title;
    $this->description          = $this->method_description;
    $this->icon                 = plugins_url('/logo.svg', __FILE__);

    $this->init_form_fields();
    $this->init_settings();

    add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
    add_action('woocommerce_api_wc_gateway_'.$this->id, array($this, 'gateway_api'));

    if ($this->get_option('sandbox') == 'yes') {
      \SatispayGBusiness\Api::setSandbox(true);
    }

    \SatispayGBusiness\Api::setPublicKey($this->get_option('publicKey'));
    \SatispayGBusiness\Api::setPrivateKey($this->get_option('privateKey'));
    \SatispayGBusiness\Api::setKeyId($this->get_option('keyId'));
  }

  public function process_refund($order, $amount = null, $reason = '') {
    $order = new WC_Order($order);

    \SatispayGBusiness\Payment::create(array(
      'flow' => 'REFUND',
      'amount_unit' => round($amount * 100),
      'currency' => (method_exists($order, 'get_currency')) ? $order->get_currency() : $order->order_currency,
      'parent_payment_uid' => $order->get_transaction_id()
    ));

    return true;
  }

  public function finalize_orders() {
    if ($this->get_option('finalizeUnhandledTransactions') === 'yes' && $this->get_option('enabled') === 'yes') {
      $rangeStart = $this->get_start_date_scheduled_time();
      $rangeEnd = $this->get_end_date_scheduled_time();
      $orders = wc_get_orders(array(
        'limit' => -1,
        'type' => 'shop_order',
        'status' => array('wc-pending'),
        'date_created'=> $rangeStart .'...'. $rangeEnd
      )
    );
    foreach ($orders as $order) {
      try {
        if ($order->get_payment_method() === 'satispay') {
          $transactionId = $order->get_transaction_id();
          if (!isset($transactionId)) {
            continue;
          }
          //callback logic
          $payment = \SatispayGBusiness\Payment::get($transactionId);
          if ($order->has_status(wc_get_is_paid_statuses())) {
            continue;
          }
          if ($payment->status === 'ACCEPTED') {
            $order->payment_complete($payment->id);
            $order->add_order_note('The Satispay Payment has been finalized by custom cron action');
            $order->save();
          }
        }
      } catch (\Exception $e) {
          if (function_exists('wc_get_logger')) {
            $logger = wc_get_logger();
            $logger->debug('An error occured when finalizing the order ' . $order->get_order_number() .
            '. Error: ' . $e->getMessage(),
            array('source' => 'satispay'));
          }
        }
      }
    }
  }

  public function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title' => __('Enable/Disable', 'woo-satispay'),
        'label' => __('Enable Satispay', 'woo-satispay'),
        'type' => 'checkbox',
        'default' => 'no'
      ),
      'activationCode' => array(
        'title' => __('Activation Code', 'woo-satispay'),
        'type' => 'text',
        'description' => sprintf(__('Get a six characters Activation Code from Online Shop section on <a href="%s" target="_blank">Satispay Dashboard</a>.', 'woo-satispay'), 'https://business.satispay.com')
      ),
      
      // 'advanced' => array(
      //   'title' => __( 'Advanced Options', 'woo-satispay' ),
      //   'type' => 'title',
      //   'description' => '',
      // ),

      'sandbox' => array(
        'title' => __('Sandbox', 'woo-satispay'),
        'label' => __('Sandbox Mode', 'woo-satispay'),
        'type' => 'checkbox',
        'default' => 'no',
        'description' => sprintf(__('Sandbox Mode can be used to test payments. Request a <a href="%s" target="_blank">Sandbox Account</a>.', 'woo-satispay'), 'https://developers.satispay.com/docs/sandbox-account')
      ),
      'finalizeUnhandledTransactions' => array(
        'title' => __('Finalize unhandled payments', 'woo-satispay'),
        'label' => __('Enable cron', 'woo-satispay'),
        'type' => 'checkbox',
        'default' => 'no',
        'description' => sprintf(__('Finalize unhandled Satispay payments with a cron.', 'woo-satispay'))
      ),
      'finalizeMaxHours' => array(
        'title' => __('Finalize pending payments up to', 'woo-satispay'),
        'label' => __('Finalize pending payments up to', 'woo-satispay'),
        'type' => 'integer',
        'default' => 4,
        'description' => sprintf(__('Choose a number of hours, default is four and minimum is two.', 'woo-satispay'))
      )
      // 'debug' => array(
      //   'title' => __('Debug Logs', 'woo-satispay'),
      //   'label' => __('Enable Logs', 'woo-satispay'),
      //   'type' => 'checkbox',
      //   'default' => 'no',
      //   'description' => sprintf(__('Log Satispay requests inside %s.<br />Note: this may log personal informations. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woo-satispay'), '<code>'.WC_Log_Handler_File::get_log_file_path('satispay').'</code>')
      // )
    );
  }

  public function gateway_api() {
    switch($_GET['action']) {
      case 'redirect':
        $paymentId = WC()->session->get('satispay_payment_id');
        if (!$paymentId) {
            header('Location: '.$this->get_return_url(''));
            break;
        }
        $payment = \SatispayGBusiness\Payment::get($paymentId);
        $order = new WC_Order($payment->metadata->order_id);

        if ($payment->status === 'ACCEPTED') {
          header('Location: '.$this->get_return_url($order));
        } else {
          \SatispayGBusiness\Payment::update($payment->id, array(
            'action' => 'CANCEL'
          ));

          header('Location: '.$order->get_cancel_order_url_raw());
        }
        break;
      case 'callback':
        $payment = \SatispayGBusiness\Payment::get($_GET['payment_id']);
        $order = new WC_Order($payment->metadata->order_id);

        if ($order->has_status(wc_get_is_paid_statuses())) {
          exit;
        }

        if ($payment->status === 'ACCEPTED') {
          $order->payment_complete($payment->id);
        }
        break;
    }
  }

  public function process_admin_options() {
    $activationCode = $this->get_option('activationCode');
    $sandbox = $this->get_option('sandbox');
    $finalizeMaxHours = $this->get_option('finalizeMaxHours');
    $finalizeUnhandledTransactions = $this->get_option('finalizeUnhandledTransactions');
    $postData = $this->get_post_data();

    $newActivationCode = $postData['woocommerce_satispay_activationCode'];
    $newSandbox = $postData['woocommerce_satispay_sandbox'];

    if (!empty($newActivationCode) && $newActivationCode != $activationCode) {
      if ($newSandbox == '1') {
        \SatispayGBusiness\Api::setSandbox(true);
      }

      try {
        $authentication = \SatispayGBusiness\Api::authenticateWithToken($newActivationCode);

        $this->update_option('keyId', $authentication->keyId);
        $this->update_option('privateKey', $authentication->privateKey);
        $this->update_option('publicKey', $authentication->publicKey);
        $this->update_option('activationCode', $newActivationCode);

        \SatispayGBusiness\Api::setKeyId($authentication->keyId);
        \SatispayGBusiness\Api::setPrivateKey($authentication->privateKey);
        \SatispayGBusiness\Api::setPublicKey($authentication->publicKey);
      } catch(\Exception $ex) {
        echo '<div class="notice-error notice">';
        echo '<p>'.sprintf(__('The Activation Code "%s" is invalid', 'woo-satispay'), $newActivationCode).'</p>';
        echo '</div>';
      }
    } else if (empty($newActivationCode)) {
      $this->update_option('keyId', '');
      $this->update_option('privateKey', '');
      $this->update_option('publicKey', '');
      $this->update_option('activationCode', '');
    }

    return parent::process_admin_options();
  }

  public function admin_options() {
    try {
      \SatispayGBusiness\Payment::all();
    } catch (\Exception $ex) {
      echo '<div class="notice-error notice">';
      echo '<p>'.sprintf(__('Satispay is not correctly configured, get an Activation Code from Online Shop section on <a href="%s" target="_blank">Satispay Dashboard</a>', 'woo-satispay'), 'https://business.satispay.com').'</p>';
      echo '</div>';
    }
    
    return parent::admin_options();
  }

  public function is_available() {
    if ($this->get_option('enabled') === 'no') {
      return false;
    }
    return true;
  }

  public function process_payment($order_id) {
    $order = wc_get_order($order_id);

    $apiUrl = WC()->api_request_url('WC_Gateway_Satispay');
    if (strpos($apiUrl, '?') !== FALSE) {
      $callbackUrl = $apiUrl.'&action=callback&payment_id={uuid}';
      $redirectUrl = $apiUrl.'&action=redirect';
    } else {
      $callbackUrl = $apiUrl.'?action=callback&payment_id={uuid}';
      $redirectUrl = $apiUrl.'?action=redirect';
    }

    $payment = \SatispayGBusiness\Payment::create(array(
      'flow' => 'MATCH_CODE',
      'amount_unit' => round($order->get_total() * 100),
      'currency' => (method_exists($order, 'get_currency')) ? $order->get_currency() : $order->order_currency,
      'callback_url' => $callbackUrl,
      'external_code' => $order->get_id(),
      'redirect_url' => $redirectUrl,
      'metadata' => array(
        'order_id' => $order->get_id()
      )
    ));

    try {
        $order->set_transaction_id($payment->id);
        WC()->session->set('satispay_payment_id', $payment->id);
        $order->save();
    } catch (\Exception $e) {
        if (function_exists('wc_get_logger')) {
            $logger = wc_get_logger();
            $logger->debug(
                'Order id - ' . $order->get_id() . ' - Could not save transaction Id for payment due to the following error: ' . $e->getMessage(),
                array('source' => 'satispay')
            );
        }
    }

    return array(
      'result' => 'success',
      'redirect' => $payment->redirect_url
    );
  }

  /**
   * Get the start criteria for the scheduled datetime
  */
  private function get_start_date_scheduled_time()
  {
    $maxHours = $this->get_option('finalizeMaxHours');
    $now = new \DateTime('now', new DateTimeZone('UTC'));
    $scheduledTimeFrame = $maxHours;
    if (is_null($scheduledTimeFrame) || $scheduledTimeFrame == 0 || $scheduledTimeFrame < 0) {
      $scheduledTimeFrame = 4; // DEFAULT_MAX_HOURS
    }
    $tosub = new \DateInterval('PT'. $scheduledTimeFrame . 'H');
    return strtotime( $now->sub($tosub)->format('Y-m-d H:i:s'));
  }

  /**
   * Get the end criteria for the scheduled datetime
  */
  private function get_end_date_scheduled_time()
  {
    $now = new \DateTime('now', new DateTimeZone('UTC'));
    // remove just 1 hour so normal transactions can still be processed
    $tosub = new \DateInterval('PT'. 1 . 'H');
    return strtotime($now->sub($tosub)->format('Y-m-d H:i:s'));
  }
}

function wc_satispay_finalize_orders()
{
  $model = new WC_Satispay();
  $model->finalize_orders();
}
