<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/auth.php';

$config = include __DIR__ . '/config/config.php';

//set database connection
$pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
                $config['db']['user'],
                $config['db']['password']
            );

$auth = new Auth($pdo);

//Fetch all currency code from currency table
$stmt = $pdo -> prepare("SELECT code FROM currencies");
$stmt->execute();
$curr = $stmt -> fetchAll();

$allExchangeRates = [];
$success = true;

foreach($curr as $row){
    $currCde = $row['code'];
    $currCdeSmall = strtolower($row['code']);
    $url = $config['rates_url'] . $currCdeSmall . $config['rates_file_extension'];

    // Fetch exchange rates for the current currency
    $json = @file_get_contents($url);
    if ($json === FALSE) {
        $success = false;
        break;
    }

    $exchangeRates = json_decode($json, true);
    if ($exchangeRates === NULL) {
        $success = false;
        break;
    }

    $allExchangeRates[$currCde] = $exchangeRates;
}

if ($success) {
    // Begin transaction
    $pdo->beginTransaction();

    // Clear previous records
    $pdo->exec("DELETE FROM exchangerates");

    $insertStmt = $pdo->prepare(
        "INSERT INTO exchangerates (base_currency, target_currency, exchange_rate, date)
        VALUES (:base_currency, :target_currency, :exchange_rate, :date)
        ON DUPLICATE KEY UPDATE exchange_rate = :exchange_rate, date = :date"
    );

    foreach ($allExchangeRates as $baseCurrency => $exchangeRates) {
        foreach ($exchangeRates as $rate) {
            $targetCurrency = $rate['code'];
            $exchangeRate = $rate['rate'];
            $inverseRate = $rate['inverseRate'];
            $date = date('Y-m-d', strtotime($rate['date']));

            // Insert the exchange rate into the database
            $insertStmt->execute([
                ':base_currency' => $baseCurrency,
                ':target_currency' => $targetCurrency,
                ':exchange_rate' => $exchangeRate,
                ':date' => $date
            ]);

            // Check if inverse record already exists to avoid duplicate storage
            $inverseCheckStmt = $pdo->prepare(
                "SELECT 1 FROM exchangerates WHERE base_currency = :base_currency AND target_currency = :target_currency AND date = :date"
            );
            $inverseCheckStmt->execute([
                ':base_currency' => $targetCurrency,
                ':target_currency' => $baseCurrency,
                ':date' => $date
            ]);

            if ($inverseCheckStmt->rowCount() === 0) {
                // Insert the inverse exchange rate into the database
                $insertStmt->execute([
                    ':base_currency' => $targetCurrency,
                    ':target_currency' => $baseCurrency,
                    ':exchange_rate' => $inverseRate,
                    ':date' => $date
                ]);
            }
        }
    }
    // Commit transaction
    $pdo->commit();
    echo "Exchange rates updated successfully.";
} else {
    echo "Failed to fetch exchange rates.";
}

// Close the connection
$pdo = null;
?>