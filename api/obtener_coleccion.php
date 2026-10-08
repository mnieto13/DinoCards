<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../services/ColeccionService.php";

$usuarioId = $_SESSION['usuario_id'] ?? null;
$servicio = new ColeccionService();
$resultado = $servicio->obtenerColeccion($usuarioId);

echo json_encode($resultado);