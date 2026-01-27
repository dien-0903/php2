<?php 
    return [
        'host' => $_ENV['HOST'] ?:'127.0.0.1',
        'database' => $_ENV['DATABASE'] ?: 'php-lop',
        'username' => $_ENV['USERNAME'] ?: 'root',
        'password' => $_ENV['PASSWORD'] ?: ''
    ]
?>