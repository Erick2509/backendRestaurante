<?php
require_once __DIR__ . '/../controllers/PlatoController.php';
require_once __DIR__ . '/../controllers/PedidoController.php';
require_once __DIR__ . '/../controllers/EmpleadoController.php';
require_once __DIR__ . '/../controllers/MesaController.php';
require_once __DIR__ . '/../controllers/LoginController.php';

$uri = str_replace('\\', '/', $_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

// 🔹 Login
if ($method === 'POST' && preg_match('#/backendRestaurante(/index\.php)?/api/login$#', $uri)) {
    (new LoginController())->login();
    exit;
}

// 🔹 Platos
if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/platos/?$#', $uri)) {
    (new PlatoController())->index();
    exit;
}

if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/platos/(\d+)$#', $uri, $matches)) {
    (new PlatoController())->show($matches[2]);
    exit;
}

if ($method === 'POST' && preg_match('#/backendRestaurante(/index\.php)?/api/platos/?$#', $uri)) {
    (new PlatoController())->crear();
    exit;
}

if ($method === 'PUT' && preg_match('#/backendRestaurante(/index\.php)?/api/platos/(\d+)$#', $uri, $matches)) {
    (new PlatoController())->actualizar($matches[2]);
    exit;
}

if ($method === 'DELETE' && preg_match('#/backendRestaurante(/index\.php)?/api/platos/(\d+)$#', $uri, $matches)) {
    (new PlatoController())->eliminar($matches[2]);
    exit;
}

if ($method === 'POST' && preg_match('#/backendRestaurante(/index\.php)?/api/platos/upload$#', $uri)) {
    (new PlatoController())->subirImagen();
    exit;
}

// 🔹 Pedidos
if ($method === 'POST' && preg_match('#/backendRestaurante(/index\.php)?/api/pedidos/?$#', $uri)) {
    $data = json_decode(file_get_contents("php://input"), true);
    (new PedidoController())->crear($data);
    exit;
}

if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/pedidos/(\d+)$#', $uri, $matches)) {
    (new PedidoController())->obtenerPorId($matches[2]);
    exit;
}

if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/pedidos/cliente/([^/]+)$#', $uri, $matches)) {
    (new PedidoController())->obtenerPedidosPorCliente($matches[2]);
    exit;
}

// 🔹 Empleados y Mesas
if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/mozos/disponibles$#', $uri)) {
    (new EmpleadoController())->obtenerMozosDisponibles();
    exit;
}

if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/mesas/disponibles$#', $uri)) {
    (new MesaController())->obtenerMesasDisponibles();
    exit;
}
if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/empleados/?$#', $uri)) {
    (new EmpleadoController())->obtenerTodos();
    exit;
}

if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/empleados/(\d+)$#', $uri, $matches)) {
    (new EmpleadoController())->obtenerPorId($matches[2]);
    exit;
}

if ($method === 'POST' && preg_match('#/backendRestaurante(/index\.php)?/api/empleados/?$#', $uri)) {
    (new EmpleadoController())->crear();
    exit;
}

if ($method === 'PUT' && preg_match('#/backendRestaurante(/index\.php)?/api/empleados/(\d+)$#', $uri, $matches)) {
    (new EmpleadoController())->actualizar($matches[2]);
    exit;
}

if ($method === 'DELETE' && preg_match('#/backendRestaurante(/index\.php)?/api/empleados/(\d+)$#', $uri, $matches)) {
    (new EmpleadoController())->eliminar($matches[2]);
    exit;
}

// 🔹 Rutas para Mesas
if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/mesas/?$#', $uri)) {
    (new MesaController())->obtenerTodas();
    exit;
}

if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/mesas/(\d+)$#', $uri, $matches)) {
    (new MesaController())->obtenerPorId($matches[2]);
    exit;
}

if ($method === 'POST' && preg_match('#/backendRestaurante(/index\.php)?/api/mesas/?$#', $uri)) {
    (new MesaController())->crear();
    exit;
}

if ($method === 'PUT' && preg_match('#/backendRestaurante(/index\.php)?/api/mesas/(\d+)$#', $uri, $matches)) {
    (new MesaController())->actualizar($matches[2]);
    exit;
}

if ($method === 'DELETE' && preg_match('#/backendRestaurante(/index\.php)?/api/mesas/(\d+)$#', $uri, $matches)) {
    (new MesaController())->eliminar($matches[2]);
    exit;
}
if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/pedidos$#', $uri)) {
    echo json_encode((new Pedido())->obtenerTodosLosPedidos());
    exit;
}
if ($method === 'PUT' && preg_match('#/backendRestaurante(/index\.php)?/api/pedidos/(\d+)/estado$#', $uri, $matches)) {
    $data = json_decode(file_get_contents("php://input"), true);
    (new PedidoController())->actualizarEstado($matches[2], $data['estado']);
    exit;
}
if ($method === 'GET' && preg_match('#/backendRestaurante(/index\.php)?/api/pedidos/(\d+)/detalle$#', $uri, $matches)) {
    (new PedidoController())->obtenerPorId($matches[2]);
    exit;
}




// 🔴 Ruta no encontrada (SIEMPRE AL FINAL)
http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada", "ruta" => $uri]);
exit;
