<?php
require __DIR__ . '/vendor/autoload.php';

use application\DefaultComponentFactory;
use yasmf\DataSource;
use yasmf\Router;

$dataSource = new DataSource(
    $host = 'statisalle-db',
    $port = 3306, 
    $db = 'statisalle', 
    $user = 'statisalle', 
    $pass = 'statisalle', 
    $charset = 'utf8mb4'

    // $host = 'localhost',       // Adresse de l'hôte pour la base locale
    // $port = 3306,              // Port (doit être un entier)
    // $db = 'statisallebd',      // Nom de la base de données
    // $user = 'root',            // Identifiant
    // $pass = 'root',            // Mot de passe
    // $charset = 'utf8mb4'       // Jeu de caractères
);

if (!class_exists('application\DefaultComponentFactory')) {
    die("Erreur : La classe DefaultComponentFactory n'est pas chargée !");
}

$router = new Router(new DefaultComponentFactory(), $dataSource);
$router->route(__DIR__, $dataSource);