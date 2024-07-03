<?php

class CurrencyConverter
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRates()
    {
        $stmt = $this->pdo->query("SELECT * FROM exchangerates");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCodes()
    {
        $stmt = $this->pdo->query("SELECT * FROM currencies");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function convert($fromCurrency, $amount)
    {
        $rates = $this->getRates();
        $converted = [];

        foreach ($rates as $rate) {
            if ($rate['target_currency'] !== $fromCurrency) {
                $converted[] = [
                    'currency' => $rate['target_currency'],
                    'rate' => $amount * $rate['exchange_rate'],
                ];
            }
        }

        return $converted;
    }
}
