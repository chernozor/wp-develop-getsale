<?php

class getsaleSettingsPage {
    public $options;
    public $settings_page_name = 'dev_getsale_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'getsale_add_plugin_page'));
        add_action('admin_init', array($this, 'getsale_page_init'));
        $this->options = get_option('dev_getsale_option_name');
    }

    public function getsale_add_plugin_page() {
        add_options_page('GetSale Settings', 'GetSale', 'manage_options', $this->settings_page_name, array(
            $this,
            'getsale_create_admin_page'));
    }

    public function getsale_create_admin_page() {
        $this->options = get_option('dev_getsale_option_name');
        ?>
        <script type="text/javascript">
            <?php include('js/admin.js'); ?>
        </script>
        <div id='getsale_site_url' style='display: none'><?php echo get_site_url(); ?></div>
        <div class='wrap'>
            <div id='wrapper'>
                <form id='settings_form' method='post' action='options.php'>
                    <h1><?php _e('GetSale Popup Tool'); ?></h1>
                    <?php
                    getsale_echo_before_text();
                    settings_fields('dev_getsale_option_group');
                    do_settings_sections('dev_getsale_settings');
                    ?>
                    <input type='submit' name='submit_btn'>
                </form>
            </div>
        </div>
        <?php
    }

    public function getsale_page_init() {
        register_setting('dev_getsale_option_group', 'dev_getsale_option_name', array($this, 'getsale_sanitize'));

        add_settings_section('setting_section_id', '', // Title
            array($this, 'getsale_print_section_info'), $this->settings_page_name);

        add_settings_field('getsale_host', __('Host', 'dev-getsale-popup-tool'), array(
            $this,
            'getsale_host_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('email', __('Email', 'dev-getsale-popup-tool'), array(
            $this,
            'getsale_email_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_api_key', __('API Key', 'dev-getsale-popup-tool'), array(
            $this,
            'getsale_api_key_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_reg_error', 'getsale_reg_error', array(
            $this,
            'getsale_reg_error_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_project_id', 'getsale_project_id', array(
            $this,
            'getsale_project_id_callback'), $this->settings_page_name, 'setting_section_id');
    }

    public function getsale_sanitize($input) {
        $new_input = array();
        $domain = $input['getsale_host'];
        $url = get_site_url();
        if (($input['getsale_email'] !== '') && ($input['getsale_api_key'] !== '') && ($input['getsale_project_id'] == '')) {
            $reg_ans = getsale_reg($domain, $input['getsale_email'], $input['getsale_api_key'], $url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))) {
                    $new_input = get_option('dev_getsale_option_name');
                    $new_input['getsale_host'] = trim($input['getsale_host']);
                    $new_input['getsale_project_id'] = $reg_ans->payload->projectId;
                    $new_input['getsale_email'] = trim($input['getsale_email']);
                    $new_input['getsale_api_key'] = trim($input['getsale_api_key']);
                    $new_input['getsale_reg_error'] = '';
                    update_option('uptolike_options', $new_input);
                }
                elseif ($reg_ans->status = 'error') {
                    $new_input = get_option('dev_getsale_option_name');
                    $new_input['getsale_project_id'] = '';
                    $new_input['getsale_host'] = trim($input['getsale_host']);
                    $new_input['getsale_email'] = trim($input['getsale_email']);
                    $new_input['getsale_api_key'] = trim($input['getsale_api_key']);
                    $new_input['getsale_reg_error'] = $reg_ans->code;
                    update_option('uptolike_options', $new_input);
                }
            }
        }
        return $new_input;
    }

    public function getsale_print_section_info() {
    }

    public function getsale_host_callback() {
        printf('<input type="text" id="getsale_host" name="dev_getsale_option_name[getsale_host]" value="%s" title="%s"/>', isset($this->options['getsale_host']) ? esc_attr(trim($this->options['getsale_host'])) : '', __('Enter Host', 'dev-getsale-popup-tool'));
    }

    public function getsale_email_callback() {
        printf('<input type="text" id="getsale_email" name="dev_getsale_option_name[getsale_email]" value="%s" title="%s"/>', isset($this->options['getsale_email']) ? esc_attr(trim($this->options['getsale_email'])) : '', __('Enter Email', 'dev-getsale-popup-tool'));
    }

    public function getsale_api_key_callback() {
        printf('<input type="text" id="getsale_api_key" name="dev_getsale_option_name[getsale_api_key]" value="%s" title="%s" />', isset($this->options['getsale_api_key']) ? esc_attr(trim($this->options['getsale_api_key'])) : '', __('Enter API Key', 'dev-getsale-popup-tool'));
    }

    public function getsale_reg_error_callback() {
        printf('<input type="text" id="getsale_reg_error" name="dev_getsale_option_name[getsale_reg_error]" value="%s" />', isset($this->options['getsale_reg_error']) ? esc_attr($this->options['getsale_reg_error']) : '');
    }

    public function getsale_project_id_callback() {
        printf('<input type="text" id="getsale_project_id" name="dev_getsale_option_name[getsale_project_id]" value="%s" />', isset($this->options['getsale_project_id']) ? esc_attr($this->options['getsale_project_id']) : '');
    }
}

