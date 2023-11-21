<?php

// Datos de conexión a la base de datos
define('DB_HOST', '192.168.56.1:8808'); // Dirección del servidor de base de datos
define('DB_USER', 'usuari'); // Nombre de usuario de la base de datos
define('DB_PASS', 'password1'); // Contraseña del usuario de la base de datos
define('DB_NAME', 'db_Presentaciones'); // Nombre de la base de datos

// Verificación para limitar el acceso directo
defined('VALID_ENTRY_POINT') or die('Access denied');

define('ROOT_PATH', realpath(dirname(__FILE__) . '/..') . '/');
