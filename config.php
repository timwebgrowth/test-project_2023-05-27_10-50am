<?php
// The ExchangeRatesApiProvider needs a provided valid API key on 'config.php'
// for 'https://api.apilayer.com/exchangerates_data/latest' to work. Otherwise, it uses the "rates.txt" file.
// The endpoint "https://api.exchangeratesapi.io/latest" asks for an API key. I have found that it is a mirror
// for 'https://api.apilayer.com/exchangerates_data/latest'. I created my own key there and got example data for
// use. However, the key is "free" and limited to 100 requests.
// I know that this is ugly, but even the original code is not working for me as it fails requests for
// exchange rates and asks for a key.

// Key for exchangeRatesLive API 3JrTcVfETVTLcIY2a3QADWCQ0Ba3TqUH, 100 requests only
return [
    'exchangeRatesApiKey' => false,
];