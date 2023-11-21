<?php

class Presentacion
{
    private int|null $id;
    private string $titulo;
    private string $descripcion;
    private string $tema;
    private string $url;
    private string $pin;
    private array $diapositivas;

    public function __construct(int|null $id, string $titulo, string $descripcion, string $tema, string $url, string $pin, array $diapositivas)
    {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->tema = $tema;
        $this->url = $url;
        $this->pin = $pin;
        $this->diapositivas = $diapositivas;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getTema(): string
    {
        return $this->tema;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPin(): string
    {
        return $this->pin;
    }

    public function getDiapositivas(): array
    {
        return $this->diapositivas;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function setTema(string $tema): void
    {
        $this->tema = $tema;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public static function setPin(PDO $conn, int $id_presentacion, string $pin): void
    {
        $stmt = $conn->prepare("UPDATE presentacion SET pin = ? WHERE id = ?");
        $stmt->bindParam(1, $pin);
        $stmt->bindParam(2, $id_presentacion);
        $stmt->execute();
    }

    public static function checkPin(PDO $conn, int $id_presentacion, string $pin): bool
    {
        $stmt = $conn->prepare("SELECT pin FROM presentacion WHERE id = ?");
        $stmt->bindParam(1, $id_presentacion);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row['pin'] === $pin);
    }

    public function setDiapositivas(array $diapositivas): void
    {
        $this->diapositivas = $diapositivas;
    }

    /**
     * Funcion que actualiza la informacion de la presentacion.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @return void
     */
    public function actualizarInfo(PDO $conn): void
    {
        $id_presentacion = $this->getId();

        $stmt = $conn->prepare("SELECT titulo, descripcion, tema, url FROM presentacion WHERE id = ?");
        $stmt->bindParam(1, $id_presentacion);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($this->getTitulo() !== $row['titulo']) {
            $newTitulo = $this->getTitulo();

            $stmt = $conn->prepare("UPDATE presentacion SET titulo = ? WHERE id = ?");
            $stmt->bindParam(1, $newTitulo);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->execute();
        }

        if ($this->getDescripcion() !== $row['descripcion']) {
            $newDescripcion = $this->getDescripcion();

            $stmt = $conn->prepare("UPDATE presentacion SET descripcion = ? WHERE id = ?");
            $stmt->bindParam(1, $newDescripcion);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->execute();
        }

        if ($this->getTema() !== $row['tema']) {
            $newTema = $this->getTema();

            $stmt = $conn->prepare("UPDATE presentacion SET tema = ? WHERE id = ?");
            $stmt->bindParam(1, $newTema);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->execute();
        }

        if ($this->getUrl() !== $row['url']) {
            $newUrl = $this->getUrl();

            $stmt = $conn->prepare("UPDATE presentacion SET url = ? WHERE id = ?");
            $stmt->bindParam(1, $newUrl);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->execute();
        }
    }

    /**
     * Función que publica u oculta la presentación.
     */
    public function publica()
    {
        if ($this->url === 'null') {
            $this->url = strval(random_int(1000000000, 9999999999));
        } else {
            $this->url = 'null';
        }
    }

    /**
     * Funcion que obtiene el último ID de las diapositivas de la presentación.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @return int
     */
    public function getLastDiapositivaId($conn): int
    {
        $id_presentacion = $this->getId();
        $stmt = $conn->prepare("SELECT id FROM diapositiva WHERE presentacion_id = ? ORDER BY id DESC");
        $stmt->bindParam(1, $id_presentacion);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        return $result['id'];
    }

    /**
     * Funcion que crea una nueva presentacion en la BD.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @return void
     */
    public function guardarNuevaPresentacion(PDO $conn): void
    {
        $stmt = $conn->prepare("INSERT INTO presentacion(titulo, descripcion, tema, url, pin) VALUES (?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $this->titulo);
        $stmt->bindParam(2, $this->descripcion);
        $stmt->bindParam(3, $this->tema);
        $stmt->bindParam(4, $this->url);
        $stmt->bindParam(5, $this->pin);
        $stmt->execute();

        $this->id = $conn->lastInsertId();

        foreach ($this->diapositivas as $diapositiva) {
            $diapositiva->nuevaDiapositiva($conn, $this->id);
        }
    }

    /**
     * Funcion que obtiene el array de diapositivas de una presentacion.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion ID de la presentacion que queremos consultar.
     * @return array
     */
    private static function getDiapositivasBD(PDO $conn, int $id_presentacion): array
    {
        $stmt = $conn->prepare(
            "SELECT dt.id as diapositiva_id, 
            COALESCE(tt.titulo, tc.titulo, ti.titulo, tp.titulo) AS titulo, 
            COALESCE(tc.contenido, ti.contenido) AS contenido,
            ti.nombre_imagen,
            tp.pregunta, tp.respuesta_a, tp.respuesta_b, tp.respuesta_c, tp.respuesta_d, tp.respuesta_correcta 
        FROM presentacion p 
            LEFT JOIN diapositiva dt ON p.id = dt.presentacion_id
            LEFT JOIN tipoTitulo tt ON dt.id = tt.diapositiva_id AND dt.presentacion_id = tt.presentacion_id
            LEFT JOIN tipoContenido tc ON dt.id = tc.diapositiva_id AND dt.presentacion_id = tc.presentacion_id
            LEFT JOIN tipoImagen ti ON dt.id = ti.diapositiva_id AND dt.presentacion_id = ti.presentacion_id
            LEFT JOIN tipoPregunta tp ON dt.id = tp.diapositiva_id AND dt.presentacion_id = tp.presentacion_id
        WHERE p.id = ?
        ORDER BY dt.orden ASC;"
        );
        $stmt->bindParam(1, $id_presentacion);
        $stmt->execute();

        $diapositivas = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['nombre_imagen'] === null) {

                if ($row['pregunta'] !== null) {
                    array_push($diapositivas, new TipoPregunta($row['diapositiva_id'], $row['titulo'], $row['pregunta'], $row['respuesta_a'], $row['respuesta_b'], $row['respuesta_c'], $row['respuesta_d'], $row['respuesta_correcta']));
                } else if ($row['contenido'] === null) {
                    array_push($diapositivas, new TipoTitulo($row['diapositiva_id'], $row['titulo']));
                } else {
                    array_push($diapositivas, new TipoContenido($row['diapositiva_id'], $row['titulo'], $row['contenido']));
                }
            } else {
                array_push($diapositivas, new TipoImagen($row['diapositiva_id'], $row['titulo'], $row['contenido'], $row['nombre_imagen']));
            }
        }


        return $diapositivas;
    }

    /**
     * Funcion que retorna una presentacion instanciada de la BD.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion ID de la presentacion que queremos instanciar.
     * @return Presentacion
     */
    public static function getPresentacionBD(PDO $conn, int $id_presentacion): Presentacion|null
    {
        $stmt = $conn->prepare("SELECT id, titulo, descripcion, tema, url, pin FROM presentacion WHERE id = ?");
        $stmt->bindParam(1, $id_presentacion);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($row['id'])) {
            $presentacion = new Presentacion($row['id'], $row['titulo'], $row['descripcion'], $row['tema'], $row['url'], $row['pin'], []);

            $diapositivas = Presentacion::getDiapositivasBD($conn, $id_presentacion);
            $presentacion->setDiapositivas($diapositivas);
            return $presentacion;
        } else {
            return null;
        }
    }

    /**
     * Funcion que retorna una presentacion instanciada de la BD en base a la URL publica.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion ID de la presentacion que queremos instanciar.
     * @return Presentacion
     */
    public static function getPresentacionByURL(PDO $conn, int $url): Presentacion|null
    {
        $stmt = $conn->prepare("SELECT id FROM presentacion WHERE url = ?");
        $stmt->bindParam(1, $url);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($row["id"])) {
            return self::getPresentacionBD($conn, $row["id"]);
        } else {
            return null;
        }

    }

    /**
     * Funcion que elimina una presentacion de la BD.
     * @param PDO $conn Objeto PDO de la conexion a la base de datos.
     * @param int $id_presentacion ID de la presentacion a eliminar.
     * @return string
     */
    public static function eliminarPresentacionBD(PDO $conn, int $id): string
    {
        try {
            $conn->beginTransaction();

            // Finalmente, elimina la presentación de la tabla Presentacions
            $query = "DELETE FROM presentacion WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();

            $conn->commit();
            return 'La presentación se ha eliminado correctamente.';
        } catch (PDOException $e) {
            $conn->rollBack();
            return 'Error al eliminar la presentación.';
        }
    }
}
