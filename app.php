<?php

require_once './vendor/autoload.php';

use App\CurlRequestHandler;
use App\ExchangeRatesApiProvider;
use App\BinListNetProvider;
use App\CommissionController;

$curlRequestHandler = new CurlRequestHandler();
$currencyRatesProvider = new ExchangeRatesApiProvider($curlRequestHandler);
$binProvider = new BinListNetProvider($curlRequestHandler);
$commissionController = new CommissionController($currencyRatesProvider, $binProvider);

echo "The 'https://api.exchangeratesapi.io/latest' endpoint is accesible only with a valid API key. \n";
echo "I could not use it. The original code was not working also because of it. \n";
echo "So I had to create a free API key on a mirror of this API service: \n";
echo "'https://api.apilayer.com/exchangerates_data/latest' \n";
echo "The key have only 100 free requests. And currently is enabled in 'config.php'. \n";
echo "If the key will expire, you will need to set a key value to 'false' in 'config.php' so the data would be used from 'rates.txt' file \n";

$inputFile = $argv[1];
$transactions = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($transactions as $transaction) {
    try {
        $transactionData = json_decode($transaction, true);
        $commission = $commissionController->calculate($transactionData);
        echo $commission . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}