function getsale_echo_before_text() {
    echo '<div id=\'before_install\' style=\'display:none;\'>' . __('GetSale Popup Tool has been successfully installed', 'dev-getsale-popup-tool') . '<br/>' . __('To get started, you must enter Email and API Key, from from your <a href=\'https://getsale.io\'>GetSale account</a>', 'dev-getsale-popup-tool') . '</div>
<div class="wrap" id="after_install" style="display:none;">
<p><b>' . __('GetSale Popup Tool', 'dev-getsale-popup-tool') . '</b> &mdash; ' . __('professional tool for creating popup windows', 'dev-getsale-popup-tool') . '</p>
<p>' . __('GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'dev-getsale-popup-tool') . '</p>
</div>
</div>
<script type=\'text/javascript\'>
    window.onload = function () {
        if (document.location.search == \'?option=com_installer&view=install\') {
            document.getElementById(\'before_install\').style.display = \'block\';
        } else document.getElementById(\'after_install\').style.display = \'block\';
    }
</script>';
}

function getsale_reg($regDomain, $email, $key, $url) {
    $domain = $regDomain;
    if (($domain == '') OR ($email == '') OR ($key == '') OR ($url == '')) {
        return;
    }

    if (!function_exists('curl_init')) {
        $json_result = '';
        $json_result->status = 'error';
        $json_result->code = 0;
        $json_result->message = 'No Curl!';
        return $json_result;
    };

    $ch = curl_init();
    $jsondata = json_encode(array(
        'email' => trim($email),
        'key' => $key,
        'url' => $url,
        'cms' => 'wordpress'));

    $options = array(
        CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept: application/json'),
        CURLOPT_URL => $domain . '/api/registration.json',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsondata,
        CURLOPT_RETURNTRANSFER => true);

    curl_setopt_array($ch, $options);
    $json_result = json_decode(curl_exec($ch));
    curl_close($ch);
    if (isset($json_result->status)) {
        if (($json_result->status == 'OK') && (isset($json_result->payload))) {
        }
        elseif ($json_result->status = 'error') {
        }
    }
    return $json_result;
}

function getsale_scripts_method() {
    $options = get_option('dev_getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_handle', plugins_url('js/main.js', __FILE__), array('jquery'));
        $datatoBePassed = array('project_id' => $options['getsale_project_id']);
        wp_localize_script('getsale_handle', 'getsale_vars', $datatoBePassed);
        wp_enqueue_script('getsale_handle');
    }
}

function getsale_scripts_add() {
    $options = get_option('dev_getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/add.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

function getsale_scripts_del() {
    $options = get_option('dev_getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/del.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

function getsale_set_default_code() {
    $options = get_option('dev_getsale_option_name');
    if (is_bool($options)) {
        $options = array();
        $options['getsale_email'] = '';
        $options['getsale_api_key'] = '';
        $options['getsale_project_id'] = '';
        $options['getsale_reg_error'] = '';
        update_option('dev_getsale_option_name', $options);
    }
}

add_action('admin_menu', 'getsale_admin_actions');

function getsale_admin_actions() {
    if (current_user_can('manage_options')) {
        if (function_exists('add_meta_box')) {
            add_menu_page('Dev GetSale Settings', 'Dev GetSale', 'manage_options', 'dev_getsale_settings', 'getsale_custom_menu_page', plugin_dir_url(__FILE__) . '/img/logo.png', 100);
        }
    }
}

function getsale_custom_menu_page() {
    $dev_getsale_settings_page = new getsaleSettingsPage();
    if (!isset($dev_getsale_settings_page)) {
        wp_die(__('Plugin GetSale has been installed incorrectly.'));
    }
    if (function_exists('add_plugins_page')) {
        add_plugins_page('Dev GetSale Settings', 'Dev GetSale', 'manage_options', 'dev_getsale_settings', array(
            &$dev_getsale_settings_page,
            'getsale_create_admin_page'));
    }
}