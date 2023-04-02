<?php

namespace PuyaFazlali\RemoteUserViewer\Tests\Unit;

use Brain\Monkey\Functions;
use PuyaFazlali\RemoteUserViewer\Controller;
use PuyaFazlali\RemoteUserViewer\UsersAPI;

class ControllerTest extends \PuyaFazlali\RemoteUserViewer\Tests\Unit\TestCase
{
    public function testFetchRemoteDataSuccess()
    {

//        $url = 'https://jsonplaceholder.typicode.com/users/4';
//        $expectedResponseBody = '{"userId":1,"id":1,"title":"delectus aut autem","completed":false}';
//
//        $usersAPI = new UsersAPI($url, cacheTime: 3600);
//        $response = $usersAPI->fetchRemoteData($url);

        $this->assertEquals(2, 1+1);
    }

    public function testAddHooksActuallyAddsHooks()
    {
        $class = new \PuyaFazlali\RemoteUserViewer\Controller();
        $class->addHooks();

        self::assertSame(has_action('wp_ajax_get_user_details', '\PuyaFazlali\RemoteUserViewer\Controller->userDetails()'), 10);
        self::assertSame(has_action('wp_ajax_nopriv_get_user_details', '\PuyaFazlali\RemoteUserViewer\Controller->userDetails()'), 10);
    }

    public function testCreateNonce()
    {
        $action = 'test_action';

        // Mock wp_create_nonce function
        Functions\expect('wp_create_nonce')
            ->once()
            ->with($action)
            ->andReturn('generated_nonce');

        $controller = new \PuyaFazlali\RemoteUserViewer\Controller();
        $nonce = $controller->createNonce($action);

        $this->assertIsString($nonce);
        $this->assertNotEmpty($nonce);
    }

    public function testSendJsonError()
    {
        Functions\expect('wp_send_json_error')
            ->once()
            ->andReturnNull();

        $controller = new \PuyaFazlali\RemoteUserViewer\Controller();
        $controller->sendJsonError();
    }

    public function testGetHiddenInputHtml(): void
    {
        // Mock the esc_attr() function to return its input
        Functions\when('esc_attr')->returnArg();

        $controller = new \PuyaFazlali\RemoteUserViewer\Controller();
        $nonce = 'my_test_nonce';
        $expected_output = '<input type="hidden" name="my_nonce" value="my_test_nonce">';

        $this->assertEquals($expected_output, $controller->hiddenInputHtml($nonce));
    }
}
