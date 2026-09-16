<?php
// 1. Iniciamos la sesión global del sistema
session_start();

// 2. Cargamos configuraciones y la conexión a BD
require_once "config/config.php";
require_once "config/database.php";

// 3. Cargamos los controladores requeridos
require_once "controladores/plantilla.controlador.php";
require_once "controladores/usuarios.controlador.php";

// 4. Cargamos los modelos requeridos
require_once "modelos/usuarios.modelo.php";

// 5. Instanciamos la plantilla e iniciamos la vista
$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();