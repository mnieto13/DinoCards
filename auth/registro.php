<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../services/UsuarioService.php";

$datos = json_decode(file_get_contents("php://input"), true);
$user  = $datos["username"] ?? "";
$email = $datos["email"] ?? "";
$pass  = $datos["password"] ?? "";
$conf  = $datos["confirmPassword"] ?? $pass;

$servicio = new UsuarioService();
$resultado = $servicio->registrar($user, $email, $pass, $conf);

echo json_encode($resultado);