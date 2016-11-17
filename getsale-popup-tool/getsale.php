<?php
/*
* Plugin Name: GetSale
* Plugin URI: https://getsale.io
* Description: GetSale &mdash; professional tool for creating popup windows.
* Version: 1.0.1
* Author: GetSale Team
* Author URI: https://getsale.io
* Text Domain: getsale-popup-tool
* Domain Path: /languages
*/

$gsver = '1.0.1';

// Creating the widget

include 'getsale_options.php';

add_action('plugins_loaded', 'getsale_load_textdomain');

function getsale_load_textdomain() {
    load_plugin_textdomain( 'getsale-popup-tool', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

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
    $settings_link = '<a href="options-general.php?page=getsale_settings">'. __('Settings', 'getsale-popup-tool') .'</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('plugin_row_meta', 'getsale_plugin_description_links', 10, 4);

function getsale_plugin_description_links($meta, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $meta;
    $meta[] = '<a href="options-general.php?page=getsale_settings">'. __('Settings', 'getsale-popup-tool') .'</a>';
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

    $reg_domain = 'http://edge-dev.getsale.io';
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

add_action( 'admin_enqueue_scripts', 'getsale_script_translate' );

function getsale_script_translate() {
    wp_enqueue_script( 'getsale-main-script', plugin_dir_url( __FILE__ ) . 'main.js');
    wp_localize_script( 'getsale-main-script', 'gs', array(
        'authorization' => __( 'Authorization', 'getsale-popup-tool' ),
        'enter_value' => __( 'Please, enter your Email and API Key from your GetSale account', 'getsale-popup-tool' ),
        'registration' => __( 'If you don’t have GetSale account, you can register it <a href=\'https://getsale.io\'>here</a>', 'getsale-popup-tool' ),
        'support' => __( 'Contact Us: <a href=\'mailto:support@getsale.io\'>support@getsale.io</a>', 'getsale-popup-tool' ),
        'getsale_ver' => '1.0.1',
        'congrats' => __( 'Congratulations! Your website is successfully linked to your <a href=\'https://getsale.io\'>GetSale account</a>', 'getsale-popup-tool' ),
        'widgets_create' => __( 'You can start creating widgets for your website using your <a href=\'https://getsale.io\'>GetSale account</a>!', 'getsale-popup-tool' ),
        'api_key_success' => __( 'API Key is correct', 'getsale-popup-tool' ),
        'email_success' => __( 'Email is correct', 'getsale-popup-tool' ),
        'error403' => __( 'Attention! API Key is invalid. Please, check and enter API Key once again', 'getsale-popup-tool' ),
        'error404' => __( 'Attention! This Email isn’t registered on <a href=\'https://getsale.io\'>GetSale</a>', 'getsale-popup-tool' ),
        'error500' => __( 'Attention! This website is already in use on <a href=\'https://getsale.io\'>GetSale</a>', 'getsale-popup-tool' ),
        'desc' => __( 'powerful cutting edge tool to create widgets and popups for your website!', 'getsale-popup-tool' ),
        'description' => __( 'GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'getsale-popup-tool' ),
        'getsale_name' => __( 'GetSale Popup Tool', 'getsale-popup-tool' ),
        'path' => plugins_url('ok.png', __FILE__),
    ));
}