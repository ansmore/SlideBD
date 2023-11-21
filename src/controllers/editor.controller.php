<?php
session_start();

define('VALID_ENTRY_POINT', true);
include '../../config.php';

require_once '../models/db.php';
require_once '../models/Presentacion.php';
require_once '../models/Diapositiva.php';
require_once '../models/TipoTitulo.php';
require_once '../models/TipoContenido.php';
require_once '../models/TipoImagen.php';
require_once '../models/TipoPregunta.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$id_presentacion;
$editPin = '';

// Función para verificar y crear la carpeta de imágenes
function createImagesFolder()
{
    $imagesDirectory = '../imagenes';
    if (!file_exists($imagesDirectory)) {
        mkdir($imagesDirectory, 0777, true);
    }
    return $imagesDirectory;
}

function handleImageUpload($imagen)
{
    $ruta_imagen = createImagesFolder() . '/' . $imagen['name'];
    // Lógica para validar la imagen, cambiar el nombre, etc.
    move_uploaded_file($imagen['tmp_name'], $ruta_imagen);
    // Devuelve el nuevo nombre de la imagen
    return $imagen['name'];
}


if (isset($_POST['add_pin'])) {
    $id = $_POST['id_presentacion'];
    $pin = $_POST['add_pin'];

    Presentacion::setPin($conn, $id, $pin);

    $id_presentacion = $id;
} else if (isset($_POST['drop_pin'])) {
    $id = $_POST['id_presentacion'];
    $pin = $_POST['drop_pin'];

    if (Presentacion::checkPin($conn, $id, $pin)) {
        Presentacion::setPin($conn, $id, 'null');
    }

    $id_presentacion = $id;
} else if (isset($_POST['home_url']) && isset($_POST['home_id_presentacion'])) {
    $id = $_POST['home_id_presentacion'];
    $changeUrl = $_POST['home_url'];

    $presentacionBD = Presentacion::getPresentacionBD($conn, $id);
    if ($changeUrl === 'true') {
        $presentacionBD->publica();
    }
    $presentacionBD->actualizarInfo($conn);
    header("Location: ../views/home.php");
    exit;
} else if (isset($_POST['presentacion_id'])) {
    $id_presentacion = $_POST['presentacion_id'];
    $presentacionBD = Presentacion::getPresentacionBD($conn, $id_presentacion);


    $presentacionBD->setTitulo($_POST['p_titulo']);
    $presentacionBD->setDescripcion($_POST['p_descripcion']);
    $presentacionBD->setTema($_POST['tema']);
    
    if ($_POST['url'] === 'true') {
        $presentacionBD->publica();
    }
    
    $hasPin = $presentacionBD->getPin() !== 'null';
    if ($_POST['modifyPin'] === 'true') {
        if ($hasPin) {
            $editPin = '&p=drop';
        } else {
            $editPin = '&p=new';
        }
    }
    $presentacionBD->actualizarInfo($conn);

    $ordenDiapositivas = isset($_POST['ordenDiapositivas']) ? explode(',', $_POST['ordenDiapositivas']) : [];

    // Obtén la lista de IDs de las diapositivas existentes en la base de datos para esta presentación
    $diapositivasExistentes = Diapositiva::obtenerIdsDiapositivasPorPresentacion($conn, $id_presentacion);
    
    // Compara y elimina las diapositivas que ya no existen
    foreach ($diapositivasExistentes as $idDiapositivaExistente) {
        if (!in_array($idDiapositivaExistente, $ordenDiapositivas)) {
            $diapositivaAEliminar = Diapositiva::getDiapositivaPorId($conn, $idDiapositivaExistente);
            if ($diapositivaAEliminar instanceof TipoImagen) {
                // Obtén el nombre de la imagen anterior
                if ($diapositivaAEliminar instanceof TipoImagen) {
                    $nombreImagenAnterior = $diapositivaAEliminar->getNombre_imagen();
                }

                // Puedes manejar la respuesta, como mostrar un mensaje de éxito o error
            }
                // Elimina la diapositiva y realiza cualquier otro proceso de eliminación necesario
                $mensaje = $diapositivaAEliminar->eliminarDiapositiva($conn, $id_presentacion);

                // Elimina la imagen anterior si existe
                if (!empty($nombreImagenAnterior)) {
                    $rutaImagenAnterior = createImagesFolder() . '/' . $nombreImagenAnterior;
                    if (file_exists($rutaImagenAnterior)) {
                        unlink($rutaImagenAnterior);
                    }
                }

        }
    }
    $tiposDiapositivas = [];

    foreach ($ordenDiapositivas as $orden => $idDiapositiva) {
        ++$orden; // Incrementa antes de usar
        if (strpos($idDiapositiva, 'new-') === false) {
            $editDiapositiva = Diapositiva::getDiapositivaPorId($conn, $idDiapositiva);
            if ($editDiapositiva) {
                $titulo = $_POST['d_titulo_' . $idDiapositiva] ?? '';
                $contenido = $_POST['d_contenido_' . $idDiapositiva] ?? '';

                $pregunta = $_POST['d_pregunta_' . $idDiapositiva] ?? '';
                $respuestaA = $_POST['d_respuesta_a_' . $idDiapositiva] ?? '';
                $respuestaB = $_POST['d_respuesta_b_' . $idDiapositiva] ?? '';
                $respuestaC = $_POST['d_respuesta_c_' . $idDiapositiva] ?? '';
                $respuestaD = $_POST['d_respuesta_d_' . $idDiapositiva] ?? '';
                $respuestaCorrecta = $_POST['d_respuesta_correcta_' . $idDiapositiva] ?? '';


                $editDiapositiva->setTitulo($titulo);

                if ($editDiapositiva instanceof TipoContenido) {
                    $editDiapositiva->setContenido($contenido);
                } elseif ($editDiapositiva instanceof TipoImagen) {
                    if (isset($_FILES['d_imagen_' . $idDiapositiva]) && $_FILES['d_imagen_' . $idDiapositiva]['error'] == UPLOAD_ERR_OK) {
                        $imagen = $_FILES['d_imagen_' . $idDiapositiva];
                        $nombre_imagen = $imagen['name'];
                        $ext = pathinfo($nombre_imagen, PATHINFO_EXTENSION);

                        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                            $unique_id = uniqid('', true);
                            $nombre_imagen = "imagen_$idDiapositiva" . "_" . $unique_id . '.' . $ext;
                            $ruta_imagen = createImagesFolder() . '/' . $nombre_imagen;

                            move_uploaded_file($imagen['tmp_name'], $ruta_imagen);

                            // Obtén el nombre de la imagen anterior
                            $nombreImagenAnterior = $editDiapositiva->getNombre_imagen();

                            // Actualiza el nombre de la imagen en la diapositiva
                            $editDiapositiva->setNombre_imagen($nombre_imagen);

                            // Elimina la imagen anterior si existe
                            if (!empty($nombreImagenAnterior)) {
                                $rutaImagenAnterior = createImagesFolder() . '/' . $nombreImagenAnterior;
                                if (file_exists($rutaImagenAnterior)) {
                                    unlink($rutaImagenAnterior);
                                }
                            }
                        } else {
                            $nombre_imagen = '';
                        }
                    } else {
                        // Si no se ha enviado un archivo, conserva el nombre de imagen existente
                        $nombre_imagen = $editDiapositiva->getNombre_imagen();
                    }

                    $editDiapositiva->setContenido($contenido);
                } elseif ($editDiapositiva instanceof TipoPregunta) {
                    // Manejar lógica específica para diapositivas de tipoPregunta

                    $editDiapositiva->setPregunta($pregunta);
                    $editDiapositiva->setRespuestaA($respuestaA);
                    $editDiapositiva->setRespuestaB($respuestaB);
                    $editDiapositiva->setRespuestaC($respuestaC);
                    $editDiapositiva->setRespuestaD($respuestaD);
                    $editDiapositiva->setRespuestaCorrecta($respuestaCorrecta);
                }
                $editDiapositiva->setOrden($orden);
                $editDiapositiva->actualizarDiapositiva($conn, $id_presentacion);
                Diapositiva::actualizarOrdenDiapositiva($conn, $editDiapositiva->getId(), $orden);
            }
        } else {
            // Es una nueva diapositiva
            $idDiapositiva = str_replace('new-', '', $idDiapositiva); // Corregir la variable $tempId
            $titulo = $_POST['d_titulo_new-' . $idDiapositiva] ?? '';
            $titulo = is_string($titulo) ? $titulo : ''; // Asignar una cadena vacía si $titulo no es una cadena válida 
            $contenido = $_POST['d_contenido_new_' . $idDiapositiva] ?? '';

            $pregunta = $_POST['d_pregunta_new-' . $idDiapositiva] ?? '';
            $respuestaA = $_POST['d_respuesta_A_new-' . $idDiapositiva] ?? '';
            $respuestaB = $_POST['d_respuesta_B_new-' . $idDiapositiva] ?? '';
            $respuestaC = $_POST['d_respuesta_C_new-' . $idDiapositiva] ?? '';
            $respuestaD = $_POST['d_respuesta_D_new-' . $idDiapositiva] ?? '';
            $respuestaCorrecta = $_POST['d_respuesta_correcta_new-' . $idDiapositiva] ?? '';

            // Crear una nueva diapositiva vacía del tipo correspondiente
            $tipoDiapositiva = explode('-', $idDiapositiva)[1]; // Extrae el tipo de la ID temporal

            // Almacena el tipo de diapositiva en el array
            $tiposDiapositivas[] = $tipoDiapositiva;

            // Antes de crear una nueva instancia de TipoImagen, asegúrate de que $nombre_imagen esté definida
            if ($tipoDiapositiva === 'imagen') {
                // Verifica si se ha subido una imagen para la nueva diapositiva
                if (isset($_FILES['d_imagen_new-' . $idDiapositiva]) && $_FILES['d_imagen_new-' . $id]['error'] == UPLOAD_ERR_OK) {
                    $imagen = $_FILES['d_imagen_new-' . $tempId];
                    $nombre_imagen = $imagen['name'];
                    $ext = pathinfo($nombre_imagen, PATHINFO_EXTENSION);

                    if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                        // Genera un nombre de archivo único para la imagen
                        $unique_id = uniqid('', true);
                        $nombre_imagen = "imagen_$idDiapositiva" . "_" . $unique_id . '.' . $ext;
                        $ruta_imagen = createImagesFolder() . '/' . $nombre_imagen;

                        // Mueve la imagen al directorio de imágenes
                        move_uploaded_file($imagen['tmp_name'], $ruta_imagen);
                    } else {
                        // Si el tipo de archivo no es válido, puedes asignar un valor predeterminado o vacío al nombre de la imagen
                        $nombre_imagen = '';
                    }
                } else {
                    $nombre_imagen = '';
                }

            }

            // Añade lógica para manejar diapositivas de tipoPregunta
            if ($tipoDiapositiva === 'pregunta') {
                // Manejar lógica específica para diapositivas de tipoPregunta
                $nuevaDiapositiva = new TipoPregunta(null, $titulo, $pregunta, $respuestaA, $respuestaB, $respuestaC, $respuestaD, $respuestaCorrecta);
            } elseif ($tipoDiapositiva === 'imagen') {
                // Crea una nueva diapositiva de tipo imagen y la añade a la presentación
                $nuevaDiapositiva = new TipoImagen(null, $titulo, $contenido, $nombre_imagen);
            } elseif ($tipoDiapositiva === 'contenido') {
                $nuevaDiapositiva = new TipoContenido(null, $titulo, $contenido);
            } else {
                $nuevaDiapositiva = new TipoTitulo(null, $titulo);
            }

            if (isset($nuevaDiapositiva)) {
                $nuevaDiapositiva->setOrden($orden);
                $nuevaDiapositiva->nuevaDiapositiva($conn, $id_presentacion);
            }
        }
    }
} else {
    // Crear una nueva presentación
    $titulo = $_POST['p_titulo'] ?? '';
    $descripcion = $_POST['p_descripcion'] ?? '';
    $tema = $_POST['tema'] ?? '';

    $diapositivas = [];

    $newPresentacion = new Presentacion(null, $titulo, $descripcion, $tema, 'null', 'null', $diapositivas);
    
    if ($_POST['url'] === 'true') {
        $newPresentacion->publica();
    }
    
    $newPresentacion->guardarNuevaPresentacion($conn);
    $id_presentacion = $newPresentacion->getId();

    $tiposDiapositivas = [];

    // Crear diapositivas para la nueva presentación
    $ordenDiapositivas = isset($_POST['ordenDiapositivas']) ? explode(',', $_POST['ordenDiapositivas']) : [];
    foreach ($ordenDiapositivas as $orden => $tempId) {
        $orden++; // Asegúrate de que el orden comience en 1
        $tempId = str_replace('new-', '', $tempId);
        $titulo = $_POST['d_titulo_new-' . $tempId] ?? '';
        $titulo = is_string($titulo) ? $titulo : ''; // Asignar una cadena vacía si $titulo no es una cadena válida 
        $contenido = $_POST['d_contenido_new-' . $tempId] ?? '';

        $pregunta = $_POST['d_pregunta_new-' . $tempId] ?? '';
        $respuestaA = $_POST['d_respuesta_A_new-' . $tempId] ?? '';
        $respuestaB = $_POST['d_respuesta_B_new-' . $tempId] ?? '';
        $respuestaC = $_POST['d_respuesta_C_new-' . $tempId] ?? '';
        $respuestaD = $_POST['d_respuesta_D_new-' . $tempId] ?? '';
        $respuestaCorrecta = $_POST['d_respuesta_correcta_new-' . $tempId] ?? '';


        // Crear una nueva diapositiva vacía del tipo correspondiente
        $tipoDiapositiva = explode('-', $tempId)[1]; // Extrae el tipo de la ID temporal

        // Almacena el tipo de diapositiva en el array
        $tiposDiapositivas[] = $tipoDiapositiva;

        // Antes de crear una nueva instancia de TipoImagen, asegúrate de que $nombre_imagen esté definida
        if ($tipoDiapositiva === 'imagen') {
            // Verifica si se ha subido una imagen para la nueva diapositiva
            if (isset($_FILES['d_imagen_new-' . $tempId]) && $_FILES['d_imagen_new-' . $tempId]['error'] == UPLOAD_ERR_OK) {
                $imagen = $_FILES['d_imagen_new-' . $tempId];
                $nombre_imagen = $imagen['name'];
                $ext = pathinfo($nombre_imagen, PATHINFO_EXTENSION);

                if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                    // Genera un nombre de archivo único para la imagen
                    $unique_id = uniqid('', true);
                    $nombre_imagen = "imagen_$tempId" . "_" . $unique_id . '.' . $ext;
                    $ruta_imagen = createImagesFolder() . '/' . $nombre_imagen;

                    // Mueve la imagen al directorio de imágenes
                    move_uploaded_file($imagen['tmp_name'], $ruta_imagen);
                } else {
                    // Si el tipo de archivo no es válido, puedes asignar un valor predeterminado o vacío al nombre de la imagen
                    $nombre_imagen = '';
                }
            } else {
                $nombre_imagen = '';
            }


        }

        // Ahora crea la diapositiva independientemente del tipo
        // Añade lógica para manejar diapositivas de tipoPregunta
        if ($tipoDiapositiva === 'pregunta') {
            // Manejar lógica específica para diapositivas de tipoPregunta
            $nuevaDiapositiva = new TipoPregunta(null, $titulo, $pregunta, $respuestaA, $respuestaB, $respuestaC, $respuestaD, $respuestaCorrecta);
        } elseif ($tipoDiapositiva === 'imagen') {
            // Crea una nueva diapositiva de tipo imagen y la añade a la presentación
            $nuevaDiapositiva = new TipoImagen(null, $titulo, $contenido, $nombre_imagen);
        } elseif ($tipoDiapositiva === 'contenido') {
            $nuevaDiapositiva = new TipoContenido(null, $titulo, $contenido);
        } else {
            $nuevaDiapositiva = new TipoTitulo(null, $titulo);
        }

        if (isset($nuevaDiapositiva)) {
            $nuevaDiapositiva->setOrden($orden);
            $nuevaDiapositiva->nuevaDiapositiva($conn, $id_presentacion);
        }
    }
    // Establece la variable de sesión para indicar que la creación fue exitosa
    $_SESSION['guardado_exitoso'] = true;

}


// Redirigir al usuario de vuelta a la página de creación de presentaciones
header("Location: ../views/editor.php?presentacion_id=" . $id_presentacion . $editPin);
exit;

