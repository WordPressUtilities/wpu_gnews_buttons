<?php
defined('ABSPATH') || die;
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/* Delete options */
$options = array(
    'wpu_gnews_buttons_options',
    'wpu_gnews_buttons_wpu_gnews_buttons_version'
);
foreach ($options as $opt) {
    delete_option($opt);
    delete_site_option($opt);
}
