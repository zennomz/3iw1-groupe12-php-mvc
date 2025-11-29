<?php

try {
    $pdo = new PDO( 'pgsql:dbname=devdb;host=db' , 'devuser', 'devpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    die();
}