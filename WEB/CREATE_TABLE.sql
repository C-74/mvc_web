CREATE TABLE `web_m1_s1`.`t_utilisateur_uti` (
    `uti_idutilisateur` INT NOT NULL AUTO_INCREMENT,
    `uti_login` TEXT NOT NULL,
    `uti_mdp` LONGTEXT NOT NULL,
    `uti_idcompte` INT NOT NULL UNIQUE,
    `uti_mail` TEXT NOT NULL,
    `uti_date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `uti_date_connexion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`uti_idutilisateur`)
) ENGINE = InnoDB;

CREATE TABLE `web_m1_s1`.`t_produit_pro` (
    `pro_idproduit` INT NOT NULL AUTO_INCREMENT,
    `pro_type` VARCHAR(100) NOT NULL,
    `pro_designation` VARCHAR(255) NOT NULL,
    `pro_prix_ht` DECIMAL(10,2) NOT NULL,
    `pro_date_arrive` DATE NOT NULL,
    `pro_timestamp_arrive` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `pro_stock` INT DEFAULT 0,
    PRIMARY KEY (`pro_idproduit`)
) ENGINE = InnoDB;

INSERT INTO `web_m1_s1`.`t_produit_pro`
(`pro_type`, `pro_designation`, `pro_prix_ht`, `pro_date_arrive`, `pro_stock`) VALUES 
('Électronique', 'Smartphone Samsung Galaxy S24', 899.99, '2025-10-15', 25),
('Informatique', 'Ordinateur portable HP Pavilion 15', 749.50, '2025-09-22', 12),
('Accessoire', 'Casque audio Sony WH-1000XM5', 299.90, '2025-10-30', 40),
('Électroménager', 'Aspirateur Dyson V15 Detect', 649.00, '2025-08-10', 18),
('Mobilier', 'Chaise de bureau ergonomique', 199.99, '2025-11-01', 30);