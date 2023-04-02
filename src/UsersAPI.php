<?php

declare(strict_types=1);

namespace PuyaFazlali\RemoteUserViewer;

use Exception;

/**
 * The model that fetches the users data from 3rd-party API.
 *
 * @package PuyaFazlali\RemoteUserViewer
 */
class UsersAPI
{
    /**
     * @var string The URL of the API to fetch users from.
     */
    private string $apiUrl;
    /**
     * @var int The time in seconds to cache API responses.
     */
    private int $cacheTime;

    /**
     * UsersAPI constructor.
     *
     * @param string $apiUrl The URL of the API to fetch users from.
     * @param int $cacheTime The time in seconds to cache API responses. Default: 3600
     */
    public function __construct(string $apiUrl, int $cacheTime = 3600)
    {
        $this->apiUrl = $apiUrl;
        $this->cacheTime = $cacheTime;
    }

    /**
     * Fetches the list of all the users from the API.
     *
     * @return array An array of user data or the error message
     * @throws Exception If there was an error fetching the API response.
     */
    public function fetchUsers(): array
    {
        $cacheKey = $this->cacheKey('remote_users_viewer__' . $this->apiUrl);
        $users = $this->transient($cacheKey);

        if ($users !== false) {
            return $users;
        }

        $response = $this->apiResponse($this->apiUrl);

        $users = $this->decodeJson($response);

        if ($this->isSuccess($response)) {
            $this->addTransient($cacheKey, $users);
        }

        return $users;
    }

    /**
     * Fetches the details of a single user from the API.
     *
     * @param int $userId The ID of the user to fetch.
     * @return array The details of the user or the error message.
     * @throws Exception If there was an error fetching the API response.
     */
    public function fetchSingleUserDetails(int $userId): array
    {
        $cacheKey = $this->cacheKey("remote_user_$userId");
        $userDetails = $this->transient($cacheKey);

        if ($userDetails !== false) {
            return $userDetails;
        }

        $response = $this->apiResponse("$this->apiUrl/$userId");

        $userDetails = $this->decodeJson($response);

        if ($this->isSuccess($response)) {
            $this->addTransient($cacheKey, $userDetails);
        }

        return $userDetails;
    }

    /**
     * Generates a cache key for the given key string.
     *
     * @param string $key The key string to generate a cache key for.
     * @return string The cache key.
     */
    public function cacheKey(string $key): string
    {
        return $key;
    }

    /**
     * Wrapper for the WordPress get_transient() function.
     * Retrieves a value from the cache using the given cache key.
     *
     * @param string $key The cache key to retrieve the value for.
     * @return mixed The cached value or false if it does not exist in the cache.
     */
    public function transient(string $key): mixed
    {
        return get_transient($key);
    }

    /**
     * Wrapper for the WordPress set_transient() function.
     * Sets a value in the cache using the given cache key.
     *
     * @param string $key The cache key to set the value for.
     * @param mixed $value The value to set in the cache.
     * @return bool True if the transient was set, false otherwise.
     */
    public function addTransient(string $key, $value): bool
    {
        return set_transient($key, $value, $this->cacheTime);
    }

    /**
     * Makes an API request to the given URL and returns the response.
     *
     * @param string $url The URL to make the API request to.
     * @return array|\WP_Error The API response or the errors made.
     * @throws Exception If there is an error making the API request.
     */
    public function apiResponse(string $url): array | \WP_Error
    {
        $response = $this->remoteGet($url);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        return $response;
    }

    /**
     * Decodes the given API response and returns it as an array.
     *
     * @param mixed $response The API response to decode.
     * @return array|null The decoded API response as an array, or null if there was an error.
     * @throws Exception If there is an error decoding the API response.
     */
    public function decodeJson($response): ?array
    {
        $responseBody = $this->remoteRetrieveBody($response);
        $decoded = json_decode($responseBody, true);

        if (null === $decoded) {
            throw new Exception('Error decoding API response.');
        }

        return $decoded;
    }

    /**
     * Determines if the given API response is a success.
     *
     * @param mixed $response The API response to check.
     * @return bool True if the response is a success, false otherwise.
     */
    public function isSuccess($response): bool
    {
        $statusCode = wp_remote_retrieve_response_code($response);

        return ($statusCode >= 200 && $statusCode < 300);
    }

    /**
     * Retrieve the response body from a WP_HTTP response object.
     *
     * @param mixed $response The WP_HTTP response object.
     * @return string The response body.
     */
    public function remoteRetrieveBody($response): string
    {
        return wp_remote_retrieve_body($response);
    }

    /**
     * Wrapper for the WordPress function wp_remote_get(),
     * performing an HTTP GET request using the WP_HTTP library.
     *
     * @param string $url The URL to request.
     * @return array|\WP_Error The WP_HTTP response object or WP_Error object on failure.
     */
    public function remoteGet(string $url): array | \WP_Error
    {
        return wp_remote_get($url);
    }
}
