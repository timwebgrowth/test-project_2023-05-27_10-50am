<?php

namespace App;

interface BinProvider {
    public function getBinData($binId);
    public function isEu($binId);
}