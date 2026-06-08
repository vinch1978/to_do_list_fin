<?php

// connection base donnée
require_once "connectBD.inc.php";

// -----permet de choisir quel type de tableau on veut ---------
// requete pour compter nbre de taches par statut
$sql ="SELECT 
    listestatut.statut,
    COUNT(*) AS NombreTaches
FROM 
    listetaches
INNER JOIN listestatut
ON listetaches.idStatut = listestatut.idStatut
GROUP BY 
    listestatut.idStatut, listestatut.statut"; 

// preparation requete de statistique puis execution
    $requete = $pdo->prepare($sql);
    $requete->execute();
    
// Récupération des résultats
    $statstatut = $requete->fetchAll(PDO::FETCH_ASSOC);

//transformation ede $statstaut en 2 tableau pour chart.js
$labels = [];
$donnees = [];

foreach ($statstatut as $ligne) {
    $labels[] = $ligne['statut'];
    $donnees[] = $ligne['NombreTaches'];
}

// convertion en JSON pour utilise direct ds chart.js
$donneesGraphique = json_encode([
    'labels' => $labels,
    'data' => $donnees
]);

//-------permet de choisir le type de donut qu'on veut----
$choix = isset($_GET['graphique']) && !empty($_GET['graphique'])
    ? $_GET['graphique']
    : 'statut';

// creation des mapping autorise
$graphiqueOptions = [
    'priorite' => [
        'label' => 'listepriorite.priorite',
        'join' => 'INNER JOIN listepriorite ON listetaches.idPriorite = listepriorite.idPriorite',
        'group' => 'listepriorite.idPriorite, listepriorite.priorite',
        'titre' => 'Répartition par priorité'],

    'difficulte' => [
        'label' => 'niveaudifficulte.difficulte',
        'join' => 'INNER JOIN niveaudifficulte ON listetaches.idDifficulte = niveaudifficulte.idDifficulte',
        'group' => 'niveaudifficulte.idDifficulte, niveaudifficulte.difficulte',
        'titre' => 'Répatition par difficulté'],

    'type' => [
        'label' => 'listetype.`type`',
        'join' => 'INNER JOIN listetype ON listetaches.idType = listetype.idType',
        'group' => 'listetype.idType, listetype.`type`',
        'titre' => 'Répartition par type'],

    'statut' =>[
        'label' => 'listestatut.statut',
        'join' => 'INNER JOIN listestatut ON listetaches.idStatut = listestatut.idStatut',
        'group' => 'listestatut.idStatut, listestatut.statut',
        'titre' => 'Répartition par statut'],
];

// On affiche avec la valeur demande sinon valeur par defaut
$graphique = array_key_exists($choix, $graphiqueOptions) 
              ? $graphiqueOptions[$choix] 
              : $graphiqueOptions['statut'];

// on recupere chaque partie de la valeur choisi
$labelGraphique = $graphique['label'];
$joinGraphique = $graphique['join'];
$groupGraphique = $graphique['group'];
$titreGraphique = $graphique['titre'];

// requete sql pour compter les taches en fonction du graphique demande
$sql = "SELECT 
        $labelGraphique AS libelle,
        COUNT(*) AS NombreTaches
    FROM listetaches
    $joinGraphique
    GROUP BY $groupGraphique";

// preparation et execution de la requete $sql
$stmt = $pdo->prepare($sql);
$stmt->execute();
$statGraphique = $stmt->fetchAll(PDO::FETCH_ASSOC);// recuperation des statistisque du graphique

$labels = [];
$donnees = [];

foreach ($statGraphique as $ligne) {
    $labels[] = $ligne['libelle'];
    $donnees[] = $ligne['NombreTaches'];
}

$donneesGraphique = json_encode([
    'labels' => $labels,
    'data' => $donnees,
    'titre' => $titreGraphique
]);
?>