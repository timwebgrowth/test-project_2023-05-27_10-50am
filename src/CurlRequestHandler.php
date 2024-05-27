<?php

namespace App;

use Exception;

class CurlRequestHandler
{
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