<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/auth.php';

$config = include __DIR__ . '/../config/config.php';

$pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
                $config['db']['user'],
                $config['db']['password']
            );

$auth = new Auth($pdo);
//check if request method is POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if ($auth->checkIp($config['allowed_ips']) && $auth->login($username, $password, $remember)) {
        header('Location: convert.php');
        exit();
    } else {
        $error = 'Invalid login or IP not allowed';
    }

}
include __DIR__ . '/../templates/login.html';

?>