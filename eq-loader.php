<?php

/**
 * @throws Exception
 */
function load_eQual_lib() {
    static $is_equal_loaded = false;

    if ($is_equal_loaded) {
        return;
    }

    if (!function_exists('eQual::init')) {
        $eq_bootstrap = ABSPATH . '../eq.lib.php';

        if (file_exists($eq_bootstrap)) {
            if ((include_once $eq_bootstrap) === false) {
                die('eQual: missing bootstrap library.');
            }

            if (!defined('__EQ_LIB')) {
                die('fatal error: __EQ_LIB not loaded.');
            }
        }

        if (!is_callable('equal\services\Container::getInstance')) {
            die('eQual: missing mandatory service Container.');
        }

        $context = \equal\services\Container::getInstance()->get('context');

        if (!$context) {
            die('eQual: unable to retrieve mandatory dependency.');
        }

        $context->getHttpResponse();
    }

    if (!is_callable('eQual::run')) {
        throw new Exception('unable to load eQual dependencies');
    }

    $is_equal_loaded = true;
}
