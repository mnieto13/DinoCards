<?php
require_once __DIR__ . "/../src/AccesoDatos.php";

class ColeccionService {
    private $db;

    public function __construct() {
        $this->db = AccesoDatos::obtenerInstancia();
    }

    public function abrirSobre($usuarioId, $tipo) {
        $esDiario = ($tipo === "diario");

        // Control de regla de negocio: ¿Tiene derecho al sobre diario hoy?
        if ($esDiario) {
            $ultimaFecha = $this->db->obtenerUltimaObtencion($usuarioId);
            if (!empty($ultimaFecha)) {
                $hoy = date("Y-m-d");
                $diaUltimo = substr($ultimaFecha, 0, 10);
                if ($diaUltimo === $hoy) {
                    return [
                        "exito" => false,
                        "mensaje" => "Ya has reclamado tu sobre diario hoy. Vuelve mañana."
                    ];
                }
            }
        }

        // Obtener 5 especímenes aleatorios y registrarlos en la BD
        $cartas = $this->db->generarYGuardarSobre($usuarioId, $esDiario);

        return [
            "exito" => true,
            "cartas" => $cartas
        ];
    }

    public function obtenerColeccion($usuarioId) {
        if (!$usuarioId) {
            return ["exito" => false, "coleccion" => []];
        }

        $coleccion = $this->db->obtenerColeccionUsuario($usuarioId);

        return [
            "exito" => true,
            "coleccion" => $coleccion
        ];
    }
}