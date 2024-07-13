<?php
    function getConnection() {
        
        $host = $_ENV['MYSQL_HOST'];
        $dbname = $_ENV['MYSQL_DATABASE'];
        $chatset = $_ENV['MYSQL_CHARSET'];
        $user = $_ENV['MYSQL_USER'];
        $pass = $_ENV['MYSQL_PASSWORD'];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$chatset";

        try {
            $pdo = new PDO($dsn, $user, $pass);
            return $pdo;
        } catch (PDOException $ex) {
            echo 'Erro: '.$ex->getMessage(); 
        }
    }
