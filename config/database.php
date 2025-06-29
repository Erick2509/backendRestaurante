<?php
class Database {
    private $host = "mainline.proxy.rlwy.net";
    private $db_name = "railway";
    private $username = "root";
    private $password = "coKnDEOZXJWgMIRmfGZJFOokdTTjjlGb";
    private $port = "18460"; // 🔁 Agregamos el puerto
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;port=$this->port;dbname=$this->db_name",
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo "❌ Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
}
