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

//    try {
//        $users1 = \core\User::ids( [ 1, 2 ] )->get( true );
//
//        $users2 = \config\eQual::run( 'get', 'model_collect', [ 'entity' => 'core\\User' ] );
//    } catch ( Exception $e ) {
//        print_r( [ $e->getCode(), $e->getMessage() ] );
//    }
    add_shortcode( 'eq_menu_menu', 'eq_menu_shortcode' );
} );

/**
 * Shortcode for menu.
 *
 * @param array $attributes
 *
 * @return string
 */
function eq_menu_shortcode( array $attributes ): string {

    $shortcode_attributes = shortcode_atts( [
        'menu' => 'eQual',
    ], $attributes, 'eq_menu_menu' );


    return "
        <div id=\"sb-menu\" style=\"height: 30px;\"></div>
        <div id=\"sb-container\" style=\"margin-top: 20px;\"></div>
    ";
}

function eq_menu_add_custom_page() {
    $post = [
        'post_title'   => 'eQual test page',
        'post_content' => '[eq_menu_menu menu="eQual"]',
        'post_status'  => 'publish',
        'post_author'  => 1,
        'post_type'    => 'page',
    ];

    wp_insert_post( $post );
}

register_activation_hook( __FILE__, 'eq_menu_add_custom_page' );
