<?php
final class ICAPFW_WOO
{
    protected $cities;

    protected static $_instance = null;

    public static function init()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    public function do_init()
    {
        load_plugin_textdomain('italy-city-and-postcode-for-woocommerce', false, plugin_basename(dirname(ICAPFW_WOO_PLUGIN_BASENAME)) . '/languages');

        add_filter('woocommerce_billing_fields', [$this, 'billing_fields']);
        add_filter('woocommerce_shipping_fields', [$this, 'shipping_fields']);
        add_filter('woocommerce_form_field_city', [$this, 'form_field_city'], 10, 4);
        add_action('wp_enqueue_scripts', [$this, 'load_scripts']);

        add_filter('woocommerce_states', [$this, 'load_country_states']);
        add_filter('woocommerce_rest_prepare_report_customers', [$this, 'set_state_local']);
		add_filter('woocommerce_default_address_fields',[$this, 'wc_change_state_and_city_order']);

    }

    public function billing_fields($fields)
    {
        $fields['billing_city']['type'] = 'city';
        return $fields;
    }

    public function shipping_fields($fields)
    {
        $fields['shipping_city']['type'] = 'city';
        return $fields;
    }

    public function form_field_city($field, $key, $args, $value)
    {
        // Do we need a clear div?
        if ((!empty($args['clear']))) {
            $after = '<div class="clear"></div>';
        } else {
            $after = '';
        }

        // Required markup
        if ($args['required']) {
            $args['class'][] = 'validate-required';
            $required = ' <abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
        } else {
            $required = '';
        }

        // Custom attribute handling
        $custom_attributes = [];
        if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
            foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
            }
        }

        // Validate classes
        if (!empty($args['validate'])) {
            foreach ($args['validate'] as $validate) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        // field p and label
        $field = '<p class="form-row ' . esc_attr(implode(' ', $args['class'])) .'" id="' . esc_attr($args['id']) . '_field">';
        if ($args['label']) {
            $field .= '<label for="' . esc_attr($args['id']) . '" class="' . esc_attr(implode(' ', $args['label_class'])) .'">' . $args['label']. $required . '</label>';
        }

        // Get Country
        $country_key = $key == 'billing_city' ? 'billing_country' : 'shipping_country';
        $current_cc = WC()->checkout->get_value($country_key);
        $state_key = $key == 'billing_city' ? 'billing_state' : 'shipping_state';
        $current_sc = WC()->checkout->get_value($state_key);

        // Get country cities
        $cities = $this->get_cities($current_cc);
        $field .= '<span class="woocommerce-input-wrapper">';
        if (is_array($cities)) {
            $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="city_select ' . esc_attr(implode(' ', $args['input_class'])) .'" ' . implode(' ', $custom_attributes) . ' placeholder="' . esc_attr($args['placeholder']) . '">
                <option value="">'. __('Select an option&hellip;', 'woocommerce') .'</option>';

            if ($current_sc && isset($cities[$current_sc])) {
                $dropdown_cities = $cities[$current_sc];
            } elseif (is_array(reset($cities))) {
                $dropdown_cities = [];
            } else {
                $dropdown_cities = $cities;
            }
            foreach ($dropdown_cities as $city_name) {
                if (is_array($city_name)) {
                    $city_name = $city_name[0];
                }
                $field .= '<option value="' . esc_attr($city_name) . '" ' . selected($value, $city_name, false) . '>' . $city_name .'</option>';
            }
            $field .= '</select>';
        } else {
            $field .= '<input type="text" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) .'" value="' . esc_attr($value) . '" placeholder="' . esc_attr($args['placeholder']) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" ' . implode(' ', $custom_attributes) . ' />';
        }
        // field description and close wrapper
        if ($args['description']) {
            $field .= '<span class="description">' . esc_attr($args['description']) . '</span>';
        }
        $field .= '</span>';

        $field .= '</p>' . $after;

        return $field;
    }

    public function load_scripts()
    {
        if (defined('WC_VERSION')) {
            if (is_cart() || is_checkout() || is_wc_endpoint_url('edit-address')) {
                wp_enqueue_script('italy-city-and-postcode-for-woocommerce', ICAPFW_WOO_PLUGIN_URL . 'style/js/city-select.js', ['jquery', 'woocommerce'], ICAPFW_WOO_VERSION, true);

                wp_localize_script('italy-city-and-postcode-for-woocommerce', 'italy_city_and_postcode_select_params', [
                    'cities' => $this->get_cities(),
                    'i18n_select_city_text'=> esc_attr__('Select an option&hellip;', 'woocommerce'),
                ]);
            }
        }
    }

    protected function i18n_files_path()
    {
        $file_path = ICAPFW_WOO_PLUGIN_DIR;
        return $file_path;
    }

    public function load_country_states($states)
    {
        $allowed = array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries());
        if ($allowed) {
            $base_path = $this->i18n_files_path();
            foreach ($allowed as $code => $country) {
                if (file_exists($base_path . 'states/' . $code . '.php')) {
                    $states = array_merge($states, include($base_path . 'states/' . $code . '.php'));
                }
            }
        }
        return $states;
    }

    public function get_cities($cc = null)
    {
        if (empty($this->cities)) {
            $this->load_country_cities();
        }
        if (!is_null($cc)) {
            return isset($this->cities[$cc]) ? $this->cities[$cc] : false;
        } else {
            return $this->cities;
        }
    }

    public function load_country_cities()
    {
        $cities = [];
        $allowed = array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries());
        if( get_option( 'wcicapfw_disable_zipcode_field' ) == "yes" && $allowed) {
            $base_path = $this->i18n_files_path();
            foreach ($allowed as $code => $country) {
                if (file_exists($base_path . 'cities-nozipcode/' . $code . '.php')) {
                    $cities = array_merge($cities, include($base_path . 'cities-nozipcode/' . $code . '.php'));
                }
            }
        }
		else {
            $base_path = $this->i18n_files_path();
            foreach ($allowed as $code => $country) {
                if (file_exists($base_path . 'cities-withzipcode/' . $code . '.php')) {
                    $cities = array_merge($cities, include($base_path . 'cities-withzipcode/' . $code . '.php'));
                }
            }
        }
        $this->cities = apply_filters('italy_city_and_postcode_select_cities', $cities);
    }

    public function set_state_local($response)
    {
        static $states;
        if (!isset($states[$response->data['country']])) {
            $states[$response->data['country']] = WC()->countries->get_states($response->data['country']);
        }
        if (isset($states[$response->data['country']][$response->data['state']])) {
            $response->data['state'] = $states[$response->data['country']][$response->data['state']];
        }
        return $response;
    }
	
	

    public function wc_change_state_and_city_order($fields) {
	
	
            $fields['state']['priority'] = 60;
            $fields['city']['priority'] = 65;
			$fields['postcode']['priority'] = 80;
            return $fields;
        }

    public static function plugin_activation()
    {
    }

    public static function plugin_deactivation()
    {
    }
}