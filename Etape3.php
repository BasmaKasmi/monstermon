<?php

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=monster', 'root', 'Basma!2001');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function executeQuery($queryNumber, $pdo)
{
    $response = [];
    switch ($queryNumber) {
        case 1:
            // Obtenir la liste des monsters capturés classé par la date de capture (Du plus récent au plus ancien)
            $stmt = $pdo->prepare("SELECT * FROM Monster WHERE Date_de_capture IS NOT NULL ORDER BY Date_de_capture DESC");
            break;

        case 2:
            // Obtenir le nombre de monsters par type, vos deux champs devront être nommé respectivement “Type” et “Nombre”
            $stmt = $pdo->prepare("SELECT type AS Type, COUNT(*) AS Nombre FROM Monster GROUP BY type");
            break;

        case 3:
            // Obtenir pour chaque monster, son dresseur en affichant son nom et prénom dans le même champ nommé “dresseur” : si le monster n’est associé à aucun dresseur, vous devez afficher “aucun” dans ce même champ.
            $stmt = $pdo->prepare("SELECT Monster.nom AS Monster, COALESCE(CONCAT(Dresseur.nom, ' ', Dresseur.prenom), 'aucun') AS Dresseur FROM Monster LEFT JOIN Dresseur ON Monster.ID_Dresseur = Dresseur.id");
            break;

        case 4:
            // Obtenir la liste des ventes : chaque vente doit mentionner le magasin concerné, le nom du monster vendu, le dresseur avec son nom et prénom, le prix et la date de vente classée par la date de vente décroissant (Du plus récent au plus ancien)
            $stmt = $pdo->prepare("SELECT Magasin.nom AS Magasin, Monster.nom AS Monster, CONCAT(Dresseur.nom, ' ', Dresseur.prenom) AS Dresseur, Vente.Prix AS Prix, Vente.date_vente AS Date_de_vente 
                                   FROM Vente 
                                   JOIN Magasin ON Vente.magasin_id = Magasin.id 
                                   JOIN Monster ON Vente.monster_id = Monster.id 
                                   LEFT JOIN Dresseur ON Vente.dresseur_id = Dresseur.id 
                                   ORDER BY Vente.date_vente DESC");
            break;

        case 5:
            // Obtenir le chiffre d'affaires total par magasin : vous devez afficher les résultats avec un champ “Magasin” et un autre champ nommé “CA”.
            $stmt = $pdo->prepare("SELECT Magasin.nom AS Magasin, SUM(Vente.Prix) AS CA 
                                   FROM Vente 
                                   JOIN Magasin ON Vente.magasin_id = Magasin.id 
                                   GROUP BY Magasin.nom");
            break;

        case 6:
            // Obtenir les monsters ayant effectué ou non des quêtes. Vous devez obtenir également la date à laquelle elle a été effectuée tout cela classé par de la plus récente à la plus plus ancienne.
            $stmt = $pdo->prepare("SELECT Monster.nom AS Nom_Monster, Monster_quete.date AS Date_Monster_Quete 
                                   FROM Monster 
                                   LEFT JOIN Monster_quete ON Monster.id = Monster_quete.Monster_ID 
                                   ORDER BY Monster_quete.date DESC");
            break;

        case 7:
            // Obtenir la liste de tous les événements même sans participant et combats : pour chaque évènement on souhaite avoir le nombre de monster qui y participe, l'arène , le nombre d'équipement disponible pour ces évènement et le nombre de combat effectués.
            $stmt = $pdo->prepare("SELECT Evenement.nom AS Evenement_Nom, Arene.nom AS Arene, 
                                   COUNT(DISTINCT Monster_quete.Monster_ID) AS Nombre_Monster_Participants, 
                                   COUNT(DISTINCT Equipement.id) AS Nombre_Equipements, 
                                   COUNT(DISTINCT Combat.id) AS Nombre_Combats 
                                   FROM Evenement 
                                   LEFT JOIN Arene ON Evenement.Arene_id = Arene.id 
                                   LEFT JOIN Equipement ON Evenement.id = Equipement.Evenement_id 
                                   LEFT JOIN Combat ON Evenement.id = Combat.Evenement_id 
                                   LEFT JOIN Monster_quete ON Evenement.id = Monster_quete.quete_id 
                                   GROUP BY Evenement.nom, Arene.nom 
                                   ORDER BY Nombre_Monster_Participants ASC");
            break;

        case 8:
            // Obtenir la liste des monsters de type feu n’ayant pas été capturés, disposant d’un niveau compris entre 2 et 5 et lui restant plus de 400 de point de vie.
            $stmt = $pdo->prepare("SELECT * FROM Monster 
                                   WHERE type = 'feu' AND points_de_vie > 400 AND Capture = 0 AND niveau BETWEEN 2 AND 5 
                                   ORDER BY points_de_vie ASC");
            break;

        case 9:
            // Obtenir la liste des espèces ayant au moins 4 monsters.
            $stmt = $pdo->prepare("SELECT Espece.nom AS Espece, COUNT(Monster.id) AS Nombre 
                                   FROM Espece
                                   JOIN Monster ON Espece.id = Monster.Espece 
                                   GROUP BY Espece.nom 
                                   HAVING COUNT(Monster.id) >= 4");
            break;

        // Requêtes supplémentaires
        case 10:
            $stmt = $pdo->prepare("SELECT * FROM Evenement WHERE Date_de_debut > CURDATE()");
            break;

        case 11:
            $stmt = $pdo->prepare("SELECT Dresseur.nom, Dresseur.prenom, COUNT(Combat.id) AS Total_Combats, AVG(Monster.niveau) AS Average_Level 
                                   FROM Dresseur 
                                   INNER JOIN Monster ON Dresseur.id = Monster.ID_Dresseur 
                                   INNER JOIN Combat ON Monster.id = Combat.monster1_id OR Monster.id = Combat.monster2_id 
                                   GROUP BY Dresseur.id");
            break;

        case 12:
            $stmt = $pdo->prepare("SELECT Dresseur.id, Dresseur.nom, Dresseur.prenom, SUM(Monster.points_d_experience) AS Total_XP 
                                   FROM Dresseur 
                                   INNER JOIN Monster ON Dresseur.id = Monster.ID_Dresseur 
                                   GROUP BY Dresseur.id 
                                   ORDER BY Total_XP DESC");
            break;

        case 13:
            $stmt = $pdo->prepare("SELECT Espece.nom, MAX(Monster.niveau) AS Max_Level 
                                   FROM Espece 
                                   INNER JOIN Monster ON Espece.id = Monster.Espece 
                                   GROUP BY Espece.id");
            break;

        case 14:
            $stmt = $pdo->prepare("SELECT Arene.nom, MIN(Evenement.Prix) AS Min_Cost 
                                   FROM Arene 
                                   INNER JOIN Evenement ON Arene.id = Evenement.Arene_id 
                                   GROUP BY Arene.nom 
                                   HAVING MIN(Evenement.Prix) > 50");
            break;

        default:
            return json_encode(['error' => 'Requete invalide, (1 à 14)'], 400);
    }

    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response[] = $row;
    }

    return json_encode($response);
}

// Validation de l'entrée
if (!isset($argv[1])) {
    die("Erreur : Un numéro de requête est requis.");
}

// Appel de la fonction executeQuery
echo executeQuery($argv[1], $pdo);

// Fermeture de la connexion
$pdo = null;

?>
