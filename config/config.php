<?php

return[
    'db' => [
        'host' => 'localhost',
        'dbname' => 'currency_app',
        'user' => 'root',
        'password' => ''
    ],
    'allowed_ips' => ['127.0.0.1', '::1'],
    'rates_url' => 'https://floatrates.com/daily/',
    'rates_file_extension' => '.json'
];

?>