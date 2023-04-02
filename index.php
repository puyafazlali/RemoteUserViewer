<?php

/**
 * Plugin Name: Remote User Viewer
 * Plugin URI:  #
 * Description: A WordPress plugin to show a table with users fetched from a 3rd-party API.
 * Author:      Puya Fazlali
 * Author URI:  https://puyafazlali.com
 * Version:     1.0.0
 * License:     GPLv3
 */

# -*- coding: utf-8 -*-

declare(strict_types=1);

namespace PuyaFazlali\RemoteUserViewer;

/**
 * Bootstraps the plugin.
 *
 * @since   1.0.0
 * @wp-hook plugins_loaded
 *
 * @return void
 */
function bootstrap()
{

    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        /**
         * Composer-generated autoload file.
         */
        include_once __DIR__ . '/vendor/autoload.php';
    }

    new RemoteUserViewer();
    new Controller();
}

add_action('plugins_loaded', __NAMESPACE__ . '\\bootstrap', 0);
