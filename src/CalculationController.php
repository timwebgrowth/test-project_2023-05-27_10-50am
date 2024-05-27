<?php

namespace App;

class CalculationController
{
    private $currencyRatesProvider;
    private $binProvider;

    public function __construct(ExchangeRatesApiProvider $currencyRatesProvider, BinListNetProvider $binProvider) {
        $this->currencyRatesProvider = $currencyRatesProvider;
        $this->binProvider = $binProvider;
    }

    public function calculate($transactionData) {

        $binId = $transactionData['bin'];
        $amount = $transactionData['amount'];
        $currency = $transactionData['currency'];

        $isEu = $this->binProvider->isEu($binId);

        $exchangeRate = $this->currencyRatesProvider->getExchangeRate($currency);

        $amountInEur = $currency === 'EUR' ? $amount : $amount / $exchangeRate;
        $commissionRate = $isEu ? 0.01 : 0.02;
        $amountWithComission = ceil(
            (($amountInEur * $commissionRate) * 100) / 100
        );
        return $amountWithComission;
    }
}