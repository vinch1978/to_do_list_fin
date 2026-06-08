<?php

require_once "connectBD.inc.php";
// récupération simple des tâches depuis la table listetaches.
try {

    // Requête pour récupérer les tâches
    $sql = "SELECT idTache, titreTache, dateCreation, dateLimite, note, idPriorite, idStatut, idType, idDifficulte FROM listetaches";
    $stmt = $pdo->query($sql);
    
    // Récupération des résultats dans un tableau associatif
    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de recuperation taches : " . $e->getMessage());
}

// creation variable pour recupererle choix quand on veut trier les taches
// Vérifie si le paramètre 'tri' existe dans l'URL
$choix = isset($_GET['tri']) && !empty($_GET['tri'])
    ? $_GET['tri']
    : 'dateLimite';

//  definition des mappings autorise
$triColonne = [
    'dateLimite' => 'listetaches.dateLimite',
    'dateCreation' => 'listetaches.dateCreation',
    'difficulte' => 'listetaches.idDifficulte',
    'priorite' => 'listetaches.idPriorite',
    'statut' => 'listetaches.idStatut',
    'termine' => 'listetaches.dateLimite'
];

// On affiche avec la valeur demande sinon avec valeur par defaut
$triage = array_key_exists($choix, $triColonne) 
              ? $triColonne[$choix] 
              : 'listetaches.dateLimite';

// variable pour le filtre "termine"
if ($choix === 'termine') {
    $whereStatut = 'listetaches.idStatut = 3';
} else {
    $whereStatut = 'listetaches.idStatut <> 3';
}


// etape suivante : ajouter les jointures pour afficher priorite, statut, type, difficulte et collegues.
// jointure pour lire&relier 2 tables et affiche le texte ds indrx.php
// requete pour recupere les tache a afficher dans le tableau
// on utilise des jointure pour afficher les texte lisibles a la place des identifiants
$sql = "SELECT  listetaches.idTache, 
                listetaches.titreTache, 
                listetaches.dateCreation, 
                listetaches.dateLimite, 
                listetaches.note, 
                
                listepriorite.priorite, 
                listepriorite.color, 
                
                niveaudifficulte.difficulte, 
                
                listetype.`type` AS typeTache, 
                
                listestatut.statut,
                listetaches.idStatut,

                listecollegue.nomCollegue, 
                listecollegue.prenomCollegue, 
                tachecollegue.pourcentageTravail

FROM listetaches
INNER JOIN listepriorite
ON listetaches.idPriorite = listepriorite.idPriorite

INNER JOIN niveaudifficulte
ON listetaches.idDifficulte = niveaudifficulte.idDifficulte

INNER JOIN listetype
ON listetaches.idType = listetype.idType

INNER JOIN listestatut
ON listetaches.idStatut = listestatut.idStatut

LEFT JOIN tachecollegue
on listetaches.idTache = tachecollegue.idTache


LEFT JOIN listecollegue
ON tachecollegue.idCollegue = listecollegue.idCollegue

WHERE $whereStatut ORDER BY $triage";

// permet de lire la base de donnée pour l'afficher dans la page index.php
$stmt = $pdo->prepare($sql);
$stmt->execute();
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);// recuperation de toutes les taches sous forme de tableau associatif

?>