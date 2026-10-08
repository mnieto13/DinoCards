<?php
session_start();
$_SESSION = [];
session_destroy();
header("Content-Type: application/json; charset=UTF-8");
echo json_encode(["exito" => true, "mensaje" => "Sesión cerrada correctamente."]);