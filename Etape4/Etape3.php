<?php
// Paramètres de connexion à la base de données
$host = 'localhost';
$dbname = 'monster';
$username = 'root';
$password = 'Basma!2001';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supprimer la procédure stockée si elle existe déjà
    $pdo->exec("DROP PROCEDURE IF EXISTS MonsterType");
    $pdo->exec("DROP PROCEDURE IF EXISTS AddMonster");
    $pdo->exec("DROP PROCEDURE IF EXISTS MiseAJourMonster");

    // La commande SQL pour créer la procédure stockée MonsterType
    $sqlMonsterType = "
    CREATE PROCEDURE MonsterType(IN type_monster ENUM('feu', 'eau', 'air', 'terre'))
    BEGIN
        SELECT * FROM Monster WHERE type = type_monster;
    END";

    $pdo->exec($sqlMonsterType);

    // La commande SQL pour créer la procédure stockée AddMonster
    $sqlAddMonster = "
    CREATE PROCEDURE AddMonster (
        IN p_nom VARCHAR(45),
        IN p_points_de_vie INT,
        IN p_type ENUM('feu', 'eau', 'air', 'terre'),
        IN p_poids DECIMAL(10,2),
        IN p_taille DECIMAL(10,2),
        IN p_points_de_puissance INT,
        IN p_niveau INT,
        IN p_points_d_experience INT,
        IN p_ID_Dresseur INT,
        IN p_Espece INT,
        IN p_Capture TINYINT
    )
    BEGIN
        INSERT INTO Monster (nom, points_de_vie, type, poids, taille, points_de_puissance, niveau, points_d_experience, ID_Dresseur, Espece, Capture)
        VALUES (p_nom, p_points_de_vie, p_type, p_poids, p_taille, p_points_de_puissance, p_niveau, p_points_d_experience, p_ID_Dresseur, p_Espece, p_Capture);
    END";

    $pdo->exec($sqlAddMonster);

    // La commande SQL pour créer la procédure stockée MiseAJourMonster
    $sqlMiseAJourMonster = "
    CREATE PROCEDURE MiseAJourMonster (
        IN p_id INT,
        IN p_points_de_vie INT,
        IN p_niveau INT,
        IN p_points_de_puissance INT,
        IN p_points_d_experience INT
    )
    BEGIN
        UPDATE Monster 
        SET points_de_vie = p_points_de_vie, 
            niveau = p_niveau, 
            points_de_puissance = p_points_de_puissance, 
            points_d_experience = p_points_d_experience 
        WHERE id = p_id;
        
        SELECT * FROM Monster WHERE id = p_id;
    END";

    $pdo->exec($sqlMiseAJourMonster);

    echo "Les procédures stockées ont été créées avec succès.";

} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>
