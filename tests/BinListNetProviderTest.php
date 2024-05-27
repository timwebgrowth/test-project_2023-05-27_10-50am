<?php

require_once 'vendor/autoload.php';

use App\BinListNetProvider;
use App\CurlRequestHandler;
use PHPUnit\Framework\TestCase;


class BinListNetProviderTest extends TestCase
{

    protected function setUp(): void {
        $curlRequestHandler = new CurlRequestHandler();
        $this->binProvider = new BinListNetProvider($curlRequestHandler);
    }

    public function testGetBinData() {
        $binId = '45717360';
        $expectedResponse = '{"number":{},"scheme":"visa","type":"debit","brand":"Visa Classic","country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank A/S"}}';

        $curlRequestHandler = $this->getMockBuilder(CurlRequestHandler::class)->getMock();
        $curlRequestHandler->expects($this->once())
            ->method('sendRequest')
            ->with('https://lookup.binlist.net/' . $binId)
            ->willReturn($expectedResponse);

        $result = $this->binProvider->getBinData($binId);

        $this->assertEquals(json_decode($expectedResponse), $result);
    }

    public function testIsEu() {
        $binCheckArr = array(
            '45717360' => true,
            '50988655' => false,
            '45417360' => true,
            '41417360' => false
        );

        foreach ($binCheckArr as $binId => $assertResult) {
            $result = $this->binProvider->isEu($binId);

            $this->assertEquals($result, $assertResult, "Failed testIsEu for binId: $binId");
        }
    }
}