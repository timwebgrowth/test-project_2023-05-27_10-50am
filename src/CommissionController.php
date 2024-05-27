<?php

namespace App;

use Exception;

class CommissionController
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
        if (!isset($transactionData['bin']) || !isset($transactionData['amount']) || !isset($transactionData['currency'])) {
            throw new Exception('Invalid transaction data. Required parameters are missing.');
        }

        $binId = $transactionData['bin'];
        $amount = $transactionData['amount'];
        $currency = $transactionData['currency'];

        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception('Invalid amount. Amount must be a positive numeric value.');
        }

        if (!is_string($currency) || empty($currency)) {
            throw new Exception('Invalid currency. Currency must be a non-empty string.');
        }

        $isEu = $this->binProvider->isEu($binId);
        $exchangeRate = $this->currencyRatesProvider->getExchangeRate($currency);

        if ($exchangeRate === null) {
            throw new Exception('Exchange rate not found for the given currency.');
        }

        $amountInEur = $currency === 'EUR' ? $amount : $amount / $exchangeRate;
        $commissionRate = $isEu ? 0.01 : 0.02;
        $commission = $amountInEur * $commissionRate;
        $commission = ceil($commission * 100) / 100;

        return $commission;
    }
}