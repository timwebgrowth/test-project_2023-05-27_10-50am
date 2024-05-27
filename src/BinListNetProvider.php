<?php

namespace App;

use Exception;

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
        if (!$binData) {
            throw new Exception('Failed to get bin data for ' . $binId);
        }
        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
        ];

        return in_array($binData->country->alpha2, $euCountries);
    }

}