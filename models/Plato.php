<?php
require_once __DIR__ . '/../config/database.php';

class Plato {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function listar() {
        $sql = "SELECT id, nombre, descripcion, precio, categoria, imagen_url AS imagenUrl FROM platos";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerPorId($id) {
        $sql = "SELECT id, nombre, descripcion, precio, categoria, imagen_url AS imagenUrl 
                FROM platos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
