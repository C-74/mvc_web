<?php
class Produit {
    private $conn;
    private $table = "t_produit_pro";

    public $pro_idproduit;
    public $pro_type;
    public $pro_designation;
    public $pro_prix_ht;
    public $pro_date_arrive;
    public $pro_timestamp_arrive;
    public $pro_stock;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function lireTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY pro_idproduit ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function creer() {
        $query = "INSERT INTO " . $this->table . "
                  (pro_type, pro_designation, pro_prix_ht, pro_date_arrive, pro_stock)
                  VALUES (:type, :designation, :prix, :date_arrive, :stock)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":type", $this->pro_type);
        $stmt->bindParam(":designation", $this->pro_designation);
        $stmt->bindParam(":prix", $this->pro_prix_ht);
        $stmt->bindParam(":date_arrive", $this->pro_date_arrive);
        $stmt->bindParam(":stock", $this->pro_stock);
        return $stmt->execute();
    }

    public function modifier() {
        $query = "UPDATE " . $this->table . "
                  SET pro_type=:type, pro_designation=:designation, pro_prix_ht=:prix, pro_date_arrive=:date_arrive, pro_stock=:stock
                  WHERE pro_idproduit=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":type", $this->pro_type);
        $stmt->bindParam(":designation", $this->pro_designation);
        $stmt->bindParam(":prix", $this->pro_prix_ht);
        $stmt->bindParam(":date_arrive", $this->pro_date_arrive);
        $stmt->bindParam(":stock", $this->pro_stock);
        $stmt->bindParam(":id", $this->pro_idproduit);
        return $stmt->execute();
    }

    public function supprimer() {
        $query = "DELETE FROM " . $this->table . " WHERE pro_idproduit=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->pro_idproduit);
        return $stmt->execute();
    }

    public function lireUn() {
        $query = "SELECT * FROM " . $this->table . " WHERE pro_idproduit=:id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->pro_idproduit);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
