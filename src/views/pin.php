<?php
session_start();

// Punto de entrada principal
define('VALID_ENTRY_POINT', true);
include '../../config.php';

require_once '../models/db.php';
require_once '../models/Presentacion.php';
require_once '../models/Diapositiva.php';
require_once '../models/TipoTitulo.php';
require_once '../models/TipoContenido.php';
require_once '../models/TipoImagen.php';
require_once '../models/TipoPregunta.php';

// Obtener la única instancia de la base de datos
$db = Database::getInstance();

// Obtener la conexión a la base de datos
$conn = $db->getConnection();

$url = $_GET['url'];
$presentacion = Presentacion::getPresentacionByURL($conn, $url);
if ($presentacion === null) {
    header("Location: ../views/home.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pin.css">
    <title>PIN</title>
</head>

<body>
    <dialog id="insert_pin" class="dialogPin">
        <p>Introduce el PIN</p>
        <form method="POST" action="../views/preview.php?url=<?= $_GET['url'] ?>">
            <div><span>PIN actual</span>
                <input type="password" name="pin" minlength="4" maxlength="8">
            </div>
            <button class="button" type="submit">Aceptar</button>
        </form>
    </dialog>
</body>

</html>