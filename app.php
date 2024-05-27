<?php

require_once './vendor/autoload.php';

use App\CurlRequestHandler;
use App\ExchangeRatesApiProvider;
use App\BinListNetProvider;
use App\CalculationController;

$curlRequestHandler = new CurlRequestHandler();
$currencyRatesProvider = new ExchangeRatesApiProvider($curlRequestHandler);
$binProvider = new BinListNetProvider($curlRequestHandler);
$calculationController = new CalculationController($currencyRatesProvider, $binProvider);

$inputFile = $argv[1];
$transactions = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($transactions as $transaction) {
    try {
        $transactionData = json_decode($transaction, true);
        $amountWithCommission = $calculationController->calculate($transactionData);
        echo $amountWithCommission . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}