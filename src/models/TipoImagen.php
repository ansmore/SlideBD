<?php

class TipoImagen extends Diapositiva
{
    private string $titulo;
    private string $contenido;
    private string $nombre_imagen;

    public function __construct(int|null $id_diapositiva, string $titulo, string $contenido, string $nombre_imagen)
    {
        parent::__construct($id_diapositiva);
        $this->titulo = $titulo;
        $this->contenido = $contenido;
        $this->nombre_imagen = $nombre_imagen;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getContenido(): string
    {
        return $this->contenido;
    }

    public function getNombre_imagen(): string
    {
        return $this->nombre_imagen;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function setContenido(string $contenido): void
    {
        $this->contenido = $contenido;
    }

    public function setNombre_imagen(string $nombre_imagen): void
    {
        $this->nombre_imagen = $nombre_imagen;
    }

    public function nuevaDiapositiva(PDO $conn, int $id_presentacion): void
    {
        $stmt = $conn->prepare("INSERT INTO diapositiva(presentacion_id, orden) VALUES (?, ?)");
        $stmt->bindParam(1, $id_presentacion);
        $stmt->bindParam(2, $this->orden);
        $stmt->execute();

        $id_diapositiva = $conn->lastInsertId();
        $this->setId($id_diapositiva);

        $stmt = $conn->prepare("INSERT INTO tipoImagen(diapositiva_id, presentacion_id, titulo, contenido, nombre_imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $id_diapositiva);
        $stmt->bindParam(2, $id_presentacion);
        $stmt->bindParam(3, $this->titulo);
        $stmt->bindParam(4, $this->contenido);
        $stmt->bindParam(5, $this->nombre_imagen);
        $stmt->execute();

    }

    public function actualizarDiapositiva(PDO $conn, int $id_presentacion): void
    {
        $id_diapositiva = $this->getId();

        $stmt = $conn->prepare("SELECT titulo, contenido, nombre_imagen FROM tipoImagen WHERE presentacion_id = ? AND diapositiva_id = ?");
        $stmt->bindParam(1, $id_presentacion);
        $stmt->bindParam(2, $id_diapositiva);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['titulo'] !== $this->getTitulo()) {
            $newTitulo = $this->getTitulo();

            $stmt = $conn->prepare("UPDATE tipoImagen SET titulo = ? WHERE presentacion_id = ? AND diapositiva_id = ?");

            $stmt->bindParam(1, $newTitulo);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->bindParam(3, $id_diapositiva);
            $stmt->execute();
        }

        if ($row['contenido'] !== $this->getContenido()) {
            $newContenido = $this->getContenido();

            $stmt = $conn->prepare("UPDATE tipoImagen SET contenido = ? WHERE presentacion_id = ? AND diapositiva_id = ?");

            $stmt->bindParam(1, $newContenido);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->bindParam(3, $id_diapositiva);
            $stmt->execute();
        }

        if ($row['nombre_imagen'] !== $this->getNombre_imagen()) {
            $newNombreImagen = $this->getNombre_imagen();

            $stmt = $conn->prepare("UPDATE tipoImagen SET nombre_imagen = ? WHERE presentacion_id = ? AND diapositiva_id = ?");

            $stmt->bindParam(1, $newNombreImagen);
            $stmt->bindParam(2, $id_presentacion);
            $stmt->bindParam(3, $id_diapositiva);
            $stmt->execute();
        }
    }

    public function getDiapositivaHTML(): string
    {
        $nombreImagen = '../imagenes/' . $this->getNombre_imagen();

        $imagenHtml = ''; // Inicializamos la variable que contendrá el código HTML de la imagen

        // Verificamos si el nombre de la imagen es nulo o vacío
        if ($this->getNombre_imagen() !== 'null' && $this->getNombre_imagen() !== '') {
            // Si la imagen no es nula, mostramos la imagen
            $imagenHtml = '
            <div class="imagenDiv">
                <img src="' . $nombreImagen . '" alt="Imagen" class="imgMostrada" />
            </div>';
        } else {
            $imagenHtml = '<div class="imagenDiv" id="mensajeNoImg">No hay imagen disponible</div>';
        }

        return '
        <div class="d-containerImagen" data-id="' . $this->getId() . '">
        <img src="../assets/icons/eliminar.svg" alt="Eliminar Diapositiva" id="imgEliminar" onclick="confirmDelete(event, this.closest(\'.d-containerImagen\'))">
        <input class="focus" type="text" form="data_p" value="' . $this->getTitulo() . '" autocomplete="off"
            name="d_titulo_' . $this->getId() . '" placeholder="Haz click para añadir un título..." />
            <div class="d-containerImgText">
                <textarea class="focus" form="data_p" autocomplete="off"
                name="d_contenido_' . $this->getId() . '" placeholder="Haz click para añadir un texto">' . $this->getContenido() . '</textarea>
                <div class="imagenDiv">
                    ' . $imagenHtml . '
                    <input class="imagen" type="file" form="data_p" name="d_imagen_' . $this->getId() . '"
                                accept="image/jpeg, image/png, image/jpg" />
                </div>
            </div>
        </div>';
    }

    public function getDiapositivaPreview(): string
    {
        $nombreImagen = '../imagenes/' . $this->getNombre_imagen();

        return '
        <div class="d-container" style="display: none;">
            <h1 class="d_titulo_' . $this->getId() . '">' . $this->getTitulo() . '</h1>
            <div class="d-containerContenidoImagen">
                <p class="d_contenido_' . $this->getId() . '">' . $this->getContenido() . '</p>
                <div class="imagen">
                    <img class="d_imagen_' . $this->getId() . '" src="' . $nombreImagen . '" alt="Imagen de la diapositiva" id="imagenPreview">
                </div>
            </div>
        </div>';
    }

    public function getMiniatura(): string
    {
        $nombreImagen = '../imagenes/' . $this->getNombre_imagen();

        return '
        <div class="d-miniatura" id_diapositiva="'. $this->getId() .'">
            <h1>' . $this->getTitulo() . '</h1>
            <div class="d-containerContenidoImagen">
                <p>' . $this->getContenido() . '</p>
                <div class="imagen">
                    <img id="imagenPreview" src="' . $nombreImagen . '" alt="Imagen de la diapositiva">
                </div>
            </div>
        </div>';
    }
}

