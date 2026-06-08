<?php
require_once "php/connectBD.inc.php";
require_once "php/afficherTaches.php";
require_once "php/doughnut.php";


// Récupération de la valeur statut envoyer par le formulaire
$statut = isset($_POST['idStatutChoisi']) ? $_POST['idStatutChoisi'] : '';

// Définition de la date actuelle au format jj/mm/aaaa 
$dateJour = date('Y-m-d'); 

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  
    <script src="js/scripts.js" defer></script>
   
    <title>To do list</title>

</head>

<body>
    
<header>
    <h1>Ma to do list rien qu'à moi</h1>
</header>
    <div>

<?php
try{
  // Requête pour sélectionner  les statuts
    $requete = $pdo->prepare("SELECT idStatut, statut FROM listestatut ");
    $requete->execute();

    // Récupération des résultats
    $listestatut = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

try{
  // Requête pour sélectionner  les priorite
    $requete = $pdo->prepare("SELECT idPriorite, priorite FROM listepriorite ");
    $requete->execute();

    // Récupération des résultats
    $listepriorite = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

try{
  // Requête pour sélectionner  les types
    $requete = $pdo->prepare("SELECT idType, `type` FROM listetype ");
    $requete->execute();

    // Récupération des résultats
    $listetype = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

try{
  // Requête pour sélectionner  la table  difficulte
    $requete = $pdo->prepare("SELECT idDifficulte, difficulte FROM niveaudifficulte ");
    $requete->execute();

    // Récupération des résultats
    $niveaudifficulte = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

try{
    //Requête pour sélectionner  la table  collegue
    $requete = $pdo->prepare("SELECT idCollegue, nomCollegue, prenomCollegue FROM listecollegue");
    $requete->execute();

    // Récupération des résultats
    $listeCollegue = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<form id="toDoListForm" action="php/ajouterTache.php" method="post">
    <fieldset>
        
        <div class="ligne"><label for="titreTache">Tâche</label><input id="titreTache" type="text" name="titreTache" placeholder="quelle est ta nouvelle tâche ?" required></div>

        <div class="groupe"><label for="dateCreation">Date création</label><input type="date" id="dateCreation" name="dateCreation" value="<?php echo $dateJour; ?>"></div>

        <div class="groupe"><label for="dateLimite">Date limite</label><input type="date" id="dateLimite" name="dateLimite" required></div> 

        <div class="groupe"><label for="idCollegue">Choisir un collégue</label><select id="idCollegue" name="idCollegue"><option value="" selected>==Choisir un collégue==</option>
            <?php
            foreach ($listeCollegue as $collegue) {
            echo '<option value="' . htmlspecialchars($collegue['idCollegue'])  . '">'  . htmlspecialchars($collegue['nomCollegue'].' '.$collegue['prenomCollegue']) . '</option>';
             }
            ?>
        </select></div>

         <div class="groupe"><label for="pourcentageTravail">pourcentage travail</label><input type="number" min="1" max="100" inputmode="numeric" id="pourcentageTravail" name="pourcentageTravail" placeholder="Indiquez un chiffre entre 1 et 100" disabled></div>
        
        <div class="groupe"><label for="idPriorite">Priorité</label><select id="idPriorite" name="idPriorite" required><option value="" disabled selected hidden>==Choisir==</option>
            <?php
            foreach ($listepriorite as $priorite) {
            echo '<option value="' . htmlspecialchars($priorite['idPriorite'])  . '">'  . htmlspecialchars($priorite['priorite']) . '</option>';
             }
            ?>
        </select></div> 

        <div class="groupe"><label for="idStatut">Statut</label><select id="idStatut" name="idStatut" required><option value="" disabled selected hidden>==Choisir==</option>
            <?php
            foreach ($listestatut as $statut) {
            echo '<option value="' . htmlspecialchars($statut['idStatut'])  . '">'  . htmlspecialchars($statut['statut']) . '</option>';
             }
            ?>
        </select></div> 

        <div class="groupe"><label for="idType">Type</label><select id="idType" name="idType" required><option value="" disabled selected hidden>==Choisir==</option>
            <?php
            foreach ($listetype as $type) {
            echo '<option value="' . htmlspecialchars($type['idType'])  . '">'  . htmlspecialchars($type['type']) . '</option>';
             }
            ?>
        </select></div> 

        <div class="groupe"><label for="idDifficulte">Difficulté</label><select id="idDifficulte" name="idDifficulte" required><option value="" disabled selected hidden>==Choisir==</option>
            <?php
            foreach ($niveaudifficulte as $difficulte) {
            echo '<option value="' . htmlspecialchars($difficulte['idDifficulte'])  . '">'  . htmlspecialchars($difficulte['difficulte']) . '</option>';
             }
             ?>
        </select></div> 

        <div class="ligne"><label for="note">Note</label> <!--petite zone de texte pour laisser une note-->
        <textarea id="note" name="note" placeholder="petite note personnelle" maxlength="200"></textarea></div>
        
        <div class="ligne"><button class="button" id="btnAjouter" type="submit">Ajouter la tâche</button></div>

</fieldset>
</form>

<details class="triage">
    <summary>Graphique par</summary>
     <div>
        <a href="index.php?graphique=difficulte">Difficulté</a>
        <a href="index.php?graphique=priorite">Priorité</a>
        <a href="index.php?graphique=statut">Statut</a>
        <a href="index.php?graphique=type">Type</a>
    </div>
</details>
<div class="graphique">
    <canvas id="graphiqueStatut"></canvas>
</div> 

<details class="triage">
    <summary>Trier par</summary>
     <div>
        <a href="index.php?tri=dateCreation">Date création</a>
        <a href="index.php?tri=dateLimite">Date limite</a>
        <a href="index.php?tri=difficulte">Difficulté</a>
        <a href="index.php?tri=priorite">Priorité</a>
        <a href="index.php?tri=statut">Statut</a>
        <a href="index.php?tri=termine">Terminé</a>
    </div>
</details>

<table class="tableau">
    <thead>
        <tr>
            <th>Titre</th>            
            <th>Date création</th>
            <th>Date limite</th>
            <th>Priorité</th>
            <th>Difficulté</th>
            <th>Type</th>
            <th>Collegue</th>
            <th>Pourcentage Travail</th>
            <th>Note</th>
            <th>Statut</th>
        </tr>
    </thead>

    <tbody id="tableauTaches">
        <!-- Si aucune tache est recupere depuis la base on affiche un message -->
        <?php if (empty($taches)) : ?>
                <tr><td colspan="10">Aucune tâche trouvée.</td></tr>
            <?php else : ?>

                <!-- Boucle qui parcourt toutes les tache recuperé depuis MySQL --> 
                <?php foreach ($taches as $tache) : ?>

                    <!-- Affichage des informations principales de la tâche -->
                    <tr style="background-color: <?php echo htmlspecialchars($tache['color']); ?>">
                        <td data-label="Titre"><?= htmlspecialchars($tache['titreTache']) ?></td>
                        <td data-label="Date création"><?= htmlspecialchars($tache['dateCreation']) ?></td>
                        <td data-label="Date limite"><?= htmlspecialchars($tache['dateLimite']) ?></td>
                        <td data-label="Priorité"><?= htmlspecialchars($tache['priorite']) ?></td>
                        <td data-label="Difficulté"><?= htmlspecialchars($tache['difficulte']) ?></td>
                        <td data-label="Type"><?= htmlspecialchars($tache['typeTache']) ?></td>
                        <td data-label="Collègue">
                            <?php if (!empty($tache['nomCollegue'])) : ?>
                            <?= htmlspecialchars($tache['nomCollegue'] . ' ' . $tache['prenomCollegue']) ?>
                            <?php else : ?>     <!--si pas de collegue on affiche des tirets-->
                            - - - - - -
                            <?php endif; ?>
                        </td>

                        <td data-label="Pourcentage travail">
                            <?php if (!empty($tache['pourcentageTravail'])) : ?>
                            <?= htmlspecialchars($tache['pourcentageTravail']) ?> %
                            <?php else : ?>     <!--si pas de pourcentage on affiche des tirets-->
                            - - - - - -
                            <?php endif; ?>
                        </td>
                        <td data-label="Note">
                        <?php if (!empty($tache['note'])) : ?>    
                        <?= htmlspecialchars($tache['note']) ?>
                        <?php else : ?>     <!--si pas de note on affiche des tirets-->
                            - - - - - -
                        <?php endif; ?>
                        </td>
                        <td data-label="Statut">
                            
                            <!--mettre les boutons pour modification statuts-->
                            <form method="POST" action="php/modification_statut.php" style="display:inline;">
                                <input type="hidden" name="idTache" value="<?= htmlspecialchars($tache['idTache'])?>">
                                 
                                <button class="statut" type="submit" name="idStatut"  value="1"<?= $tache['idStatut']==1 ? 'disabled' : ''?>>À faire</button>
                                <button class="statut" type="submit" name="idStatut" value="2"<?= $tache['idStatut']==2 ? 'disabled' : ''?>>En cours</button>
                                <button class="statut" type="submit" name="idStatut" value="3"<?= $tache['idStatut']==3 ? 'disabled' : ''?>>Terminé</button>
                                <button class="statut" type="submit" name="idStatut" value="4"<?= $tache['idStatut']==4 ? 'disabled' : ''?>>En attente</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
     </tbody>
</table> 
   
<script> const donneesGraphique = <?= $donneesGraphique ?>;
</script>
</div>

<footer>
    <p>&copy;2026 GOILLOT Vincent &amp; Dupuyoo Jean-michel <br>
    Projet to do list</p>
</footer>
</body>
</html>