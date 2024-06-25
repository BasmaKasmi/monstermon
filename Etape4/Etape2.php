<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=monster', 'root', 'Basma!2001');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supprimer la vue StatistiqueMonsterQuete si elle existe déjà
    $pdo->exec("DROP VIEW IF EXISTS StatistiqueMonsterQuete");

    // Créer la vue StatistiqueMonsterQuete
    $sql1 = "CREATE VIEW StatistiqueMonsterQuete AS
            SELECT Monster.nom AS Nom_Monster, Monster_quete.date AS Date_Monster_Quete
            FROM Monster
            LEFT JOIN Monster_quete ON Monster.id = Monster_quete.Monster_ID
            ORDER BY Monster_quete.date DESC";
    $pdo->exec($sql1);

    echo "La vue StatistiqueMonsterQuete a été créée avec succès\n";

    // Supprimer la vue StatistiqueEvenement si elle existe déjà
    $pdo->exec("DROP VIEW IF EXISTS StatistiqueEvenement");

    // Créer la vue StatistiqueEvenement
    $sql2 = "CREATE VIEW StatistiqueEvenement AS
            SELECT Evenement.nom AS Evenement_Nom, Arene.nom AS Arene, 
                   COUNT(DISTINCT Monster_quete.Monster_ID) AS Nombre_Monster_Participants, 
                   COUNT(DISTINCT Equipement.id) AS Nombre_Equipements, 
                   COUNT(DISTINCT Combat.id) AS Nombre_Combats 
            FROM Evenement 
            LEFT JOIN Arene ON Evenement.Arene_id = Arene.id 
            LEFT JOIN Equipement ON Evenement.id = Equipement.Evenement_id 
            LEFT JOIN Combat ON Evenement.id = Combat.Evenement_id 
            LEFT JOIN Monster_quete ON Evenement.id = Monster_quete.quete_id 
            GROUP BY Evenement.nom, Arene.nom 
            ORDER BY Nombre_Monster_Participants ASC";
    $pdo->exec($sql2);

    echo "La vue StatistiqueEvenement a été créée avec succès\n";

} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>
