# WordPress Remote User Viewer Plugin


License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: WordPress, AJAX, JSON,
Tested up to: 6.1.1
Stable tag: 1.0.0
Requires PHP: 8.1.9

> A WordPress plugin to show a table with users fetched from a 3rd-party API.

## Description

This is a WordPress plugin that displays a list of users obtained from a 3rd-party API endpoint in an HTML table. The table includes a link for each user that, once clicked, displays additional details about the user without reloading the page.

## Installation

1. Clone this repository to the `**wp-content/plugins**` directory of your WordPress installation.

```
git clone https://github.com/PuyaFazlali
```

2. Install the plugin's dependencies by running the following command from the root directory of the plugin.


```
composer install 
```

3. Activate the plugin in the WordPress admin panel.

## Usage

After activating the plugin, you can access the list of users by visiting a custom endpoint that looks like the following URL.


```
https://example.com/my-lovely-users-table/
```

To change the URL of the custom endpoint, you can modify the `ENDPOINT_NAME` constant in the `src/RemoteUserViewer.php` file.
Please note that if you change the name of the custom endpoint, you will need to update the permalinks in WordPress settings for the changes to take effect. This can be done by going to `Settings > Permalinks` and clicking the "Save Changes" button.
## Implementation Details

### Custom Endpoint

The plugin registers a custom endpoint using the `add_rewrite_endpoint` function. This allows us to map an arbitrary URL to a specific WordPress template file.

The custom endpoint is defined in the `src/RemoteUserViewer.php` file through the addEndpoint and customEndpointRedirect functions.


### Fetching Data

The plugin fetches user data from the `https://jsonplaceholder.typicode.com/users` endpoint using the `wp_remote_get` function. The response is then parsed as JSON and used to build an HTML table.

When the user clicks on a link in the table, the plugin makes another API request to the `https://jsonplaceholder.typicode.com/users/{id}` endpoint to fetch the details for the selected user.

The AJAX requests are made using the built-in `admin-ajax.php` file and the `wp_ajax_get_user_details` and `wp_ajax_nopriv_get_user_details` hooks.

### Frontend Technologies

The plugin uses vanilla JavaScript to handle the AJAX requests and update the user details without reloading the page. The styling is done using CSS.

### Cache

The plugin caches the API responses for 1 hour using the WordPress Transients API in `src/UsersAPI.php` file. This improves performance and reduces the number of requests made to the 3rd-party API.

### Error Handling

The plugin handles errors that may occur when fetching data from the 3rd-party API. If an error occurs, a message is displayed to the user, and the page navigation is not disrupted.

## Testing

The plugin includes automated tests that can be run using the [PHPUnit](https://phpunit.de/) , [Brain Monkey](https://github.com/Brain-WP/BrainMonkey) and [Mockery](https://github.com/mockery/mockery) testing frameworks. To run the tests, run the following command from the root directory of the plugin.

```
vendor/bin/phpunit 
```
## System Requirements
The plugin is designed to work with WordPress version 6.1.1 or later, and PHP version 8.0 or later.

### Additional Features
- Full Composer support
- Compliance with PHP_CodeSniffer and Inpsyde code style
- Object-oriented code

## License

This plugin is licensed under the GPLv3 license. See the `LICENSE` file for more information.