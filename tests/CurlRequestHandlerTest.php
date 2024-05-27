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
}