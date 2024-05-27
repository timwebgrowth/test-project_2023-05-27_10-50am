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
$transactions = explode("\n", file_get_contents($inputFile));

foreach ($transactions as $transaction) {
    try {
        if (empty($transaction)) {
            continue;
        }
        $transactionData = json_decode($transaction, true);

        $amountWithComission = $calculationController->calculate($transactionData);

        echo $amountWithComission;
        print "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        print "\n";
    }
}