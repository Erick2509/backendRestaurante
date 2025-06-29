<?php
require_once __DIR__ . '/../config/database.php';

class LoginController {
  private $conn;

  public function __construct() {
    $this->conn = (new Database())->getConnection();
  }

  public function login() {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
      echo json_encode([
        'success' => true,
        'usuario' => [
          'id' => $usuario['id'],
          'nombre' => $usuario['nombre'],
          'username' => $usuario['username']
        ]
      ]);
    } else {
      echo json_encode(['success' => false, 'mensaje' => 'Credenciales inválidas']);
    }
  }
}
