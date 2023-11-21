<?php
// Definir el punto de entrada
define('VALID_ENTRY_POINT', true);

// Incluir archivo de configuración
include '../../config.php';

// Incluir la clases.
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
$presentacion;
$requiredPin = 'none';
$exitButton = '';

if (isset($_GET['url'])) {
    $presentacion = Presentacion::getPresentacionByURL($conn, $_GET['url']);
    if ($presentacion === null) {
        header("Location: ../views/home.php");
        exit;
    }
    $id_presentacion = $presentacion->getId();
    $pin = $presentacion->getPin();
    if ($pin !== 'null') {
        if (isset($_POST["pin"])) {
            if (!$presentacion::checkPin($conn, $id_presentacion, $_POST["pin"])) {
                header("Location: ../views/pin.php?url=" . $_GET['url'] . "");
                exit;
            }
        } else {
            header("Location: ../views/pin.php?url=" . $_GET['url'] . "");
            exit;
        }
    }
} else {
    $presentacion = Presentacion::getPresentacionBD($conn, $_POST['id_presentacion']);
    $exitButton = '<a href="javascript: history.go(-1)"><img src="../assets/icons/exit.svg"></a>';
}

$tema = $presentacion->getTema();
$firstDiapositiva = isset($_POST['diapositiva_id']) ? $_POST['diapositiva_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/preview.css">
    <title>Previsualización</title>
</head>

<body tema="<?= $tema ?>">
    <div id="diapositivaAnterior">
        <button onclick="anterior()"><img src="../assets/icons/leftArrow.svg"></button>
    </div>
    <div id="diapositivaPosterior">
        <div class="actionButtons">
            <img src="../assets/icons/fullscreen.svg" onclick="activarPantallaCompleta()">
            <?= $exitButton ?>
        </div>
        <button onclick="siguiente()"><img src="../assets/icons/rightArrow.svg"></button>
    </div>
    <div id="miniaturas">
        <?php
        $diapositivas = $presentacion->getDiapositivas();
        foreach ($diapositivas as $diapositiva) {
            echo $diapositiva->getMiniatura();
        }
        ?>
    </div>
    <div id="diapositivas" firstDiapositiva="<?= $firstDiapositiva ?>">
        <?php
        $diapositivas = $presentacion->getDiapositivas();
        foreach ($diapositivas as $diapositiva) {
            echo $diapositiva->getDiapositivaPreview();
        }
        ?>
    </div>
    <script src="../js/preview.js"></script>
</body>

</html>