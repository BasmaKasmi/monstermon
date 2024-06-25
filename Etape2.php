<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=monster', 'root', 'Basma!2001');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function createTables($pdo) {
    try {
        // Table Dresseur
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Dresseur` (
            `id` INT NOT NULL,
            `nom` VARCHAR(45) NOT NULL,
            `prenom` VARCHAR(100) NOT NULL,
            `genre` ENUM('M', 'F') NOT NULL,
            `professeur_id` INT NULL,
            `est_professeur` TINYINT NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            INDEX `fk_Dresseur_Professeur_idx` (`professeur_id` ASC) VISIBLE,
            CONSTRAINT `fk_Dresseur_Professeur`
              FOREIGN KEY (`professeur_id`)
              REFERENCES `Dresseur` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Espece
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Espece` (
            `id` INT NOT NULL,
            `nom` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `nom_UNIQUE` (`nom` ASC) VISIBLE)
          ENGINE = InnoDB;");

        // Table Monster
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Monster` (
            `id` INT NOT NULL,
            `ID_Dresseur` INT NULL,
            `Espece` INT NOT NULL,
            `nom` VARCHAR(45) NOT NULL,
            `points_de_vie` INT NOT NULL,
            `type` ENUM('feu', 'eau', 'air', 'terre') NOT NULL,
            `poids` DECIMAL(10,2) NOT NULL,
            `taille` DECIMAL(10,2) NOT NULL,
            `points_de_puissance` INT NOT NULL,
            `niveau` INT NOT NULL,
            `points_d_experience` INT NOT NULL,
            `Date_de_capture` DATETIME NULL,
            `Capture` TINYINT NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Monster_Dresseur_idx` (`ID_Dresseur` ASC) VISIBLE,
            INDEX `fk_Monster_Espece_idx` (`Espece` ASC) VISIBLE,
            UNIQUE INDEX `nom_UNIQUE` (`nom` ASC) VISIBLE,
            CONSTRAINT `fk_Monster_Dresseur`
              FOREIGN KEY (`ID_Dresseur`)
              REFERENCES `Dresseur` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Monster_Espece`
              FOREIGN KEY (`Espece`)
              REFERENCES `Espece` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Localisation
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Localisation` (
            `id` INT NOT NULL,
            `adress` VARCHAR(255) NOT NULL,
            `city` VARCHAR(45) NOT NULL,
            `country` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`))
          ENGINE = InnoDB;");

        // Table Arene
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Arene` (
            `id` INT NOT NULL,
            `localisation` INT NOT NULL,
            `capacite` INT NOT NULL,
            `nom` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Arene_Localisation_idx` (`localisation` ASC) VISIBLE,
            CONSTRAINT `fk_Arene_Localisation`
              FOREIGN KEY (`localisation`)
              REFERENCES `Localisation` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Evenement
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Evenement` (
            `id` INT NOT NULL,
            `Arene_id` INT NOT NULL,
            `Date_de_debut` DATE NOT NULL,
            `Prix` DECIMAL(10,2) NOT NULL,
            `Date_de_fin` DATE NOT NULL,
            `Nom` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Evenement_Arene_idx` (`Arene_id` ASC) VISIBLE,
            CONSTRAINT `fk_Evenement_Arene`
              FOREIGN KEY (`Arene_id`)
              REFERENCES `Arene` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Combat
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Combat` (
            `id` INT NOT NULL,
            `monster1_id` INT NOT NULL,
            `monster2_id` INT NOT NULL,
            `date_combat` DATETIME NOT NULL,
            `Evenement_id` INT NULL,
            `Fin_combat` DATETIME NULL,
            `vainqueur_id` INT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Combat_monster1_idx` (`monster1_id` ASC) VISIBLE,
            INDEX `fk_Combat_monster2_idx` (`monster2_id` ASC) VISIBLE,
            INDEX `fk_Combat_Evenement_idx` (`Evenement_id` ASC) VISIBLE,
            INDEX `fk_Combat_vainqueur_idx` (`vainqueur_id` ASC) VISIBLE,
            CONSTRAINT `fk_Combat_monster1`
              FOREIGN KEY (`monster1_id`)
              REFERENCES `Monster` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Combat_monster2`
              FOREIGN KEY (`monster2_id`)
              REFERENCES `Monster` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Combat_Evenement`
              FOREIGN KEY (`Evenement_id`)
              REFERENCES `Evenement` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Combat_vainqueur`
              FOREIGN KEY (`vainqueur_id`)
              REFERENCES `Monster` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Quete
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Quete` (
            `id` INT NOT NULL,
            `nom` VARCHAR(45) NOT NULL,
            `Point_experience_gagne` INT NOT NULL,
            `Debut` DATE NOT NULL,
            `Fin` DATE NOT NULL,
            `Localisation_id` INT NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Quete_Localisation_idx` (`Localisation_id` ASC) VISIBLE,
            CONSTRAINT `fk_Quete_Localisation`
              FOREIGN KEY (`Localisation_id`)
              REFERENCES `Localisation` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Magasin
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Magasin` (
            `id` INT NOT NULL,
            `localisation` INT NOT NULL,
            `nom` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Magasin_Localisation_idx` (`localisation` ASC) VISIBLE,
            CONSTRAINT `fk_Magasin_Localisation`
              FOREIGN KEY (`localisation`)
              REFERENCES `Localisation` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Vente
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Vente` (
            `id` INT NOT NULL,
            `monster_id` INT NOT NULL,
            `magasin_id` INT NOT NULL,
            `date_vente` DATETIME NOT NULL,
            `Prix` INT NOT NULL,
            `dresseur_id` INT NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Vente_monster_idx` (`monster_id` ASC) VISIBLE,
            INDEX `fk_Vente_magasin_idx` (`magasin_id` ASC) VISIBLE,
            INDEX `fk_Vente_dresseur_idx` (`dresseur_id` ASC) VISIBLE,
            CONSTRAINT `fk_Vente_monster`
              FOREIGN KEY (`monster_id`)
              REFERENCES `Monster` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Vente_magasin`
              FOREIGN KEY (`magasin_id`)
              REFERENCES `Magasin` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Vente_dresseur`
              FOREIGN KEY (`dresseur_id`)
              REFERENCES `Dresseur` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Equipement
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Equipement` (
            `id` INT NOT NULL,
            `Nom` VARCHAR(45) NOT NULL,
            `Type` VARCHAR(45) NOT NULL,
            `Evenement_id` INT NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_Equipement_Evenement_idx` (`Evenement_id` ASC) VISIBLE,
            CONSTRAINT `fk_Equipement_Evenement`
              FOREIGN KEY (`Evenement_id`)
              REFERENCES `Evenement` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        // Table Monster_quete
        $pdo->exec("CREATE TABLE IF NOT EXISTS `Monster_quete` (
            `Monster_ID` INT NOT NULL,
            `quete_id` INT NOT NULL,
            `date` DATE NOT NULL,
            PRIMARY KEY (`Monster_ID`, `quete_id`),
            INDEX `fk_Monster_quete_Quete_idx` (`quete_id` ASC) VISIBLE,
            INDEX `fk_Monster_quete_Monster_idx` (`Monster_ID` ASC) VISIBLE,
            CONSTRAINT `fk_Monster_quete_Monster`
              FOREIGN KEY (`Monster_ID`)
              REFERENCES `Monster` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_Monster_quete_Quete`
              FOREIGN KEY (`quete_id`)
              REFERENCES `Quete` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");

        echo "Toutes les tables ont été créées avec succès.";

    } catch (PDOException $e) {
        die("Erreur lors de la création des tables : " . $e->getMessage());
    }
}

function populateTables($pdo) {
  try {
      // Insertion dans la table Dresseur
      $pdo->exec("INSERT INTO `Dresseur` (id, nom, prenom, est_professeur, professeur_id, genre) VALUES 
                  (1, 'Doe', 'John', 0, NULL, 'M')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), nom=VALUES(nom), prenom=VALUES(prenom), est_professeur=VALUES(est_professeur), professeur_id=VALUES(professeur_id), genre=VALUES(genre);");
      $pdo->exec("INSERT INTO `Dresseur` (id, nom, prenom, est_professeur, professeur_id, genre) VALUES 
                  (2, 'Smith', 'Jane', 1, NULL, 'F')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), nom=VALUES(nom), prenom=VALUES(prenom), est_professeur=VALUES(est_professeur), professeur_id=VALUES(professeur_id), genre=VALUES(genre);");

      // Insertion dans la table Espece
      $pdo->exec("INSERT INTO `Espece` (id, nom) VALUES 
                  (1, 'Espece1')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), nom=VALUES(nom);");
      $pdo->exec("INSERT INTO `Espece` (id, nom) VALUES 
                  (2, 'Espece2')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), nom=VALUES(nom);");

      // Insertion dans la table Monster
      $pdo->exec("INSERT INTO `Monster` (id, ID_Dresseur, Espece, nom, points_de_vie, type, poids, taille, points_de_puissance, niveau, points_d_experience, Date_de_capture, Capture) VALUES 
                  (1, 1, 1, 'Monster1', 100, 'feu', 50.0, 1.75, 10, 1, 0, '2023-01-01', 1)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), ID_Dresseur=VALUES(ID_Dresseur), Espece=VALUES(Espece), nom=VALUES(nom), points_de_vie=VALUES(points_de_vie), type=VALUES(type), poids=VALUES(poids), taille=VALUES(taille), points_de_puissance=VALUES(points_de_puissance), niveau=VALUES(niveau), points_d_experience=VALUES(points_d_experience), Date_de_capture=VALUES(Date_de_capture), Capture=VALUES(Capture);");
      $pdo->exec("INSERT INTO `Monster` (id, ID_Dresseur, Espece, nom, points_de_vie, type, poids, taille, points_de_puissance, niveau, points_d_experience, Date_de_capture, Capture) VALUES 
                  (2, 2, 2, 'Monster2', 100, 'feu', 50.0, 1.75, 10, 1, 0, '2023-01-01', 0)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), ID_Dresseur=VALUES(ID_Dresseur), Espece=VALUES(Espece), nom=VALUES(nom), points_de_vie=VALUES(points_de_vie), type=VALUES(type), poids=VALUES(poids), taille=VALUES(taille), points_de_puissance=VALUES(points_de_puissance), niveau=VALUES(niveau), points_d_experience=VALUES(points_d_experience), Date_de_capture=VALUES(Date_de_capture), Capture=VALUES(Capture);");

      // Insertion dans la table Localisation
      $pdo->exec("INSERT INTO `Localisation` (id, adress, city, country) VALUES 
                  (1, '123 Rue Exemple', 'Ville', 'Pays')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), adress=VALUES(adress), city=VALUES(city), country=VALUES(country);");
      $pdo->exec("INSERT INTO `Localisation` (id, adress, city, country) VALUES 
                  (2, '456 Rue Autre', 'AutreVille', 'AutrePays')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), adress=VALUES(adress), city=VALUES(city), country=VALUES(country);");

      // Insertion dans la table Arene
      $pdo->exec("INSERT INTO `Arene` (id, localisation, capacite, nom) VALUES 
                  (1, 1, 500, 'Arene1')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), localisation=VALUES(localisation), capacite=VALUES(capacite), nom=VALUES(nom);");
      $pdo->exec("INSERT INTO `Arene` (id, localisation, capacite, nom) VALUES 
                  (2, 2, 300, 'Arene2')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), localisation=VALUES(localisation), capacite=VALUES(capacite), nom=VALUES(nom);");

      // Insertion dans la table Evenement
      $pdo->exec("INSERT INTO `Evenement` (id, Arene_id, Date_de_debut, Prix, Date_de_fin, Nom) VALUES 
                  (1, 1, '2023-05-01', 100.00, '2023-05-10', 'Evenement1')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), Arene_id=VALUES(Arene_id), Date_de_debut=VALUES(Date_de_debut), Prix=VALUES(Prix), Date_de_fin=VALUES(Date_de_fin), Nom=VALUES(Nom);");
      $pdo->exec("INSERT INTO `Evenement` (id, Arene_id, Date_de_debut, Prix, Date_de_fin, Nom) VALUES 
                  (2, 2, '2023-06-01', 150.00, '2023-06-15', 'Evenement2')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), Arene_id=VALUES(Arene_id), Date_de_debut=VALUES(Date_de_debut), Prix=VALUES(Prix), Date_de_fin=VALUES(Date_de_fin), Nom=VALUES(Nom);");

      // Insertion dans la table Combat
      $pdo->exec("INSERT INTO `Combat` (id, monster1_id, monster2_id, date_combat, Evenement_id, Fin_combat, vainqueur_id) VALUES 
                  (1, 1, 2, '2023-05-05', 1, '2023-05-05', 1)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), monster1_id=VALUES(monster1_id), monster2_id=VALUES(monster2_id), date_combat=VALUES(date_combat), Evenement_id=VALUES(Evenement_id), Fin_combat=VALUES(Fin_combat), vainqueur_id=VALUES(vainqueur_id);");
      $pdo->exec("INSERT INTO `Combat` (id, monster1_id, monster2_id, date_combat, Evenement_id, Fin_combat, vainqueur_id) VALUES 
                  (2, 2, 1, '2023-06-05', 2, '2023-06-05', 2)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), monster1_id=VALUES(monster1_id), monster2_id=VALUES(monster2_id), date_combat=VALUES(date_combat), Evenement_id=VALUES(Evenement_id), Fin_combat=VALUES(Fin_combat), vainqueur_id=VALUES(vainqueur_id);");

      // Insertion dans la table Quete
      $pdo->exec("INSERT INTO `Quete` (id, nom, Point_experience_gagne, Debut, Fin, Localisation_id) VALUES 
                  (1, 'Quete1', 100, '2023-01-01', '2023-01-10', 1)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), nom=VALUES(nom), Point_experience_gagne=VALUES(Point_experience_gagne), Debut=VALUES(Debut), Fin=VALUES(Fin), Localisation_id=VALUES(Localisation_id);");
      $pdo->exec("INSERT INTO `Quete` (id, nom, Point_experience_gagne, Debut, Fin, Localisation_id) VALUES 
                  (2, 'Quete2', 200, '2023-02-01', '2023-02-10', 2)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), nom=VALUES(nom), Point_experience_gagne=VALUES(Point_experience_gagne), Debut=VALUES(Debut), Fin=VALUES(Fin), Localisation_id=VALUES(Localisation_id);");

      // Insertion dans la table Magasin
      $pdo->exec("INSERT INTO `Magasin` (id, localisation, nom) VALUES 
                  (1, 1, 'Magasin1')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), localisation=VALUES(localisation), nom=VALUES(nom);");
      $pdo->exec("INSERT INTO `Magasin` (id, localisation, nom) VALUES 
                  (2, 2, 'Magasin2')
                  ON DUPLICATE KEY UPDATE id=VALUES(id), localisation=VALUES(localisation), nom=VALUES(nom);");

      // Insertion dans la table Vente
      $pdo->exec("INSERT INTO `Vente` (id, monster_id, magasin_id, date_vente, Prix, dresseur_id) VALUES 
                  (1, 1, 1, '2023-05-01', 500.00, 1)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), monster_id=VALUES(monster_id), magasin_id=VALUES(magasin_id), date_vente=VALUES(date_vente), Prix=VALUES(Prix), dresseur_id=VALUES(dresseur_id);");
      $pdo->exec("INSERT INTO `Vente` (id, monster_id, magasin_id, date_vente, Prix, dresseur_id) VALUES 
                  (2, 2, 2, '2023-06-01', 600.00, 2)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), monster_id=VALUES(monster_id), magasin_id=VALUES(magasin_id), date_vente=VALUES(date_vente), Prix=VALUES(Prix), dresseur_id=VALUES(dresseur_id);");

      // Insertion dans la table Equipement
      $pdo->exec("INSERT INTO `Equipement` (id, Nom, Type, Evenement_id) VALUES 
                  (1, 'Equipement1', 'Type1', 1)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), Nom=VALUES(Nom), Type=VALUES(Type), Evenement_id=VALUES(Evenement_id);");
      $pdo->exec("INSERT INTO `Equipement` (id, Nom, Type, Evenement_id) VALUES 
                  (2, 'Equipement2', 'Type2', 2)
                  ON DUPLICATE KEY UPDATE id=VALUES(id), Nom=VALUES(Nom), Type=VALUES(Type), Evenement_id=VALUES(Evenement_id);");

      // Insertion dans la table Monster_quete
      $pdo->exec("INSERT INTO `Monster_quete` (Monster_ID, quete_id, date) VALUES 
                  (1, 1, '2023-01-01')
                  ON DUPLICATE KEY UPDATE Monster_ID=VALUES(Monster_ID), quete_id=VALUES(quete_id), date=VALUES(date);");
      $pdo->exec("INSERT INTO `Monster_quete` (Monster_ID, quete_id, date) VALUES 
                  (2, 2, '2023-02-01')
                  ON DUPLICATE KEY UPDATE Monster_ID=VALUES(Monster_ID), quete_id=VALUES(quete_id), date=VALUES(date);");

      echo "Toutes les tables ont été peuplées avec succès.";

  } catch (PDOException $e) {
      die("Erreur lors du peuplement des tables : " . $e->getMessage());
  }
}

function destroyTables($pdo) {
    try {
        // Table Monster_quete
        $pdo->exec("DROP TABLE IF EXISTS `Monster_quete`;");

        // Table Equipement
        $pdo->exec("DROP TABLE IF EXISTS `Equipement`;");

        // Table Vente
        $pdo->exec("DROP TABLE IF EXISTS `Vente`;");

        // Table Magasin
        $pdo->exec("DROP TABLE IF EXISTS `Magasin`;");

        // Table Quete
        $pdo->exec("DROP TABLE IF EXISTS `Quete`;");

        // Table Combat
        $pdo->exec("DROP TABLE IF EXISTS `Combat`;");

        // Table Evenement
        $pdo->exec("DROP TABLE IF EXISTS `Evenement`;");

        // Table Arene
        $pdo->exec("DROP TABLE IF EXISTS `Arene`;");

        // Table Localisation
        $pdo->exec("DROP TABLE IF EXISTS `Localisation`;");

        // Table Monster
        $pdo->exec("DROP TABLE IF EXISTS `Monster`;");

        // Table Espece
        $pdo->exec("DROP TABLE IF EXISTS `Espece`;");

        // Table Dresseur
        $pdo->exec("DROP TABLE IF EXISTS `Dresseur`;");

        echo "Toutes les tables ont été supprimées avec succès.";

    } catch (PDOException $e) {
        die("Erreur lors de la destruction des tables : " . $e->getMessage());
    }
}

/*
commande de lancement:
    php Etape2.php create
    php Etape2.php populate
    php Etape2.php destroy
*/

$action = $argv[1] ?? '';

switch ($action) {
    case "create":
        createTables($pdo);
        break;
    case "populate":
        populateTables($pdo);
        break;
    case "destroy":
        destroyTables($pdo);
        break;
    default:
        echo "Action non spécifiée ou invalide.";
}
?>
