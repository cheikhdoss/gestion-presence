<?php

try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Création de la base de données
    $sql = "CREATE DATABASE IF NOT EXISTS gestion_presences";
    $pdo->exec($sql);
    echo "Base de données créée avec succès!\n";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Code : " . $e->getCode() . "\n";
} 