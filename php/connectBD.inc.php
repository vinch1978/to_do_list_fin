<?php
ini_set("display_errors",1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
//connection localhost
$host="localhost";
$user="root";
$passwd="";
$bd="todolist";
*/
//connection bd alwaysdata

$host="mysql-goillotv.alwaysdata.net";
$user="goillotv_todolist";
$passwd="Strasbourg_duweb26";
$bd="goillotv_todolist";


/*test pour voir si connection ok avec gestion d'erreurs en mode exception*/
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$bd;charset=utf8", 
        $user, 
        $passwd, 
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    //echo "Connexion réussie.";
} catch (PDOException $e) {
    // En production, loggez l'erreur plutôt que de l'afficher
    echo "Erreur de connexion : " . $e->getMessage();
}

?>