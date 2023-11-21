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

// Obtener la única instancia de la base de datos
$db = Database::getInstance();

// Obtener la conexión a la base de datos
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $presentacion_id = $_POST['id_presentacion'];

    Presentacion::eliminarPresentacionBD($conn, $presentacion_id);

    // Establece la variable de sesión para indicar que la eliminación fue exitosa
    $_SESSION['eliminacion_exitosa'] = true;
}

header("Location: ../views/home.php");
?>