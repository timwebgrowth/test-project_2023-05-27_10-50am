<?php

require_once 'vendor/autoload.php';

use App\CurlRequestHandler;
use App\ExchangeRatesApiProvider;
use App\BinListNetProvider;
use App\CalculationController;
use PHPUnit\Framework\TestCase;

class CalculationControllerTest extends TestCase
{

    protected function setUp(): void {
        $curlRequestHandler = new CurlRequestHandler();
        $currencyRatesProvider = new ExchangeRatesApiProvider($curlRequestHandler);
        $binProvider = new BinListNetProvider($curlRequestHandler);
        $this->calculationController = new CalculationController($currencyRatesProvider, $binProvider);
    }

    public function testCalculate() {
        $testData = [
            ["bin" => "45717360", "amount" => "100.00", "currency" => "EUR"],
            ["bin" => "45417360", "amount" => "10000.00", "currency" => "JPY"],
            ["bin" => "41417360", "amount" => "130.00", "currency" => "USD"],
            ["bin" => "4745030", "amount" => "2000.00", "currency" => "GBP"],
            ["bin" => "516793", "amount" => "50.00", "currency" => "USD"]
        ];

        foreach ($testData as $transactionData) {
            $result = $this->calculationController->calculate($transactionData);
            $this->assertIsInt($result);
        }

    }
}