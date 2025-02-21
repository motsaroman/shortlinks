<?php
/**
 * Plugin Name: Short Links
 * Description: wordpress shorlinks
 * Version: 1.0.0
 * Author: Motsar Roman
 * Text Domain: short-links
 * Domain Path: /languages
 */

namespace ShortLinks;

if (!defined('ABSPATH')) {
    exit;
}
if (!session_id()) {
    session_start();
}
require_once plugin_dir_path(__FILE__) . 'includes/class-singleton.php';

final class ShortLinksPlugin extends Singleton {
    private const TEXT_DOMAIN = 'short-links';

    protected function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        ShortLinkPostType::getInstance();
        ShortLinkMetaBox::getInstance();
        ShortLinkRedirect::getInstance();
    }

    public function load_textdomain(): void {
        load_plugin_textdomain('short-links', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
}

ShortLinksPlugin::getInstance();