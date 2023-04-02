<?php

namespace PuyaFazlali\RemoteUserViewer\Tests\Unit;

use Brain\Monkey\Functions;
use Mockery\Mock;
use PuyaFazlali\RemoteUserViewer\UsersAPI;

class UsersAPITest extends \PuyaFazlali\RemoteUserViewer\Tests\Unit\TestCase
{
    public function testIsSuccessReturnsTrueWhenResponseStatusIs200()
    {
        $usersApi = new \PuyaFazlali\RemoteUserViewer\UsersAPI('https://jsonplaceholder.typicode.com/users', 3600);
        $response = [
            'response' => [
                'code' => 200,
            ],
        ];
        Functions\when('wp_remote_retrieve_response_code')->justReturn(200);

        $this->assertTrue($usersApi->isSuccess($response));
    }

    public function testIsSuccessReturnsFalseWhenResponseStatusIs400()
    {
        $usersApi = new \PuyaFazlali\RemoteUserViewer\UsersAPI('https://jsonplaceholder.typicode.com/users', 3600);
        $response = [
            'response' => [
                'code' => 400,
            ],
        ];
        Functions\when('wp_remote_retrieve_response_code')->justReturn(400);
        $this->assertFalse($usersApi->isSuccess($response));
    }

    public function testFetchUsersReturnsCachedData(): void
    {
        // Arrange
        $apiUrl = 'https://jsonplaceholder.typicode.com/users';
        $cacheTime = 3600;
        $usersAPI = new \PuyaFazlali\RemoteUserViewer\UsersAPI($apiUrl, $cacheTime);

        // Set up mock for get_transient() function
        $cachedData = array(
            array(
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
            ),
            array(
                'id' => 2,
                'name' => 'Jane Doe',
                'email' => 'janedoe@example.com',
            ),
        );
        Functions\when('get_transient')->justReturn($cachedData);

        // Set up mock for remoteGet() function
        Functions\when('remoteGet')->justReturn(false);

        // Act
        $result = $usersAPI->fetchUsers();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('email', $result[0]);
    }


}
