<?php 
class Historique {
    private $conn;
    private $table = "t_historique_prix_hpr";

    public $hpr_idhistorique;
    public $hpr_idproduit;
    public $hpr_prix_ht;
    public $hpr_date_modification;


    public function __construct($db) {
        $this->conn = $db;
    }

    public function lireTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY hpr_idhistorique ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lireDepuisProduit($idproduit) {
        $query = "SELECT * FROM " . $this->table . " WHERE hpr_idproduit=:id ORDER BY hpr_date_modification DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $idproduit);
        $stmt->execute();
        return $stmt;
    }

    public function creer() {
        $query = "INSERT INTO " . $this->table . "
                  (hpr_idproduit, hpr_prix_ht)
                  VALUES (:idproduit, :prix_ht)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idproduit", $this->hpr_idproduit);
        $stmt->bindParam(":prix_ht", $this->hpr_prix_ht);
        return $stmt->execute();
    }

    // Pas de modification pour l'historique
    // + suppression automatique dès qu'un produit est supprimé (ON DELETE CASCADE)
}