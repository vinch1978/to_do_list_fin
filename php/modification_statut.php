<?php

//fichier de connexion à la base de données.
require_once "connectBD.inc.php";

// On vérifie que le fichier est bien appelé par un formulaire en méthode POST et que les deux informations nécessaires existent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTache'], $_POST['idStatut'])) {
    $id = $_POST['idTache'];                // On recupere l'identifiant de la tâche envoyée par le formulaire
    $statut = (int)$_POST['idStatut'];      // On recupere le nouveau statut
    
    // Requête préparée pour éviter les injections SQL
    if ($statut >= 1 && $statut <= 4 && $id > 0)    // On vérifie que le statut reçu fait bien partie des statuts autorises
         {
    $stmt = $pdo->prepare("UPDATE listetaches SET idStatut = :statut WHERE idTache = :idTache");   // Requete preparee pour modifier le statut de la tache dans la base
                // Les marqueurs :statut et :idTache évitent les injections SQL
    $stmt->execute([':statut' => $statut, ':idTache' => $id]);        // On execute la requete en associant les marqueurs aux valeurs

    }
    // Redirection pour éviter les re-soumissions
    header("Location: ../index.php");
    exit();
}
?>