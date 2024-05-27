<?php

namespace App;

interface CurrencyRatesProvider {
    public function getExchangeRates();
    public function getExchangeRate($currency);
}