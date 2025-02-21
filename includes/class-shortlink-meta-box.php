<?php

namespace ShortLinks;

class ShortLinkMetaBox extends Singleton {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box']);
    }

    public function add_meta_box() {
        add_meta_box('short_link_meta', __('Short Link Data', 'short-links'), [$this, 'render_meta_box'], 'short_link');
    }

    public function render_meta_box($post) {
        $original_url = get_post_meta($post->ID, '_original_url', true);
        echo sprintf('<input type="url" name="original_url" value="%s" style="width:100%%;" required>', esc_url($original_url));
    }

    public function save_meta_box($post_id) {
        if (array_key_exists('original_url', $_POST)) {
            update_post_meta($post_id, '_original_url', esc_url_raw($_POST['original_url']));
        }
    }
}