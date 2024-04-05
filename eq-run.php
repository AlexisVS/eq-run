<?php
/**
 * Plugin Name:     eQual - Run
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Plugin for handling eQual requests
 * Author:          AlexisVS
 * Author URI:      https://github.com/AlexisVS
 * Text Domain:     eq-run
 * Domain Path:     /
 * Version:         0.1.0
 *
 * @package         Eq_Run
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


add_action( 'init', function () {

    if ( ! function_exists( 'eQual::init' ) ) {
        // try to include eQual boostrap library
        $eq_bootstrap = dirname( __FILE__ ) . '/../../../../eq.lib.php';

        if ( file_exists( $eq_bootstrap ) ) {
            if ( ( include_once( $eq_bootstrap ) ) === false ) {
                die( 'eQual: missing boostrap library.' );
            }

            if ( ! defined( '__EQ_LIB' ) ) {
                die( 'fatal error: __EQ_LIB not loaded.' );
            }
        }

        if ( ! is_callable( 'equal\services\Container::getInstance' ) ) {
            die( 'eQual: missing mandatory service Container.' );
        }

        $context = \equal\services\Container::getInstance()->get( 'context' );

        if ( ! $context ) {
            die( 'eQual: unable to retrieve mandatory dependency.' );
        }

        // make sure the original context holds the header of the original HTTP response (set by WORDPRESS)
        // so that it can be restored afterwards
        $context->getHttpResponse();
    }

    if ( ! is_callable( 'eQual::run' ) ) {
        throw new Exception( 'unable to load eQual dependencies' );
    }

    add_shortcode( 'eq_menu_menu', 'eq_menu_shortcode_test' );
} );


/**
 * Shortcode for testing purpose.
 *
 * @return string
 * @throws Exception
 */
function eq_menu_shortcode_test(): string {

    $user = \core\User::id( 1 )
                      ->read( [ 'firstname', 'lastname', 'login' ] )
                      ->adapt( 'json' )
                      ->first( true );

    $color = 'crimson'; // red

    if ( $user['id'] == 1 ) {
        $color = 'lightseagreen';
    }

    $html = '<div style="padding: 50px; background-color:' . $color . '">';
    $html .= '<h1>User Info</h1>';
    $html .= '<dl>';
    $html .= '<dt>User id:</dt>';
    $html .= '<dd>' . $user['id'] . '</dd>';
    $html .= '<br>';
    $html .= '<dt>First Name:</dt>';
    $html .= '<dd>' . $user['firstname'] . '</dd>';
    $html .= '<br>';
    $html .= '<dt>Last Name:</dt>';
    $html .= '<dd>' . $user['lastname'] . '</dd>';
    $html .= '<br>';
    $html .= '<dt>Login:</dt>';
    $html .= '<dd>' . $user['login'] . '</dd>';
    $html .= '</dl>';
    $html .= '</div>';


    return $html;
}

function eq_menu_add_custom_page() {
    $post = [
        'post_title'   => 'eQual test page',
        'post_content' => '[eq_test]',
        'post_status'  => 'publish',
        'post_author'  => 1,
        'post_type'    => 'page',
    ];

    // Insert the post if it does not exist
    if ( get_post( $post ) === null ) {
        wp_insert_post( $post );
    }
}

register_activation_hook( __FILE__, 'eq_menu_add_custom_page' );

