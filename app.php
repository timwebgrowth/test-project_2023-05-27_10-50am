<?php

class CurlRequestHandler {
    public function sendRequest($url, $headers = []) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($curl);
        if (!$response) {
            $errorMessage = "Failed request: " . $url;
            $curlError = curl_error($curl);
            if ($curlError) {
                $errorMessage .= "\n" . "Curl failed with error: " . $curlError;
            }
            curl_close($curl);
            throw new Exception($errorMessage);
        }
        curl_close($curl);
        return $response;
    }
}

interface CurrencyRatesProvider {
    public function getExchangeRates();
    public function getExchangeRate($currency);
}

class ExchangeRatesApiProvider implements CurrencyRatesProvider {
    private $requestHandler;

    public function __construct(CurlRequestHandler $curlRequestHandler) {
        $this->requestHandler = $curlRequestHandler;
    }

    public function getExchangeRates() {
// This part is left here because the "https://api.exchangeratesapi.io/latest" asks for API key, I have found that it
// is a mirror for 'https://api.apilayer.com/exchangerates_data/latest', created there my own key and and got
// an example data for future use, you may put here your own key, to requests actual live data.
// This is ugly, but why would I need a working API key to run the script for test project?
        $apiKey = false;
        if ($apiKey) {
            $url = "https://api.apilayer.com/exchangerates_data/latest";
            $headers = [
                "Content-Type: text/plain",
                "apikey: " . $apiKey
            ];
            $response = $this->requestHandler->sendRequest($url, $headers);
        } else {
            $response = file_get_contents("rates.txt");
        }
        return json_decode($response, true);
    }

    public function getExchangeRate($currency) {
        $allRates = $this->getExchangeRates();
        return $allRates['rates'][$currency];
    }
}

interface BinProvider {
    public function getBinData($binId);
    public function isEu($binId);
}

class BinListNetProvider implements BinProvider {
    private $requestHandler;

    public function __construct(CurlRequestHandler $curlRequestHandler) {
        $this->requestHandler = $curlRequestHandler;
    }

    public function getBinData($binId) {
        $url = 'https://lookup.binlist.net/' . $binId;
        $response = $this->requestHandler->sendRequest($url);
        return json_decode($response);
    }

    public function isEu($binId) {
        $binData = $this->getBinData($binId);

        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
        ];

        return in_array($binData->country->alpha2, $euCountries);
    }

}

$curlRequestHandler = new CurlRequestHandler();
$currencyRatesProvider = new ExchangeRatesApiProvider($curlRequestHandler);
$binProvider = new BinListNetProvider($curlRequestHandler);

$inputFile = $argv[1];
$transactions = explode("\n", file_get_contents($inputFile));

foreach ($transactions as $transaction) {
    try {
        if (empty($transaction)) {
            continue;
        }
        $transactionData = json_decode($transaction, true);
        $binId = $transactionData['bin'];
        $amount = $transactionData['amount'];
        $currency = $transactionData['currency'];

        $isEu = $binProvider->isEu($binId);

        $exchangeRate = $currencyRatesProvider->getExchangeRate($currency);

        $amountInEur = $currency === 'EUR' ? $amount : $amount / $exchangeRate;
        $commissionRate = $isEu ? 0.01 : 0.02;
        $amountWithComission = ceil(
            (($amountInEur * $commissionRate) * 100) / 100
        );

        echo $amountWithComission;
        print "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        print "\n";
    }
}