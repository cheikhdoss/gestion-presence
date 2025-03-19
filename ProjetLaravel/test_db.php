<?php

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=gestion_presences',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    echo "Connexion réussie à la base de données!\n";
    
    // Test de création d'une table
    $pdo->exec("CREATE TABLE IF NOT EXISTS test (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50))");
    echo "Table de test créée avec succès!\n";
    
    // Test d'insertion
    $pdo->exec("INSERT INTO test (name) VALUES ('Test')");
    echo "Données insérées avec succès!\n";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Code : " . $e->getCode() . "\n";
} 