<?php
require_once __DIR__ . "/../src/AccesoDatos.php";

class UsuarioService {
    private $db;

    public function __construct() {
        $this->db = AccesoDatos::obtenerInstancia();
    }

    public function registrar($username, $email, $password, $confirmPassword) {
        $username = trim($username);
        $email    = trim($email);
        $password = trim($password);

        // Validación estricta de lógica de negocio
        if (empty($username) || empty($email) || empty($password)) {
            return ["exito" => false, "mensaje" => "Todos los campos son obligatorios."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["exito" => false, "mensaje" => "El formato del correo electrónico no es válido."];
        }

        if ($password !== $confirmPassword) {
            return ["exito" => false, "mensaje" => "Las contraseñas no coinciden."];
        }

        // Hasheo seguro exigido por la rúbrica y el PDF
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Llamada a la capa de acceso a datos que ejecuta el Procedimiento Almacenado
        $codigoResultado = $this->db->registrarUsuario($username, $email, $hash);

        if ($codigoResultado === 0) {
            return ["exito" => true, "mensaje" => "¡Genetista registrado con éxito!"];
        } elseif ($codigoResultado === -2) {
            return ["exito" => false, "mensaje" => "El usuario o email ya está registrado."];
        } else {
            return ["exito" => false, "mensaje" => "Error interno al procesar el registro."];
        }
    }

    public function iniciarSesion($identificador, $password) {
        $identificador = trim($identificador);
        $password      = trim($password);

        if (empty($identificador) || empty($password)) {
            return ["exito" => false, "mensaje" => "Introduce usuario/email y contraseña."];
        }

        // Obtener usuario desde AccesoDatos (bonus: por usuario o email)
        $usuario = $this->db->obtenerUsuarioPorIdentificador($identificador);

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            return ["exito" => false, "mensaje" => "Credenciales incorrectas."];
        }

        // Gestión del superobjeto $_SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['username']   = $usuario['username'];

        unset($usuario['password_hash']);

        return [
            "exito" => true,
            "mensaje" => "Acceso autorizado.",
            "usuario" => $usuario
        ];
    }
}