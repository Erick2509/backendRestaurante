<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../config/database.php';

class PedidoController {
    public function crear($data) {
        file_put_contents("log_pedido.json", json_encode($data, JSON_PRETTY_PRINT));

        $pedido = new Pedido();
        $resultado = $pedido->crearPedido($data);
        header('Content-Type: application/json');

        if (isset($resultado['error'])) {
            http_response_code(500);
        }

        echo json_encode($resultado);
    }

    public function obtenerPorId($id) {
        $pedido = new Pedido();
        $resultado = $pedido->obtenerPedidoConDetalles($id);
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    public function obtenerPedidosPorCliente($clienteId) {
        $pedido = new Pedido();
        $resultado = $pedido->obtenerPedidosPorCliente($clienteId);
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    public function actualizarEstado($id, $estado) {
        header('Content-Type: application/json');

        try {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("UPDATE pedido SET estado = :estado WHERE id = :id");
            $stmt->bindParam(':estado', $estado);
            $stmt->bindValue(':id', intval($id), PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["mensaje" => "Estado actualizado correctamente"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar estado: " . $e->getMessage()]);
        }
    }
}
