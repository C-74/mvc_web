<?php
class Utilisateur {
    private $conn;
    private $pdo;
    private $table = "t_utilisateur_uti";

    public $uti_idutilisateur;
    public $uti_login;
    public $uti_mdp;
    public $uti_idcompte;
    public $uti_mail;
    public $uti_date_creation;
    public $uti_date_connexion;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function lireTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY uti_idutilisateur ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function creer() {
        $query = "INSERT INTO " . $this->table . "
                  (uti_login, uti_mdp, uti_idcompte, uti_mail)
                  VALUES (:login, :mdp, :idcompte, :mail)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $this->uti_login);
        $stmt->bindParam(":mdp", $this->uti_mdp);
        $stmt->bindParam(":idcompte", $this->uti_idcompte);
        $stmt->bindParam(":mail", $this->uti_mail);
        return $stmt->execute();
    }

    public function modifier() {
        $query = "UPDATE " . $this->table . "
                  SET uti_login=:login, uti_mdp=:mdp, uti_mail=:mail
                  WHERE uti_idutilisateur=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $this->uti_login);
        $stmt->bindParam(":mdp", $this->uti_mdp);
        $stmt->bindParam(":mail", $this->uti_mail);
        $stmt->bindParam(":id", $this->uti_idutilisateur);
        return $stmt->execute();
    }

    public function supprimer() {
        $query = "DELETE FROM " . $this->table . " WHERE uti_idutilisateur=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->uti_idutilisateur);
        return $stmt->execute();
    }

    public function lireUn() {
        $query = "SELECT * FROM " . $this->table . " WHERE uti_idutilisateur=:id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->uti_idutilisateur);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupère un utilisateur par son login
    public function getUser($login) {
        $query = "SELECT * FROM " . $this->table . " WHERE uti_login = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Met a jour la date de dernière connexion
    public function updateLastConnection($idutilisateur) {
        $query = "UPDATE " . $this->table . " SET uti_date_connexion = NOW() WHERE uti_idutilisateur = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$idutilisateur]);
    }

    public function createUser($login, $email, $hashedPassword) {
        $query = "INSERT INTO " . $this->table . " (uti_login, uti_mail, uti_mdp, uti_idcompte, uti_date_creation) 
                  VALUES (:login, :mail, :mdp, :idcompte, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":mail", $email);
        $stmt->bindParam(":mdp", $hashedPassword);
        $query_idcompte = "SELECT MAX(uti_idcompte) as max_id FROM " . $this->table;
        $stmt_idcompte = $this->conn->prepare($query_idcompte);
        $stmt_idcompte->execute();
        $result = $stmt_idcompte->fetch(PDO::FETCH_ASSOC);
        $idcompte = ($result['max_id'] ?? 0) + 1;
        $stmt->bindParam(":idcompte", $idcompte);
        return $stmt->execute();
    }

    // Vérifie si le login existe déjà (au moment de la création)
    public function loginExiste($login) {
        $query = "SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_login = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$login]);
        return $stmt->fetchColumn() > 0;
    }

    // Vérifie si le mail existe déjà (au moment de la création)
    public function mailExiste($mail) {
        $query = "SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_mail = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$mail]);
        return $stmt->fetchColumn() > 0;
    }

    // Vérifie si l'ID Compte existe déjà (au moment de la création)
    public function idCompteExiste($idcompte) {
        $query = "SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_idcompte = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idcompte]);
        return $stmt->fetchColumn() > 0;
    }

    // Vérifie si le login existe déjà (au moment de la modification)
    public function loginExistePourAutre($login, $id) {
        $sql = "SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_login = :login AND uti_idutilisateur != :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':login' => $login, ':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    // Vérifie si le mail existe déjà (au moment de la modification)
    public function mailExistePourAutre($mail, $id) {
        $sql = "SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_mail = :mail AND uti_idutilisateur != :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':mail' => $mail, ':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

}
