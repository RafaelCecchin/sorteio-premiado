<?php
    function getConnection() {
        
        $host = 'db';
        $dbname = 'sorteios';
        $chatset = 'utf8';
        $user = 'sistema';
        $pass = '1234';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$chatset";

        try {
            $pdo = new PDO($dsn, $user, $pass);
            return $pdo;
        } catch (PDOException $ex) {
            echo 'Erro: '.$ex->getMessage(); 
        }
    }
