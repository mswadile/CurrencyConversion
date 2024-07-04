<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/CurrencyConverter.php';

$config = include __DIR__ . '/../config/config.php';

$pdo = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
    $config['db']['user'],
    $config['db']['password']
);

if (!isset($_SESSION['user']) && !isset($_COOKIE['user'])) {
    header('Location: login.php');
    exit();
}

$converter = new CurrencyConverter($pdo);
$currCde = $converter -> getCodes();
$rates = $converter->getRates();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromCurrency = $_POST['from_currency'];
    $amount = $_POST['amount'];
    $convertedRates = $converter->convert($fromCurrency, $amount);
    include __DIR__ . '/../templates/convert.html';
} else {
    include __DIR__ . '/../templates/convert.html';
}

?>