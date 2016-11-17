<?php

class getsaleSettingsPage {
    public $options;
    public $settings_page_name = 'getsale_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        $this->options = get_option('getsale_option_name');
    }

    public function add_plugin_page() {
        add_options_page('Settings Admin', 'GetSale', 'manage_options', $this->settings_page_name, array($this, 'create_admin_page'));
    }

    public function create_admin_page() {
        $this->options = get_option('getsale_option_name');

        if ((isset($this->options['getsale_email'])) && ('' !== $this->options['getsale_email'])) {
            $email = $this->options['getsale_email'];
        } else $email = get_option('admin_email');

        ?>
        <div id='getsale_site_url' style='display: none'><?php echo get_site_url(); ?></div>
        <div class='wrap'>
            <div id='wrapper'>
                <form id='settings_form' method='post'
                      action='<?php echo $_SERVER['REQUEST_URI'] ?>'>
                    <h1><?php _e('GetSale Popup Tool');?></h1>
                    <?php
                    getsale_echo_before_text();
                    settings_fields('getsale_option_group');
                    do_settings_sections('getsale_settings');
                    ?>
                    <input type='submit' name='submit_btn' value='<?php __('Save Settings'); ?>'>
                </form>
            </div>
        </div>
        <?php
    }

    public function page_init() {
        register_setting('getsale_option_group', 'getsale_option_name', array($this, 'sanitize'));

        add_settings_section('setting_section_id', '', // Title
            array($this, 'print_section_info'), $this->settings_page_name);

        add_settings_field('email', __('Email', 'getsale-popup-tool'), array($this, 'getsale_email_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_api_key', __('API Key', 'getsale-popup-tool'), array($this, 'getsale_api_key_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_reg_error', 'getsale_reg_error', array($this, 'getsale_reg_error_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_project_id', 'getsale_project_id', array($this, 'getsale_project_id_callback'), $this->settings_page_name, 'setting_section_id');
    }

    public function sanitize($input) {
        $new_input = array();

        if (isset($input['getsale_email'])) $new_input['getsale_email'] = $input['getsale_email'];

        if (isset($input['getsale_project_id'])) $new_input['getsale_project_id'] = $input['getsale_project_id'];

        if (isset($input['getsale_api_key'])) $new_input['getsale_api_key'] = $input['getsale_api_key'];

        if (isset($input['getsale_reg_error'])) $new_input['getsale_reg_error'] = $input['getsale_reg_error'];

        return $new_input;
    }

    public function print_section_info() {
    }

    public function getsale_email_callback() {
        printf('<input type="text" id="getsale_email" name="getsale_option_name[getsale_email]" value="%s" title="%s"/>', isset($this->options['getsale_email']) ? esc_attr($this->options['getsale_email']) : '', __('Enter Email', 'getsale-popup-tool'));
    }

    public function getsale_api_key_callback() {
        printf('<input type="text" id="getsale_api_key" name="getsale_option_name[getsale_api_key]" value="%s" title="%s" />', isset($this->options['getsale_api_key']) ? esc_attr($this->options['getsale_api_key']) : '', __('Enter API Key', 'getsale-popup-tool'));
    }

    public function getsale_reg_error_callback() {
        printf('<input type="text" id="getsale_reg_error" name="getsale_option_name[getsale_reg_error]" value="%s" />', isset($this->options['getsale_reg_error']) ? esc_attr($this->options['getsale_reg_error']) : '');
    }

    public function getsale_project_id_callback() {
        printf('<input type="text" id="getsale_project_id" name="getsale_option_name[getsale_project_id]" value="%s" />', isset($this->options['getsale_project_id']) ? esc_attr($this->options['getsale_project_id']) : '');
    }
}

function getsale_echo_before_text() {
    echo '<div id=\'before_install\' style=\'display:none;\'>' . __('GetSale Popup Tool has been successfully installed', 'getsale-popup-tool') . '<br/>' .
        __('To get started, you must enter Email and API Key, from from your <a href=\'https://getsale.io\'>GetSale account</a>', 'getsale-popup-tool') . '</div>
<div class="wrap" id="after_install" style="display:none;">
<p><b>' . __('GetSale Popup Tool', 'getsale-popup-tool') . '</b> &mdash; ' .
        __('professional tool for creating popup windows', 'getsale-popup-tool') . '</p>
<p>' . __('GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'getsale-popup-tool') . '</p>
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
    $ch = curl_init();
    $jsondata = json_encode(array(
        'email' => $email,
        'key' => $key,
        'url' => $url,
        'cms' => 'wordpress'
    ));

    $options = array(
        CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept: application/json'),
        CURLOPT_URL => $domain . '/api/registration.json',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsondata,
        CURLOPT_RETURNTRANSFER => true
    );

    curl_setopt_array($ch, $options);
    $json_result = json_decode(curl_exec($ch));
    curl_close($ch);
    if (isset($json_result->status)) {
        if (($json_result->status == 'OK') && (isset($json_result->payload))) {
        } elseif ($json_result->status = 'error') {
        }
    }
    return $json_result;
}

function getsale_scripts_method() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_handle', plugins_url('js/main.js', __FILE__), array('jquery'));

        $datatoBePassed = array('project_id' => $options['getsale_project_id']);
        wp_localize_script('getsale_handle', 'getsale_vars', $datatoBePassed);

        wp_enqueue_script('getsale_handle');
    }
}

function getsale_scripts_add() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/add.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

function getsale_scripts_del() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/del.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

function getsale_set_default_code() {
    $options = get_option('getsale_option_name');
    if (is_bool($options)) {
        $options = array();
        $options['getsale_email'] = '';
        $options['getsale_api_key'] = '';
        $options['getsale_project_id'] = '';
        $options['getsale_reg_error'] = '';
        update_option('getsale_option_name', $options);
    }
}
