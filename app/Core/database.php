<?php 

class Database {
    public static $connection;

    public static function connect() {
        try {
            $config = require APP_PATH."/config/database.php";
            $host = $config['host'];
            $database = $config['database'];
            $username = $config['username'];
            $password = $config['password'];
            $connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
          } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
          }
    }
}