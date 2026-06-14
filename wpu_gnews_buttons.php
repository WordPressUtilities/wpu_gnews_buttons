<?php
/*
Plugin Name: WPUGnewsButtons
Plugin URI: https://github.com/WordPressUtilities/wpu_gnews_buttons
Update URI: https://github.com/WordPressUtilities/wpu_gnews_buttons
Description: Add buttons to your website to add your source to Google News and follow you on Google News.
Version: 0.1.0
Author: Darklg
Author URI: https://github.com/Darklg
Text Domain: wpu_gnews_buttons
Domain Path: /lang
Requires at least: 6.2
Requires PHP: 8.0
Network: Optional
License: MIT License
License URI: https://opensource.org/licenses/MIT
*/

if (!defined('ABSPATH')) {
    exit();
}

class WPUGnewsButtons {
    private $plugin_version = '0.1.0';
    private $plugin_settings = array(
        'id' => 'wpu_gnews_buttons',
        'name' => 'WPUGnewsButtons'
    );
    private $supported_languages = array(
        'en',
        'fr'
    );
    private $basetoolbox;
    private $settings;
    private $settings_obj;
    private $settings_details;
    private $plugin_description;

    public function __construct() {
        add_action('init', array(&$this, 'load_translation'));
        add_action('init', array(&$this, 'load_toolbox'));
        add_action('init', array(&$this, 'load_settings'));

        # Front Assets
        add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));

        # Shortcode
        add_shortcode('wpu_gnews_buttons', array(&$this, 'shortcode_wpu_gnews_buttons'));
    }

    public function load_translation() {
        $lang_dir = dirname(plugin_basename(__FILE__)) . '/lang/';
        if (strpos(__DIR__, 'mu-plugins') !== false) {
            load_muplugin_textdomain('wpu_gnews_buttons', $lang_dir);
        } else {
            load_plugin_textdomain('wpu_gnews_buttons', false, $lang_dir);
        }
        $this->plugin_description = __('Add buttons to your website to add your source to Google News and follow you on Google News.', 'wpu_gnews_buttons');
    }

    public function load_toolbox() {
        require_once __DIR__ . '/inc/WPUBaseToolbox/WPUBaseToolbox.php';
        $this->basetoolbox = new \wpu_gnews_buttons\WPUBaseToolbox(array(
            'need_form_js' => false,
            'need_table_js' => false
        ));
    }

    public function load_settings() {

        # SETTINGS
        $this->settings_details = array(
            # Admin page
            'create_page' => true,
            'plugin_basename' => plugin_basename(__FILE__),
            # Default
            'plugin_name' => $this->plugin_settings['name'],
            'plugin_id' => $this->plugin_settings['id'],
            'option_id' => $this->plugin_settings['id'] . '_options',
            'sections' => array(
                'add_source' => array(
                    'name' => __('Add source', 'wpu_gnews_buttons')
                ),
                'follow_us' => array(
                    'name' => __('Follow us', 'wpu_gnews_buttons')
                )
            )
        );

        $this->settings = array(
            'button_addsource_show' => array(
                'lang' => true,
                'label' => __('Show Button', 'wpu_gnews_buttons'),
                'type' => 'checkbox',
                'section' => 'add_source'
            ),
            'button_addsource_search_str' => array(
                'lang' => true,
                'label' => __('Source string', 'wpu_gnews_buttons'),
                'help' => sprintf(__('Defaults to %s', 'wpu_gnews_buttons'), '<code>' . $this->get_default_source() . '</code>'),
                'section' => 'add_source'
            ),
            'button_followus_show' => array(
                'lang' => true,
                'label' => __('Show Button', 'wpu_gnews_buttons'),
                'type' => 'checkbox',
                'section' => 'follow_us'
            ),
            'button_followus_search_str' => array(
                'lang' => true,
                'label' => __('Google News URL', 'wpu_gnews_buttons'),
                'type' => 'url',
                'section' => 'follow_us'
            )
        );
        require_once __DIR__ . '/inc/WPUBaseSettings/WPUBaseSettings.php';
        $this->settings_obj = new \wpu_gnews_buttons\WPUBaseSettings($this->settings_details, $this->settings);
    }

    public function wp_enqueue_scripts() {
        /* Front Style */
        wp_register_style('wpu_gnews_buttons_front_style', plugins_url('assets/css/front.css', __FILE__), array(), $this->plugin_version);
        wp_enqueue_style('wpu_gnews_buttons_front_style');
    }

    /* ----------------------------------------------------------
      Helpers
    ---------------------------------------------------------- */

    public function get_default_source() {
        $site_domain = wp_parse_url(get_site_url(), PHP_URL_HOST);
        return $site_domain;
    }

    public function get_current_language() {
        $current_lang = get_locale();
        $current_lang = substr($current_lang, 0, 2);
        if (!in_array($current_lang, $this->supported_languages)) {
            $current_lang = 'en';
        }
        return $current_lang;
    }

    /* ----------------------------------------------------------
      Front-End
    ---------------------------------------------------------- */

    public function shortcode_wpu_gnews_buttons() {

        $addsource_html = $this->get_button_addsource();
        $followus_html = $this->get_button_followus();

        $html = '';
        if ($addsource_html || $followus_html) {
            $html .= '<div class="wpu_gnews_buttons_wrapper">';
            $html .= $addsource_html;
            $html .= $followus_html;
            $html .= '</div>';
        }

        return $html;
    }

    public function get_button_addsource() {

        $settings = $this->settings_obj->get_settings();
        if (!isset($settings['button_addsource_show']) || $settings['button_addsource_show'] != '1') {
            return '';
        }

        /* Details */
        $source = $this->get_default_source();
        if (isset($settings['button_addsource_search_str']) && !empty($settings['button_addsource_search_str'])) {
            $source = $settings['button_addsource_search_str'];
        }
        $url = 'https://www.google.com/preferences/source?q=' . urlencode($source);
        $lang = $this->get_current_language();
        $image_dark = plugins_url('assets/images/addsource-dark-' . $lang . '.png', __FILE__);
        $image_light = plugins_url('assets/images/addsource-light-' . $lang . '.png', __FILE__);
        $image_html = '<img class="wpu_gnews_buttons_addsource_dark" src="' . esc_url($image_dark) . '" alt="' . esc_attr__('Add source', 'wpu_gnews_buttons') . '" />';
        $image_html .= '<img class="wpu_gnews_buttons_addsource_light" src="' . esc_url($image_light) . '" alt="' . esc_attr__('Add source', 'wpu_gnews_buttons') . '" />';

        /* HTML */
        $html = '<a class="wpu_gnews_buttons_button wpu_gnews_buttons_addsource" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">';
        $html .= $image_html;
        $html .= '</a>';

        return $html;
    }

    public function get_button_followus() {
        $settings = $this->settings_obj->get_settings();
        if (!isset($settings['button_followus_show']) || $settings['button_followus_show'] != '1') {
            return '';
        }
        if (!isset($settings['button_followus_search_str']) || !filter_var($settings['button_followus_search_str'], FILTER_VALIDATE_URL)) {
            return '';
        }

        $url = $settings['button_followus_search_str'];
        $html = '<a class="wpu_gnews_buttons_button wpu_gnews_buttons_followus" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">';
        $html .= '<img src="' . esc_url(plugins_url('assets/images/google-news.svg', __FILE__)) . '" alt="' . esc_attr__('Follow us on Google News', 'wpu_gnews_buttons') . '" />';
        $html .= '<span class="wpu_gnews_buttons_button__inner">' . sprintf(__('Follow us on %s', 'wpu_gnews_buttons'), 'Google News') . '</span>';
        $html .= '</a>';

        return $html;
    }

}

$WPUGnewsButtons = new WPUGnewsButtons();
