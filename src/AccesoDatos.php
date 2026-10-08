<?php
class AccesoDatos {
    private static $instancia = null;
    private $conexion = null;

    private function __construct() {
        $host = "localhost";
        $db   = "DinoCards";
        $user = "miguelDB";
        $pass = "VictorGoldito";

        $this->conexion = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public static function obtenerInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new AccesoDatos();
        }
        return self::$instancia;
    }

    // 1. Registro mediante Procedimiento Almacenado
    public function registrarUsuario($username, $email, $passwordHash) {
        $stmt = $this->conexion->prepare("CALL Registro(?, ?, ?, @_res)");
        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, $passwordHash);
        $stmt->execute();
        $stmt->closeCursor(); // Libera el buffer para la siguiente consulta

        $res = $this->conexion->query("SELECT @_res AS res")->fetch();
        return (int)$res['res'];
    }

    // 2. Consulta de usuario para Login (Soporta usuario o email)
    public function obtenerUsuarioPorIdentificador($identificador) {
        $stmt = $this->conexion->prepare("SELECT id, username, email, password_hash, ultima_obtencion FROM usuarios WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$identificador, $identificador]);
        return $stmt->fetch();
    }

    // 3. Consulta de fecha para control diario
    public function obtenerUltimaObtencion($usuarioId) {
        $stmt = $this->conexion->prepare("SELECT ultima_obtencion FROM usuarios WHERE id = ?");
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch();
        return $row ? $row['ultima_obtencion'] : null;
    }

    // 4. Apertura y guardado utilizando el Procedimiento Almacenado AnadirNuevaCartaAColeccion
    public function generarYGuardarSobre($usuarioId, $esDiario) {
        $cartas = [];
        
        $limite = $this->conexion->query("SELECT COUNT(*) as total FROM dinosaurios")->fetch()['total'];
        $maxDinos = ($limite > 0) ? (int)$limite : 6;

        $stmtCall = $this->conexion->prepare("CALL AnadirNuevaCartaAColeccion(?, ?, ?, @_res)");
        $stmtDino = $this->conexion->prepare("SELECT * FROM dinosaurios WHERE id = ?");

        for ($i = 0; $i < 5; $i++) {
            $dinoId = rand(1, $maxDinos);
            
            // Solo marcamos como diario en la última iteración para registrar la fecha una sola vez
            $marcarDiario = ($esDiario && $i === 4) ? 1 : 0;
            
            $stmtCall->execute([$usuarioId, $dinoId, $marcarDiario]);
            $stmtCall->closeCursor();

            $stmtDino->execute([$dinoId]);
            $cartas[] = $stmtDino->fetch();
            $stmtDino->closeCursor();
        }

        return $cartas;
    }

    // 5. Consulta de la colección mediante Procedimiento Almacenado
    public function obtenerColeccionUsuario($usuarioId) {
        $stmt = $this->conexion->prepare("CALL ObtenerColeccion(?, @_res)");
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->fetchAll();
        $stmt->closeCursor();
        return $resultado;
    }
}