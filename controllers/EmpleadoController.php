<?php
require_once __DIR__ . '/../config/database.php';

class EmpleadoController {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function obtenerMozosDisponibles() {
        header('Content-Type: application/json');
        $sql = "SELECT id, nombre, apellido FROM empleado WHERE cargo = 'Mozo' AND estado = 'disponible'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $mozos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($mozos);
    }

    public function obtenerTodos() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM empleado");
        $stmt->execute();
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($empleados);
    }

    public function obtenerPorId($id) {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM empleado WHERE id = ?");
        $stmt->execute([$id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($empleado ?: []);
    }

    public function crear() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$this->validarDatos($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        $sql = "INSERT INTO empleado (nombre, apellido, dni, cargo, telefono, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['dni'],
            $data['cargo'],
            $data['telefono'],
            $data['estado']
        ]);

        echo json_encode(['success' => true]);
    }

    public function actualizar($id) {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$this->validarDatos($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        $sql = "UPDATE empleado SET nombre=?, apellido=?, dni=?, cargo=?, telefono=?, estado=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['dni'],
            $data['cargo'],
            $data['telefono'],
            $data['estado'],
            $id
        ]);

        echo json_encode(['success' => true]);
    }

    public function eliminar($id) {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("DELETE FROM empleado WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }

    private function validarDatos($data) {
        return isset($data['nombre'], $data['apellido'], $data['dni'], $data['cargo'], $data['telefono'], $data['estado']) &&
               strlen($data['dni']) === 8 && ctype_digit($data['dni']);
    }
}
