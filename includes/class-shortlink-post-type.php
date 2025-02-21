<?php
namespace ShortLinks;

class ShortLinkPostType extends Singleton
{
    public function init()
    {
        add_action('init', [$this, 'register_post_type']);
        add_filter('manage_short_link_posts_columns', [$this, 'add_columns']);
        add_action('manage_short_link_posts_custom_column', [$this, 'fill_columns'], 10, 2);
        add_action('template_redirect', [$this, 'track_click']);
    }

    public function register_post_type()
    {
        register_post_type('short_link', [
            'labels' => [
                'name' => __('Short Links', 'short-links'),
                'singular_name' => __('Short Link', 'short-links'),
            ],
            'public' => true,
            'show_ui' => true,
            'supports' => ['title'],
            'rewrite' => ['slug' => 's', 'with_front' => false],
            'menu_position' => 20,
            'menu_icon' => 'dashicons-admin-links',
        ]);

        flush_rewrite_rules();
    }

    public function add_columns($columns)
    {
        $columns['click_count'] = __('Click Count', 'short-links');
        $columns['last_click_time'] = __('Last Click Time', 'short-links');
        $columns['short_link'] = __('Short Link', 'short-links');
        $columns['original_url'] = __('Original URL', 'short-links');
        return $columns;
    }

    public function fill_columns($column_name, $post_id)
    {
        $urlShort = get_permalink($post_id);
        $urlOriginal = get_post_meta($post_id, '_original_url', true);
        $click_count = get_post_meta($post_id, '_click_count', true);
        $last_click_time = get_post_meta($post_id, '_last_click_time', true);

        switch ($column_name) {
            case 'short_link':
                echo '<a href="' . esc_url($urlShort) . '">' . esc_url($urlShort) . '</a>';
                break;

            case 'original_url':
                echo '<a href="' . esc_url($urlOriginal) . '">' . esc_url($urlOriginal) . '</a>';
                break;

            case 'click_count':
                echo esc_html($click_count);
                break;

            case 'last_click_time':
                if ($last_click_time) {
                    echo esc_html(date('Y-m-d H:i:s', $last_click_time));
                } else {
                    echo __('No clicks yet', 'short-links');
                }
                break;
        }
    }

    public function track_click()
    {
        if (is_singular('short_link')) {
            $post_id = get_queried_object_id();
            $current_time = time();
            $click_count = (int) get_post_meta($post_id, '_click_count', true);
    
            $user_id = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown_user';

            $last_click_time_key = 'last_click_time_' . $user_id . '_' . $post_id;
            
            if (isset($_SESSION[$last_click_time_key])) {
                $last_click_time = $_SESSION[$last_click_time_key];
                
                if ($current_time - $last_click_time <= 2 * MINUTE_IN_SECONDS) {
                    return;
                }
            }

            $click_count++;
            $_SESSION[$last_click_time_key] = $current_time;
    
            update_post_meta($post_id, '_click_count', $click_count);
            update_post_meta($post_id, '_last_click_time', $current_time);
        }
    }
}