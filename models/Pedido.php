<?php
require_once __DIR__ . '/../config/database.php';

class Pedido {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function crearPedido($data) {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO pedido (cliente_id, mesa_id, mozo_id, estado, total, metodo_pago, tipo_entrega)
                      VALUES (:clienteId, :mesaId, :mozoId, :estado, :total, :metodoPago, :tipoEntrega)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(':clienteId', $data['clienteId']);
            $stmt->bindValue(':mesaId', $data['mesaId'], $data['mesaId'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':mozoId', $data['mozoId'], $data['mozoId'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':estado', $data['estado']);
            $stmt->bindValue(':total', $data['total']);
            $stmt->bindValue(':metodoPago', $data['metodoPago']);
            $stmt->bindValue(':tipoEntrega', $data['tipoEntrega']);
            $stmt->execute();

            $pedidoId = $this->conn->lastInsertId();

            $stmtDetalle = $this->conn->prepare("
                INSERT INTO pedido_plato (pedido_id, plato_id, cantidad)
                VALUES (:pedidoId, :platoId, :cantidad)
            ");

            foreach ($data['platos'] as $plato) {
                $stmtDetalle->bindValue(':pedidoId', $pedidoId);
                $stmtDetalle->bindValue(':platoId', $plato['platoId']);
                $stmtDetalle->bindValue(':cantidad', $plato['cantidad']);
                $stmtDetalle->execute();
            }

            $this->conn->commit();
            return [
                "mensaje" => "Pedido registrado correctamente",
                "pedidoId" => $pedidoId
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            file_put_contents('log_error_pedido.txt', $e->getMessage());
            return [
                "error" => "Error al registrar el pedido: " . $e->getMessage()
            ];
        }
    }

    public function obtenerPedidoConDetalles($id) {
        $pdo = $this->conn;

        // Obtener el pedido principal
        $stmt = $pdo->prepare("
            SELECT p.*, 
                   u.nombre AS nombre_cliente,
                   e.nombre AS nombre_mozo,
                   m.numero AS numero_mesa
            FROM pedido p
            LEFT JOIN usuarios u ON p.cliente_id = u.id
            LEFT JOIN empleado e ON p.mozo_id = e.id
            LEFT JOIN mesa m ON p.mesa_id = m.id
            WHERE p.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener detalles de platos
        $stmt = $pdo->prepare("
            SELECT dp.*, pl.nombre, pl.precio
            FROM pedido_plato dp
            JOIN platos pl ON dp.plato_id = pl.id
            WHERE dp.pedido_id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'pedido' => $pedido,
            'detalles' => $detalles
        ];
    }

    public function obtenerPedidosPorCliente($clienteId) {
        $stmt = $this->conn->prepare("
            SELECT p.id, p.estado, p.total, p.metodo_pago, p.tipo_entrega, p.fecha_creacion
            FROM pedido p
            WHERE p.cliente_id = :clienteId
            ORDER BY p.fecha_creacion DESC
        ");
        $stmt->bindParam(':clienteId', $clienteId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosLosPedidos() {
        $stmt = $this->conn->prepare("
            SELECT p.id, p.estado, p.total, p.metodo_pago, p.tipo_entrega, p.fecha_creacion
            FROM pedido p
            ORDER BY p.fecha_creacion DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
