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
        if ($this->apiKey) {
// The original endpoint for exchange rates was not working for me, so I had to create API key for it is mirror service.
// Can be enabled in a 'config.php'
            $url = "https://api.apilayer.com/exchangerates_data/latest";
            $headers = [
                "Content-Type: text/plain",
                "apikey: " . $this->apiKey
            ];
            $response = $this->requestHandler->sendRequest($url, $headers);
        } else {
            $response = file_get_contents("rates.txt");
        }
        return json_decode($response, true);
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