<?php

abstract class Diapositiva extends Presentacion
{
    private int|null $id;

    public function __construct(int|null $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    protected ?int $orden = null;

    public function getOrden(): ?int {
        return $this->orden;
    }

    public function setOrden(?int $orden): void {
        $this->orden = $orden;
    }

    /**
     * Funcion que añade a la BD la diapositiva instanciada.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion Id de la presentacion a la que pertenece la diapositiva.
     * @return void
     */
    abstract public function nuevaDiapositiva(PDO $conn, int $id_presentacion): void;

    /**
     * Funcion que actualiza en la BD la informacion diapositiva instanciada.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion Id de la presentacion a la que pertenece la diapositiva.
     * @return void
     */
    abstract public function actualizarDiapositiva(PDO $conn, int $id_presentacion): void;

    /**
     * Funcion que genera la vista HTML de la diapositiva instanciada.
     * @return string
     */
    abstract public function getDiapositivaHTML(): string;

    /**
     * Funcion que elimina en la BD una diapositiva instanciada.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion Id de la presentacion a la que pertenece la diapositiva.
     * @return string Mensaje resultado de la operacion.
     */
    public function eliminarDiapositiva(PDO $conn, int $id_presentacion): string
    {
        $id_diapositiva = $this->id;

        try {
            $conn->beginTransaction();

            $query = "DELETE FROM diapositiva WHERE id = ? AND presentacion_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(1, $id_diapositiva);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->execute();

            $conn->commit();
            return 'La diapositiva se ha eliminado correctamente.';
        } catch (PDOException $e) {
            $conn->rollBack();
            return 'Error al eliminar la diapositiva.';
        }
    }

    public static function actualizarOrdenDiapositiva(PDO $conn, $idDiapositiva, $nuevoOrden) {
        $stmt = $conn->prepare("UPDATE diapositiva SET orden = ? WHERE id = ?");
        $stmt->bindParam(1, $nuevoOrden);
        $stmt->bindParam(2, $idDiapositiva);
        $stmt->execute();
        return $stmt;
    }

    public static function getDiapositivaPorId(PDO $conn, $idDiapositiva): ?Diapositiva {
        // Intentar obtener detalles de TipoPregunta primero
        $stmt = $conn->prepare("
            SELECT d.id, d.orden, tp.titulo, tp.pregunta, tp.respuesta_a, tp.respuesta_b, tp.respuesta_c, tp.respuesta_d, tp.respuesta_correcta
            FROM diapositiva d
            LEFT JOIN tipoPregunta tp ON d.id = tp.diapositiva_id
            WHERE d.id = ?
        ");
        $stmt->bindParam(1, $idDiapositiva);
        $stmt->execute();
    
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos && $datos['pregunta'] !== null) {
            // Es una instancia de TipoPregunta
            return new TipoPregunta(
                $datos['id'],
                $datos['titulo'],
                $datos['pregunta'],
                $datos['respuesta_a'],
                $datos['respuesta_b'],
                $datos['respuesta_c'],
                $datos['respuesta_d'],
                $datos['respuesta_correcta']
            );
        } else {
            // Si no es TipoPregunta, intentar obtener otros tipos aquí (TipoContenido, TipoTitulo, TipoImagen, etc.)
            // Intentar obtener detalles de TipoContenido
            $stmt = $conn->prepare("
                SELECT d.id, d.orden, tc.titulo, tc.contenido
                FROM diapositiva d
                LEFT JOIN tipoContenido tc ON d.id = tc.diapositiva_id
                WHERE d.id = ?
            ");
            $stmt->bindParam(1, $idDiapositiva);
            $stmt->execute();
    
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($datos && $datos['contenido'] !== null) {
                // Es una instancia de TipoContenido
                return new TipoContenido($datos['id'], $datos['titulo'], $datos['contenido']);
            } else {
                // Si no es TipoContenido, intentar obtener TipoTitulo
                $stmt = $conn->prepare("
                    SELECT d.id, d.orden, tt.titulo
                    FROM diapositiva d
                    LEFT JOIN tipoTitulo tt ON d.id = tt.diapositiva_id
                    WHERE d.id = ?
                ");
                $stmt->bindParam(1, $idDiapositiva);
                $stmt->execute();
    
                $datos = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($datos && $datos['titulo'] !== null) {
                    // Es una instancia de TipoTitulo
                    return new TipoTitulo($datos['id'], $datos['titulo']);
                } else {
                    // Si no es TipoTitulo, intentar obtener TipoImagen
                    $stmt = $conn->prepare("
                        SELECT d.id, d.orden, ti.titulo, ti.contenido, ti.nombre_imagen
                        FROM diapositiva d
                        LEFT JOIN tipoImagen ti ON d.id = ti.diapositiva_id
                        WHERE d.id = ?
                    ");
                    $stmt->bindParam(1, $idDiapositiva);
                    $stmt->execute();
    
                    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($datos && $datos['nombre_imagen'] !== null) {
                        // Es una instancia de TipoImagen
                        return new TipoImagen($datos['id'], $datos['titulo'], $datos['contenido'], $datos['nombre_imagen']);
                    }
                }
            }
        }
        
        return null; // No se encontró la diapositiva o no tiene tipo definido
    }
    
    public static function obtenerIdsDiapositivasPorPresentacion(PDO $conn, int $id_presentacion): array
    {
        $ids = [];
        
        $query = "SELECT id FROM diapositiva WHERE presentacion_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id_presentacion);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ids[] = $row['id'];
        }
        
        return $ids;
    }

}
