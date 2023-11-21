<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/home.css">
    <title>Pantalla d'inici</title>
</head>

<body>
    <h2>Presentaciones</h2>
    <div id="global">
        <div id="añadirPresentacion">
            <a href="editor.php">
                <div class="caja_añadir">
                    <img src="../assets/icons/crear.svg" alt="Crear">
                </div>
            </a>
            <p id="añadir">AÑADIR</p>
        </div>

        <!-- Capa de superposición -->
        <div class="overlay" id="overlay"></div>

        <?php
        session_start();

        define('VALID_ENTRY_POINT', true);

        include '../../config.php';
        require_once '../models/db.php';

        // Obtener la única instancia de la base de datos
        $db = Database::getInstance();

        // Obtener la conexión a la base de datos
        $conn = $db->getConnection();

        $query = "SELECT id, titulo, url FROM presentacion";
        $result = $conn->query($query);
        $copy = 'flex';


        // Generar dinámicamente los divs con class="caja"
        while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <?php
            if ($row['url'] === 'null') {
                $copy = 'none';
            }
            ?>
            <div class="caja">
                <p>
                    <?= htmlspecialchars($row['titulo']) ?>
                </p>
                <input type="hidden" name="id_presentacion" form="previewForm_<?= $row['id'] ?>" value="<?= $row['id'] ?>">
                <form hidden id="previewForm_<?= $row['id'] ?>" action="preview.php" method="POST" hidden>
                </form>
                <div class="imagen-overlay" onclick="document.forms['previewForm_<?= $row['id'] ?>'].submit();">
                    <form action="preview.php" method="POST">
                        <input type="hidden" name="id_presentacion" value="<?= $row['id'] ?>">
                        <img src="../assets/icons/ver.png" alt="" type="submit">
                    </form>
                </div>
                <button class="opciones-btn clickable">+</button>
                <div class="opciones clickable" id="opciones<?= $row['id'] ?>">
                    <form action="editor.php" method="GET">
                        <input type="hidden" name="presentacion_id" value="<?= $row['id'] ?>">
                        <button class="editar clickable"><img src="../assets/icons/editar.svg" alt="Editar">Editar</button>
                    </form>
                    <button class="clonar clickable"><img src="../assets/icons/clonar.svg" alt="Clonar">Clonar</button>
                    <form action="../controllers/eliminar.controller.php" method="POST"
                        onclick="mostrarConfirmacionEliminar(event, this)">
                        <input type="hidden" name="id_presentacion" value="<?= $row['id'] ?>">
                        <button type="submit" class="eliminar"><img src="../assets/icons/eliminarHome.svg"
                                alt="Eliminar">Eliminar</button>
                    </form>
                    <form id="publicar_form" action="../controllers/editor.controller.php" method="POST">
                        <input type="hidden" name="home_id_presentacion" value="<?= $row['id'] ?>">
                        <input type="hidden" name="home_url" value="<?= $row['url'] ?>">
                        <button type="submit" class="eliminar"><img src="../assets/icons/publicadaHome.svg"
                                alt="Publicar">Publicar</button>
                    </form>
                    <button id="copyUrlButton" class="clonar clickable" style="display:<?= $copy ?>;"><img
                            src="../assets/icons/copyHome.svg" alt="Clonar">Copiar URL</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!--Mensaje para confirmar la eliminación de una presentación-->
    <dialog id="confirmarEliminar">
        <p>¿Estás seguro de que deseas eliminar esta presentación?</p>
        <form method="dialog">
            <button id="btn-aceptar">Aceptar</button>
            <button id="btn-cancelar">Cancelar</button>
        </form>
    </dialog>

    <dialog id="exito_eliminar">
        <p>La presentación se ha eliminado correctamente</p>
        <form method="dialog">
            <button id="btn-aceptar-exito">Aceptar</button>
        </form>
    </dialog>

    <script src="../js/home.js"></script>

    <script>
        mostrarExitoEliminar();
        // Función para mostrar el diálogo de éxito al eliminar
        function mostrarExitoEliminar() {
            const exitoEliminarDialog = document.getElementById("exito_eliminar");
            const overlay = document.getElementById("overlay");
            <?php
            if (isset($_SESSION['eliminacion_exitosa']) && $_SESSION['eliminacion_exitosa'] === true) {
                echo 'exitoEliminarDialog.style.display = "block";';
                echo 'overlay.style.display = "block";';
                // Elimina la variable de sesión para evitar que se muestre nuevamente al recargar la página.
                unset($_SESSION['eliminacion_exitosa']);
            }
            ?>

            // Agrega un event listener al botón "Aceptar" en el diálogo de éxito
            const btnExitoAceptar = document.getElementById("btn-aceptar-exito");
            btnExitoAceptar.addEventListener("click", function () {
                // Oculta el diálogo de éxito
                exitoEliminarDialog.style.display = "none";
                overlay.style.display = "none";

            });
        }
    </script>

</body>

</html>