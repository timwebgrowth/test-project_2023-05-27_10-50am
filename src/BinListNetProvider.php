<?php

namespace App;

use Exception;

class BinListNetProvider implements BinProvider
{
    private $requestHandler;

    public function __construct(CurlRequestHandler $curlRequestHandler)
    {
        $this->requestHandler = $curlRequestHandler;
    }

    public function getBinData($binId)
    {
        if (empty($binId) || !is_numeric($binId)) {
            throw new Exception('Invalid BIN ID.');
        }

        $url = 'https://lookup.binlist.net/' . $binId;
        $response = $this->requestHandler->sendRequest($url);

        if (!isset($response)) {
            throw new Exception('Failed to send request to the BIN provider.');
        }

        $binData = json_decode($response);

        if ($binData === null) {
            throw new Exception('Failed to parse BIN data JSON response.');
        }

        return $binData;
    }

    public function isEu($binId)
    {
        try {
            $binData = $this->getBinData($binId);
        } catch (Exception $e) {
            throw new Exception('Failed to get bin data for ' . $binId . ': ' . $e->getMessage());
        }

        if (!isset($binData->country->alpha2)) {
            throw new Exception('Country alpha2 code not found in BIN data.');
        }

        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
        ];

        return in_array($binData->country->alpha2, $euCountries);
    }
}