<?php

function pmxe_init()
{
    if ( ! empty($_GET['zapier_auth']) )
    {
        if ( ! empty($_GET['api_key']) )
        {

            $zapier_api_key = PMXE_Plugin::getInstance()->getOption('zapier_api_key');

            if ( ! empty($zapier_api_key) and $zapier_api_key == $_GET['api_key'] )
            {
                exit(json_encode(array('status' => 'success')));
            }
            else
            {
                http_response_code(401);
                exit(json_encode(array('status' => esc_html__('Error. Incorrect API key, check the WP All Export Pro settings page.', 'wp_all_export_plugin'))));
            }
        }
        else
        {
            http_response_code(401);
            exit(json_encode(array('status' => esc_html__('Error. Incorrect API key, check the WP All Export Pro settings page.', 'wp_all_export_plugin'))));
        }
    }
    if(!empty($_GET['check_connection'])) {
        exit(json_encode(array('success' => true)));
    }

    $custom_types = get_post_types(array('_builtin' => true), 'objects') + get_post_types(array('_builtin' => false, 'show_ui' => true), 'objects') + get_post_types(array('_builtin' => false, 'show_ui' => false), 'objects');

    foreach ($custom_types as $key => $ct) {
        if (in_array($key, array('attachment', 'revision', 'nav_menu_item', 'import_users', 'shop_webhook', 'acf-field', 'acf-field-group'))) unset($custom_types[$key]);
    }
    $custom_types = apply_filters('wpallexport_custom_types', $custom_types);

    foreach ($custom_types as $slug => $type) {

        if ($slug) {

            add_action('rest_after_insert_' . $slug, function ($post) {

                $post_id = $post->ID;
                if (wp_is_post_revision($post_id)) {
                    return;
                }

                $post = get_post($post_id);

                if ($post->post_type === 'shop_order' || ($post->post_type === 'property' && class_exists('Easy_Real_Estate'))) {
                    return;
                }

                $list = new PMXE_Export_List();

                $exportList = $list->setColumns($list->getTable() . '.*')->getBy();

                foreach ($exportList as $export) {
                    if (
                        isset($export['options']['enable_real_time_exports']) &&
                        $export['options']['enable_real_time_exports'] &&
                        isset($export['options']['enable_real_time_exports_running']) &&
                        $export['options']['enable_real_time_exports_running']
                    ) {
                        if (in_array($post->post_type, $export['options']['cpt'])) {

                            if ($post_id) {

                                $exportRecord = new PMXE_Export_Record();
                                $exportRecord->getById($export['id']);
                                $exportRecord->execute(false, true, $post_id);
                            }
                        }
                    }
                }

            });
        }
    }

    add_action('wp_after_insert_post', function($post) {

        if(!class_exists('Easy_Real_Estate')) {
            return;
        }

        if(!is_object($post)) {
            $post = get_post($post);
        }

        if(!is_object($post)) {
            return false;
        }

        if($post->post_type !== 'property') {
            return false;
        }

        if($post->post_status !== 'publish') {
            return false;
        }

        if (defined('REST_REQUEST') && REST_REQUEST // (#1)
            || isset($_GET['rest_route']) // (#2)
            && strpos($_GET['rest_route'], '/', 0) === 0) {
            return;
        }

        if(wp_is_post_revision($post->ID)) {
            return;
        }

        $property_id = $post->ID;

        $list = new PMXE_Export_List();
        $exportList = $list->setColumns($list->getTable() . '.*')->getBy();

        foreach ($exportList as $export) {
            if (
                isset($export['options']['enable_real_time_exports']) &&
                $export['options']['enable_real_time_exports'] &&
                isset($export['options']['enable_real_time_exports_running']) &&
                $export['options']['enable_real_time_exports_running']
            ) {
                if (in_array('property', $export['options']['cpt'])) {

                    if ($property_id) {
                        $exportRecord = new PMXE_Export_Record();
                        $exportRecord->getById($export['id']);
                        $exportRecord->execute(false, true, $property_id);
                    }
                }
            }
        }

    });
}