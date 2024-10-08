<?php
/**
 * Plugin Name:     eQual - Run
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Plugin for handling eQual requests
 * Author:          AlexisVS
 * Author URI:      https://github.com/AlexisVS
 * Text Domain:     eq-run
 * Domain Path:     /
 * Version:         0.1.2
 *
 * @package         Eq_Run
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'eq-loader.php';

add_action('init', function () {
    add_shortcode('eq_test', 'eq_menu_shortcode_test');
});


/**
 * Shortcode for testing purpose.
 *
 * @return string
 * @throws Exception
 */
function eq_menu_shortcode_test(): string
{
    if (!is_user_logged_in() && !is_admin()) {
        return '<div style="padding: 50px; background-color: crimson;">You must be logged in and as a non-admin to see this content.</div>';
    }
    else {
        load_eQual_lib();

        $eq_user = \wordpress\User::search(['wordpress_user_id', '=', get_current_user_id()])->read([
            'firstname',
            'lastname',
            'login'
        ])->first(true);

        $color = empty($eq_user) ? 'crimson' : 'lightseagreen';

        $html = '<div style="padding: 50px; background-color:' . $color . '">';

        if (!empty($eq_user)) {

            $html .= '<h1>User Info</h1>';
            $html .= '<dl>';
            $html .= '<dt>User id:</dt>';
            $html .= '<dd>' . $eq_user['id'] . '</dd>';
            $html .= '<br>';
            $html .= '<dt>First Name:</dt>';
            $html .= '<dd>' . $eq_user['firstname'] . '</dd>';
            $html .= '<br>';
            $html .= '<dt>Last Name:</dt>';
            $html .= '<dd>' . $eq_user['lastname'] . '</dd>';
            $html .= '<br>';
            $html .= '<dt>Login:</dt>';
            $html .= '<dd>' . $eq_user['login'] . '</dd>';
            $html .= '</dl>';
        }
        else {
            $html .= '<h1>User not found</h1>';
        }

        $html .= '</div>';

        return $html;
    }
}

function eq_run_add_custom_page()
{
    $post_title = 'eQual test page';

    // Check if a post with the same title already exists
    $query = new WP_Query([
        'post_type'      => 'page',
        'post_status'    => 'any',
        'title'          => $post_title,
        'posts_per_page' => 1,
    ]);

    // If no posts found with the same title, insert a new post
    if (!$query->have_posts()) {
        wp_insert_post([
            'post_title'   => $post_title,
            'post_content' => '[eq_test]',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_type'    => 'page',
        ]);
    }

    // Reset post data
    wp_reset_postdata();
}

register_activation_hook(__FILE__, 'eq_run_add_custom_page');


