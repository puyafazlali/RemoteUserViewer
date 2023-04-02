<?php

/*
* Users Table Template
*/

declare(strict_types=1);

namespace PuyaFazlali\RemoteUserViewer\Templates\UsersTableTemplate;

use PuyaFazlali\RemoteUserViewer\Controller;

$controller = new Controller();
$usersData = $controller->displayUsersTable();

if (isset($usersData['error'])) {
    get_header();
    echo '<h3>Error: ' . esc_html($usersData['error']) . '</h3>';
    get_footer();
    return;
}

$users = $usersData;
if (!is_array($users) || empty($users)) {
    get_header();
    echo '<h3>No user data available</h3>';
    get_footer();
    return;
}

get_header();
?>

    <div class="apiuserstable">
        <table class="remote-users-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user) : ?>
                <tr data-user-id="<?php echo esc_attr($user['id']); ?>">
                    <td>
                        <a href="#" class="remote-user-details-link"
                           data-user-id="<?php echo esc_attr($user['id']); ?>">
                            <?php echo esc_html($user['id']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" class="remote-user-details-link"
                           data-user-id="<?php echo esc_attr($user['id']); ?>">
                            <?php echo esc_html($user['name']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" class="remote-user-details-link"
                           data-user-id="<?php echo esc_attr($user['id']); ?>">
                            <?php echo esc_html($user['username']); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="remote-user-details-container"></div>
    </div>

    <?php
    get_footer();
