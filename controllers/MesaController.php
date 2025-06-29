<?php
require_once __DIR__ . '/../config/database.php';

class MesaController {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function obtenerMesasDisponibles() {
        header('Content-Type: application/json');
        $sql = "SELECT id, numero, capacidad FROM mesa WHERE estado = 'disponible'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($mesas);
    }
    public function obtenerTodas() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM mesa");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function obtenerPorId($id) {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM mesa WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function crear() {
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO mesa (numero, capacidad, estado) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['numero'],
            $data['capacidad'],
            $data['estado']
        ]);
        echo json_encode(['success' => true]);
    }

    public function actualizar($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "UPDATE mesa SET numero=?, capacidad=?, estado=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['numero'],
            $data['capacidad'],
            $data['estado'],
            $id
        ]);
        echo json_encode(['success' => true]);
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM mesa WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
