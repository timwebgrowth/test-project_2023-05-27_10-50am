<?php
require_once 'vendor/autoload.php';

function curlRequest($url) {
    $curl = curl_init();
    if($url === 'https://api.exchangeratesapi.io/latest'){
// This part is left here because the "https://api.exchangeratesapi.io/latest" asks for API key, I have found that it
// is a mirror for 'https://api.apilayer.com/exchangerates_data/latest', created there my own key and and got
// an example data for future use, you may put here your own key, to requests actual live data.
// This is ugly, but why would we need a working API key to run the script for test project?
        $apiKey = false;
        if($apiKey){
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/latest",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: text/plain",
                    "apikey: " . $apiKey
                ),
                CURLOPT_RETURNTRANSFER => true,
            ));
        } else {
            return file_get_contents("rates.txt");
        }
    } else {
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        ));
    }

    $response = curl_exec($curl);

    if (!$response) {
        $errorMessage = "Failed request: " . $url;
        $curlError = curl_error($curl);
        if($curlError){
            $errorMessage = $errorMessage . "\n" . "Curl failed with error: " . $curlError;
        }
        curl_close($curl);
        throw new Exception($errorMessage);
    }

    curl_close($curl);
    return $response;
}


function isEu($c) {
    $euCountries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
    ];

    return in_array($c, $euCountries);
}

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

        $binResults = curlRequest('https://lookup.binlist.net/' .$binId);
        $binResults = json_decode($binResults);
        $isEu = isEu($binResults->country->alpha2);

        $exchangeRatesApiLatest = curlRequest('https://api.exchangeratesapi.io/latest');
        $exchangeRatesApiLatest = json_decode($exchangeRatesApiLatest, true);
        $exchangeRate = $exchangeRatesApiLatest['rates'][$currency];

        $amountInEur = $currency === 'EUR' ? $amount : $amount / $exchangeRate;
        $commissionRate = $isEu ? 0.01 : 0.02;
        $amountWithComission = ceil((($amountInEur * $commissionRate) * 100) / 100);

        echo $amountWithComission;
        print "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        print "\n";
    }
}