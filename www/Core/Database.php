<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $pdo = null;

    private function __construct() {}

    private function __clone() {}

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO('pgsql:dbname=devdb;host=db', 'devuser', 'devpass');
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
                die();
            }
        }
        return self::$pdo;
    }
}