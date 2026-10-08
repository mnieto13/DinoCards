<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../services/ColeccionService.php";

$usuarioId = $_SESSION['usuario_id'] ?? null;
if (!$usuarioId) {
    echo json_encode(["exito" => false, "mensaje" => "No has iniciado sesión."]);
    exit;
}

$datos = json_decode(file_get_contents("php://input"), true);
$tipo  = $datos["tipo"] ?? "infinito";

$servicio = new ColeccionService();
$resultado = $servicio->abrirSobre($usuarioId, $tipo);

echo json_encode($resultado);