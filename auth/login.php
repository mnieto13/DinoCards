<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../services/UsuarioService.php";

$datos = json_decode(file_get_contents("php://input"), true);
$id    = $datos["identificador"] ?? "";
$pass  = $datos["password"] ?? "";

$servicio = new UsuarioService();
$resultado = $servicio->iniciarSesion($id, $pass);

echo json_encode($resultado);