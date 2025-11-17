<?php
class Database {
    // Informations de connexion
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // importation du fichier de connexion a la database
        $env = parse_ini_file(__DIR__ . '/../.env');
        
        $this->host = $env['DB_HOST'];
        $this->db_name = $env['DB_NAME'];
        $this->username = $env['DB_USERNAME'];
        $this->password = $env['DB_PASSWORD'];
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
        return $this->conn;
    }
}
