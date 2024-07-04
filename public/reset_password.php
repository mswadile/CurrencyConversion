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

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user = $auth->getUserByToken($token);
    if ($user && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $auth->updatePassword($user['id'], $new_password);
            $auth->deleteToken($token);
            $message = "Your password has been reset successfully.";
        } else {
            $error = "Passwords do not match.";
        }
    }
} else {
    header("Location: ../public/forgot_password.php");
    exit();
}

include __DIR__ . '/../templates/reset_password.html';

?>