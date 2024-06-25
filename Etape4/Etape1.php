<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'monster';
$username = 'root';
$password = 'Basma!2001';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Paramètres de pagination
    $limit = 10;  // Nombre de lignes par page
    $offset = 0;  // Point de départ (0 pour la première page)

    // Préparation de la requête avec pagination
    $stmt = $pdo->prepare("SELECT * FROM Combat ORDER BY id LIMIT :limit OFFSET :offset");

    // Liaison des paramètres
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $combats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($combats as $combat) {
        echo 'Combat ID: ' . $combat['id'] . "\n";
    }

} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>
