<?php

declare(strict_types=1);

namespace PuyaFazlali\RemoteUserViewer;

use Exception;

class RemoteUserViewer
{
    private const ENDPOINT_NAME = 'my-lovely-users-table';

    /**
     * Adds hooks on initialization
     */
    public function __construct()
    {
        $this->addHooks();
    }

    /**
     * Adds hooks for registering scripts and styles and setting up the custom endpoint.
     *
     * @return void
     */
    public function addHooks(): void
    {
        add_action('init', [$this, 'addEndpoint']);
        add_action('template_redirect', [$this, 'customEndpointRedirect']);
    }

    /**
     * Registers and enqueues the script remote-user-viewer.js and
     * localizes it with an object containing the URL for the admin-ajax.php file.
     *
     * @return void
     */
    public function enqueueScripts(): void
    {
        wp_register_script(
            'remote-user-viewer',
            $this->pluginDirUrl() . '../assets/js/remote-user-viewer.js',
            ['jquery'],
            '1.0',
            true
        );
        wp_enqueue_script('remote-user-viewer');
        wp_localize_script(
            'remote-user-viewer',
            'remote_user_viewer_vars',
            ['ajaxurl' => admin_url('admin-ajax.php')]
        );
    }

    /**
     * Registers and enqueues the style main.css.
     *
     * @return void
     */
    public function enqueueStyle(): void
    {
        wp_register_style(
            'remote-user-viewer',
            $this->pluginDirUrl() . '../assets/css/main.css',
            [],
            '1.0'
        );
        wp_enqueue_style('remote-user-viewer');
    }

    /**
     * Adds custom endpoint.
     *
     * @return void
     */
    public function addEndpoint(): void
    {
        add_rewrite_tag('%fake_page%', '([^&]+)');
        add_rewrite_rule(
            '^' . self::ENDPOINT_NAME . '/?$',
            'index.php?fake_page=' . self::ENDPOINT_NAME,
            'top'
        );
        add_rewrite_endpoint(self::ENDPOINT_NAME, EP_ROOT);

        flush_rewrite_rules();
    }

    /**
     * Handles the custom endpoint and loads the template
     *
     * @return void
     */
    public function customEndpointRedirect(): void
    {
        $queryVars = $this->queryVars();

        if (!isset($queryVars['fake_page']) || $queryVars['fake_page'] !== self::ENDPOINT_NAME) {
            return;
        }

        $this->enqueueStyle();
        $this->enqueueScripts();
        $this->loadTemplate();
    }

    /**
     * Retrieves the query variables using the global $wp variable.
     *
     * @return array
     */
    public function queryVars(): array
    {
        global $wp;
        return $wp->query_vars;
    }

    /**
     * Loads the template file Templates/UsersTableTemplate.php and handles any exceptions
     *
     * @return void
     */
    public function loadTemplate(): void
    {
        try {
            include($this->pluginDirPath() . 'Templates/UsersTableTemplate.php');
        } catch (Exception) {
            wp_die('Something went wrong.');
        }

        exit;
    }

    /**
     * A helper method that returns the plugin directory path.
     *
     * @return string
     */
    public function pluginDirPath(): string
    {
        return plugin_dir_path(__FILE__);
    }

    /**
     * A helper method that returns the plugin directory URL.
     *
     * @return string
     */
    public function pluginDirUrl(): string
    {
        return plugin_dir_url(__FILE__);
    }

    /**
     * Flush rewrite rules on plugin activation.
     *
     * @return void
     */
    public static function activate(): void
    {
        flush_rewrite_rules();
    }
}

//
