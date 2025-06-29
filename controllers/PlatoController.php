<?php
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../config/database.php';

class PlatoController {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    // 🔹 Listar todos los platos
    public function index() {
        $plato = new Plato();
        header('Content-Type: application/json');
        echo json_encode($plato->listar());
    }

    // 🔹 Obtener plato por ID
    public function show($id) {
        $plato = new Plato();
        header('Content-Type: application/json');
        echo json_encode($plato->obtenerPorId($id));
    }

    // 🔹 Subir imagen
    public function subirImagen() {
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $nombre = uniqid() . '_' . basename($file['name']);
            $rutaDestino = __DIR__ . '/../uploads/' . $nombre;
    
            if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                // Genera la URL completa
                $url = 'http://' . $_SERVER['HTTP_HOST'] . '/backendRestaurante/uploads/' . $nombre;
                echo $url;
            } else {
                http_response_code(500);
                echo "Error al mover archivo";
            }
        } else {
            http_response_code(400);
            echo "No se recibió ningún archivo";
        }
    }
    

    // 🔹 Crear nuevo plato
    public function crear() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];
        $precio = $data['precio'];
        $categoria = $data['categoria'];
        $imagenUrl = $data['imagenUrl'];

        $stmt = $this->conn->prepare(
            "INSERT INTO platos (nombre, descripcion, precio, categoria, imagen_url)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $imagenUrl]);

        echo json_encode(['success' => true]);
    }

    // 🔹 Actualizar plato
    public function actualizar($id) {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];
        $precio = $data['precio'];
        $categoria = $data['categoria'];
        $imagenUrl = $data['imagenUrl'];

        $stmt = $this->conn->prepare(
            "UPDATE platos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, imagen_url = ? WHERE id = ?"
        );
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $imagenUrl, $id]);

        echo json_encode(['success' => true]);
    }

    // 🔹 Eliminar plato
    public function eliminar($id) {
        header('Content-Type: application/json');

        $stmt = $this->conn->prepare("DELETE FROM platos WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
    }
}
