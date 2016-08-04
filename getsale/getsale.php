<?php
/*
Plugin Name: GetSale
Plugin URI: https://getsale.io
Description: GetSale &mdash; профессиональный инструмент для создания popup-окон.
Version: 1.0.0
Author: GetSale Team
Author URI: https://getsale.io
*/

// Creating the widget

include 'getsale_options.php';

add_action('wp_enqueue_scripts', 'getsale_scripts_method');

add_filter('plugin_action_links', 'getsale_plugin_action_links', 10, 2);

add_action('wc_ajax_add_to_cart', 'getsale_ajax_add_to_cart');
add_action('woocommerce_restore_cart_item', 'getsale_ajax_add_to_cart');

function getsale_ajax_add_to_cart() {
    setcookie('getsale_add', true, time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

add_action('woocommerce_cart_item_removed', 'getsale_del_from_cart');

function getsale_del_from_cart() {
    setcookie('getsale_del', 'true', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

function getsale_plugin_action_links($actions, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $actions;
    $settings_link = '<a href="options-general.php?page=getsale_settings">Настройки</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('plugin_row_meta', 'getsale_plugin_description_links', 10, 4);

function getsale_plugin_description_links($meta, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $meta;
    $meta[] = '<a href="options-general.php?page=getsale_settings">Настройки</a>';
    return $meta;
}

add_filter('wc_add_to_cart_message', 'getsale_add_filter', 10, 4);

function getsale_add_filter($product_id) {
    add_action('wp_enqueue_scripts', 'getsale_scripts_add');
    return $product_id;
}

$options = get_option('getsale_option_name');

if (is_admin()) {
    $options = get_option('getsale_option_name');

    if (is_bool($options)) {
        getsale_set_default_code();
    }

    $reg_domain = 'https://edge.getsale.io';
    $url = get_site_url();

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_REQUEST['getsale_option_name']))) {
        $options = $_REQUEST['getsale_option_name'];
        if (($options['getsale_email'] !== '') && ($options['getsale_api_key'] !== '') && ($options['getsale_project_id'] == '')) {
            $reg_ans = getsale_reg($reg_domain, $options['getsale_email'], $options['getsale_api_key'], $url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))) {
                    $getsale_options = get_option('getsale_option_name');
                    $getsale_options['getsale_project_id'] = $reg_ans->payload->projectId;
                    $getsale_options['getsale_reg_error'] = '';
                    $getsale_options['getsale_email'] = $options['getsale_email'];
                    $getsale_options['getsale_api_key'] = $options['getsale_api_key'];
                    update_option('getsale_option_name', $getsale_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                } elseif ($reg_ans->status = 'error') {
                    $getsale_options = get_option('getsale_option_name');
                    $getsale_options['getsale_reg_error'] = $reg_ans->code;
                    $getsale_options['getsale_project_id'] = '';
                    $getsale_options['getsale_email'] = $options['getsale_email'];
                    $getsale_options['getsale_api_key'] = $options['getsale_api_key'];
                    update_option('getsale_option_name', $getsale_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                }
            }
        }
    } else {
        $options = get_option('getsale_option_name');
        $my_settings_page = new getsaleSettingsPage();
    }
}

function getsale_script_cookie() {
    if (isset($_COOKIE['getsale_add'])) {
        add_action('wp_enqueue_scripts', 'getsale_scripts_add');
        setcookie('getsale_add', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }

    if (isset($_COOKIE['getsale_del'])) {
        add_action('wp_enqueue_scripts', 'getsale_scripts_del');
        setcookie('getsale_del', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }
};
add_action('init', 'getsale_script_cookie');