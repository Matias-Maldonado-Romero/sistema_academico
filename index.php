<?php
session_start();

require_once "config/config.php";
require_once "config/database.php";

require_once "controladores/plantilla.controlador.php";
require_once "controladores/usuarios.controlador.php";

require_once "modelos/usuarios.modelo.php";
require_once "modelos/listas.modelo.php";

$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();