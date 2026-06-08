<?php

require_once "connectBD.inc.php";

// On vérifie que la page a bien été appelée par un formulaire en méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Récupération des valeurs envoyées par le formulaire
    // L'opérateur ?? permet d'éviter une erreur si un champ n'existe pas
    $titreTache = $_POST['titreTache'] ?? '';
    $dateCreation = $_POST['dateCreation'] ?? '';
    $dateLimite = $_POST['dateLimite'] ?? '';
    $idCollegue = $_POST['idCollegue'] ?? '';
    $pourcentageTravail = $_POST['pourcentageTravail'] ?? '';
    $idPriorite = $_POST['idPriorite'] ?? '';
    $idStatut = $_POST['idStatut'] ?? '';
    $idType = $_POST['idType'] ?? '';
    $idDifficulte = $_POST['idDifficulte'] ?? '';
    $note = $_POST['note'] ?? '';

    // tableau qui contient les messages d'erreur
    $erreur = [];

    // champs obligatoires pour créer une tâche
    $champs_obligatoire = [
        'titreTache',
        'dateCreation',
        'dateLimite',
        'idPriorite',
        'idStatut',
        'idType',
        'idDifficulte'
    ];

    // Vérification des champs obligatoires
    foreach ($champs_obligatoire as $champ) {
        if (empty($_POST[$champ])) {
            $erreur[] = "Le champ " . $champ . " est obligatoire.";
        }
    }

    // Variable qui contiendra le pourcentage validé
    // Elle reste à null si aucun collègue n'est choisi
    $pourcentageValide = null;

    // Si un collègue est choisi, alors le pourcentage devient obligatoire
    if (!empty($idCollegue)) {

        // Vérification que le pourcentage est renseigné
        if ($pourcentageTravail === '') {
            $erreur[] = "Veuillez entrer un pourcentage de travail.";
        } else {

            // Validation du pourcentage compris entre 1 et 100
            $nombreValide = filter_var(
                $pourcentageTravail,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => 100
                    ]
                ]
            );

            // Si filter_var retourne false, la valeur est invalide
            if ($nombreValide === false) {
                $erreur[] = "Le pourcentage doit être un nombre entier compris entre 1 et 100.";
            } else {
                // La valeur est valide, on la garde
                $pourcentageValide = $nombreValide;
            }
        }
    }
    if (empty($erreur)) {
 
    //preparation requete avec les marqueuers ds tableau listeTache
    $stmt = $pdo->prepare('INSERT INTO listetaches (titreTache, dateCreation, dateLimite, idPriorite, idStatut, idType, idDifficulte, note) 
                            VALUES (:titreTache, :dateCreation, :dateLimite, :idPriorite, :idStatut, :idType, :idDifficulte, :note)');

    //excecution de la requete avec le tableau de donnees
    $stmt->execute([
        ':titreTache' => $titreTache,
        ':dateCreation' => $dateCreation,
        ':dateLimite' => $dateLimite,
        ':idPriorite' => $idPriorite,
        ':idStatut' => $idStatut,
        ':idType' => $idType,
        ':idDifficulte' => $idDifficulte,
        ':note' => $note
        ]);
   

    //recuperation du nouvel id de listeTaches
    $dernierIdCree = $pdo->lastInsertId();

    //utilisation id dans le tableau associatif tacheCollegue
    if (!empty($idCollegue)){
        $stmt = $pdo->prepare('INSERT INTO tachecollegue(idTache, idCollegue, pourcentageTravail)
                            VALUES (:idTache, :idCollegue, :pourcentageTravail)');
       
    $stmt->execute([
        ':idTache' => $dernierIdCree,
        ':idCollegue' => $idCollegue,
        ':pourcentageTravail' => $pourcentageValide
            ]
        );
    }

 // Redirection vers index.php
header("Location: ../index.php");
exit; // Arrête l'exécution du script
} 

    /* Test temporaire : permet de vérifier si la validation fonctionne
    if (empty($erreur)) {
        echo "Validation réussie. Les données peuvent être insérées dans la base.";
    } else {
        foreach ($erreur as $message) {
            echo "<p>" . htmlspecialchars($message) . "</p>";
        }
    }*/

} else {
    // Si quelqu'un ouvre ajouterTache.php directement sans passer par le formulaire
    echo "Aucune donnée envoyée.";
}

?> 
