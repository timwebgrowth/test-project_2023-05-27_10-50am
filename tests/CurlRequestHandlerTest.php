<?php

require_once './vendor/autoload.php';

use App\CurlRequestHandler;
use PHPUnit\Framework\TestCase;

class CurlRequestHandlerTest extends TestCase
{
    public function testSendRequest() {
        $curlRequestHandler = new CurlRequestHandler();
        $url = 'https://google.com';

        $response = $curlRequestHandler->sendRequest($url);

        $this->assertIsString($response);
    }

    public function testSendRequestWithEmptyUrl()
    {
        $curlRequestHandler = new CurlRequestHandler();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('URL cannot be empty.');
        $curlRequestHandler->sendRequest('');
    }

    public function testSendRequestWithInvalidUrl()
    {
        $curlRequestHandler = new CurlRequestHandler();
        $url = 'http://invalid-url';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed request: ' . $url);
        $curlRequestHandler->sendRequest($url);
    }

}