<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Auth.php';

$config = include __DIR__ . '/../config/config.php';

$pdo = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
    $config['db']['user'],
    $config['db']['password']
);

$auth = new Auth($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    if ($auth->sendPasswordResetToken($email)) {
        $link = $auth -> reset_link;
        $message = "A password reset link has been sent to your email.<br> <a href='$link'>reset</a>";
    } else {
        $error = "Email not found.";
    }
}

include __DIR__ . '/../templates/forgot_password.html';

?>