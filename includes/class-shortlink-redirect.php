<?php

namespace ShortLinks;

class ShortLinkRedirect extends Singleton
{
    public function __construct()
    {
        add_action('template_redirect', [$this, 'handle_redirect']);
    }

    public function handle_redirect()
    {
        if (!is_singular('short_link')) {
            return;
        }

        global $post;
        if (!$post) {
            wp_die(__('Link not found', 'short-links'), 404);
        }

        $original_url = get_post_meta($post->ID, '_original_url', true);

        if (empty($original_url) || !filter_var($original_url, FILTER_VALIDATE_URL)) {
            wp_die(__('Link not found ', 'short-links'), 404);
        }
        if (!headers_sent()) {
            wp_redirect($original_url, 301);
            exit;
        }
    }
}