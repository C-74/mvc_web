CREATE TABLE `web_m1_s1`.`t_utilisateur_uti` (
    `uti_idutilisateur` INT NOT NULL AUTO_INCREMENT,
    `uti_login` TEXT NOT NULL,
    `uti_mdp` LONGTEXT NOT NULL,
    `uti_idcompte` INT NOT NULL UNIQUE,
    `uti_mail` TEXT NOT NULL,
    `uti_date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `uti_date_connexion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `uti_admin` BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`uti_idutilisateur`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `web_m1_s1`.`t_produit_pro` (
    `pro_idproduit` INT NOT NULL AUTO_INCREMENT,
    `pro_type` VARCHAR(100) NOT NULL,
    `pro_designation` VARCHAR(255) NOT NULL,
    `pro_prix_ht` DECIMAL(10,2) NOT NULL,
    `pro_promo` DECIMAL(5,2) DEFAULT 0.00,
    `pro_date_arrive` DATE NOT NULL,
    `pro_timestamp_arrive` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `pro_stock` INT DEFAULT 0,
    `pro_image` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`pro_idproduit`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table historique pour l'historique des prix
CREATE TABLE `web_m1_s1`.`t_historique_prix_hpr` (
    `hpr_idhistorique` INT NOT NULL AUTO_INCREMENT,
    `hpr_idproduit` INT NOT NULL,
    `hpr_prix_ht` DECIMAL(10,2) NOT NULL,
    `hpr_date_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`hpr_idhistorique`),
    FOREIGN KEY (`hpr_idproduit`) REFERENCES `web_m1_s1`.`t_produit_pro`(`pro_idproduit`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `web_m1_s1`.`t_produit_pro`
(`pro_type`, `pro_designation`, `pro_prix_ht`, `pro_promo`, `pro_date_arrive`, `pro_stock`, `pro_image`) VALUES
('Électronique', 'Smartphone Samsung Galaxy S24', 899.99, 10.00, '2025-10-15', 25, ''),
('Informatique', 'Ordinateur portable HP Pavilion 15', 749.50, 0.00, '2025-09-22', 12, ''),
('Accessoire', 'Casque audio Sony WH-1000XM5', 299.90, 5.00, '2025-10-30', 40, ''),
('Électroménager', 'Aspirateur Dyson V15 Detect', 649.00, 0.00, '2025-08-10', 18, ''),
('Mobilier', 'Chaise de bureau ergonomique', 199.99, 15.00, '2025-11-01', 30, '');


-- Le mdp est "password"
INSERT INTO `web_m1_s1`.`t_utilisateur_uti`
(`uti_login`, `uti_mdp`, `uti_idcompte`, `uti_mail`, `uti_admin`) VALUES
('admin', '$2a$04$/QGQHlV.Z.t13Hsc.rfiXuhwHNkKYOdjCwPURDN1Yn0VS9zUoeDCG', 1, 'admin@example.com', TRUE);