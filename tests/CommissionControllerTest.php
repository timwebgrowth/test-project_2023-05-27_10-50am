<?php

require_once 'vendor/autoload.php';

use App\CurlRequestHandler;
use App\ExchangeRatesApiProvider;
use App\BinListNetProvider;
use App\CommissionController;
use PHPUnit\Framework\TestCase;

class CommissionControllerTest extends TestCase
{

    protected function setUp(): void {
        $curlRequestHandler = new CurlRequestHandler();
        $currencyRatesProvider = new ExchangeRatesApiProvider($curlRequestHandler);
        $binProvider = new BinListNetProvider($curlRequestHandler);
        $this->commissionController = new CommissionController($currencyRatesProvider, $binProvider);
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
            $result = $this->commissionController->calculate($transactionData);
            $this->assertIsInt($result);
        }
    }

    public function testCalculateWithMissingParameters()
    {
        $testData = [
            ["bin" => "45717360", "amount" => "100.00"],
            ["bin" => "45417360", "currency" => "JPY"],
            ["amount" => "130.00", "currency" => "USD"],
        ];

        foreach ($testData as $transactionData) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Invalid transaction data. Required parameters are missing.');
            $this->commissionController->calculate($transactionData);
        }
    }

    public function testCalculateWithInvalidAmount()
    {
        $testData = [
            ["bin" => "45717360", "amount" => "-100.00", "currency" => "EUR"],
            ["bin" => "45417360", "amount" => "0", "currency" => "JPY"],
            ["bin" => "41417360", "amount" => "abc", "currency" => "USD"],
        ];

        foreach ($testData as $transactionData) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Invalid amount. Amount must be a positive numeric value.');
            $this->commissionController->calculate($transactionData);
        }
    }

    public function testCalculateWithInvalidCurrency()
    {
        $testData = [
            ["bin" => "45717360", "amount" => "100.00", "currency" => ""],
            ["bin" => "45417360", "amount" => "10000.00", "currency" => 123],
        ];

        foreach ($testData as $transactionData) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Invalid currency. Currency must be a non-empty string.');
            $this->commissionController->calculate($transactionData);
        }
    }
}