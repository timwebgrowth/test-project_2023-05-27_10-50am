<?php

namespace App;

use Exception;

class CurlRequestHandler
{
    public function sendRequest($url, $headers = [])
    {

        if (empty($url)) {
            throw new Exception("URL cannot be empty.");
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($curl);
        if ($response === false) {
            $errorMessage = "Failed request: " . $url;
            $curlError = curl_error($curl);
            if ($curlError) {
                $errorMessage .= "\n" . "Curl failed with error: " . $curlError;
            }
            curl_close($curl);
            throw new Exception($errorMessage);
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            $errorMessage = "Request failed with HTTP status code: " . $httpCode;
            if($httpCode === 401){
                $errorMessage .= "\n" . "Bad API key, check key in 'config.php'";
            }
            curl_close($curl);
            throw new Exception($errorMessage);
        }

        curl_close($curl);

        if (empty($response)) {
            throw new Exception("Empty response received.");
        }

        return $response;
    }
}