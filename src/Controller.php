<?php

declare(strict_types=1);

namespace PuyaFazlali\RemoteUserViewer;

/**
 * Handles the connection between the API model and the template files.
 *
 * @package PuyaFazlali\RemoteUserViewer
 */
class Controller
{
    /**
     * API endpoint URL for fetching users
     */
    public const API_URL = 'https://jsonplaceholder.typicode.com/users';

    /**
     * Nonce action for securing AJAX requests
     */
    public const NONCE_ACTION = 'my_nonce_action';

    /**
     * Initializes the class and adds necessary hooks.
     */
    public function __construct()
    {
        $this->addHooks();
    }

    /**
     * Add WordPress actions for fetching user details via AJAX
     *
     * @return void
     */
    public function addHooks(): void
    {
        add_action('wp_ajax_get_user_details', [$this, 'userDetails']);
        add_action('wp_ajax_nopriv_get_user_details', [$this, 'userDetails']);
    }

    /**
     * Display a table of all the users fetched from the API
     *
     * @return  array An array of user data, or an error message if there was any
     */
    public function displayUsersTable(): array
    {
        $nonce = $this->createNonce(self::NONCE_ACTION);
        wp_nonce_field(self::NONCE_ACTION, 'my_nonce', true, true);

        // Instantiate the API class and fetch users data
        $apiUrl = esc_url(self::API_URL);
        $myUsers = new UsersAPI($apiUrl, 3600);
        $usersData = $myUsers->fetchUsers();

        if (is_wp_error($usersData)) {
            $errorMessage =
                'Sorry, there was an error fetching the users data. Please try again later.';
            return ['error' => $errorMessage];
        }

        if (empty($usersData)) {
            $errorMessage = 'No users found.';
            return ['error' => $errorMessage];
        }

        return $usersData;
    }

    /**
     * Fetches user details for a specific user ID via AJAX
     *
     * @return void
     */
    public function userDetails(): void
    {
        $data = wp_unslash($_POST);

        if (
            !isset($data['_wpnonce']) ||
            (!wp_verify_nonce(
                esc_html($data['_wpnonce']),
                self::NONCE_ACTION
            ))
        ) {
            $this->sendJsonError();
        }
        $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;

        if (!is_numeric($userId)) {
            $this->sendJsonError();
        }

        // Instantiate the API class and fetch user details
        $apiUrl = esc_url(self::API_URL);
        $usersApi = new UsersAPI($apiUrl, 3600);
        $userDetails = $usersApi->fetchSingleUserDetails($userId);

        if (false === $userDetails) {
            wp_send_json_error();
        }

        wp_send_json_success($userDetails);
    }

    /**
     * Generate the HTML for a hidden nonce input
     *
     * @param string $nonce The nonce to include in the input
     * @return string The HTML for the hidden input
     */
    public function hiddenInputHtml(string $nonce): string
    {
        return sprintf(
            '<input type="hidden" name="my_nonce" value="%s">',
            esc_attr($nonce)
        );
    }

    /**
     * Wrapper for WordPress wp_create_nonce() function, creating a WordPress nonce
     *
     * @param string $action The action for which to generate the nonce
     * @return string The generated nonce
     */
    public function createNonce(string $action): string
    {
        return wp_create_nonce($action);
    }

    /**
     * Wrapper for WordPress wp_send_json_error() function,
     * sending a JSON error response via WordPress
     *
     * @return void
     */
    public function sendJsonError(): void
    {
        wp_send_json_error();
    }
}
