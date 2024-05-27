<?php

namespace App;

class CalculationController
{
    private $currencyRatesProvider;
    private $binProvider;

    public function __construct(CurrencyRatesProvider $currencyRatesProvider, BinProvider $binProvider)
    {
        $this->currencyRatesProvider = $currencyRatesProvider;
        $this->binProvider = $binProvider;
    }

    public function calculate($transactionData)
    {
        $binId = $transactionData['bin'];
        $amount = $transactionData['amount'];
        $currency = $transactionData['currency'];

        $isEu = $this->binProvider->isEu($binId);
        $exchangeRate = $this->currencyRatesProvider->getExchangeRate($currency);

        $amountInEur = $currency === 'EUR' ? $amount : $amount / $exchangeRate;
        $commissionRate = $isEu ? 0.01 : 0.02;
        $amountWithCommission = round($commissionRate, 2);

        return $amountWithCommission;
    }
}