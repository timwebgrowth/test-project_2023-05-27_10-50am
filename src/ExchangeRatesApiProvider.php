<?php

namespace App;

use Exception;

class ExchangeRatesApiProvider implements CurrencyRatesProvider
{
    private $requestHandler;
    private $apiKey;

    public function __construct(CurlRequestHandler $curlRequestHandler)
    {
        $this->requestHandler = $curlRequestHandler;
        $config = require 'config.php';
        $this->apiKey = $config['exchangeRatesApiKey'] ?? false;
    }

    public function getExchangeRates()
    {

        if (!$this->apiKey) {
            throw new Exception('API Key is not set in "config.php" file.');
        }

        $url = "http://api.exchangeratesapi.io/v1/latest?access_key=".$this->apiKey;
        $response = $this->requestHandler->sendRequest($url);

        $response = json_decode($response, true);

        return $response;
    }

    public function getExchangeRate($currency)
    {
        $allRates = $this->getExchangeRates();
        if (!isset($allRates['rates'][$currency])) {
            throw new Exception('Exchange rate not found for currency: ' . $currency);
        }
        return $allRates['rates'][$currency];
    }
}