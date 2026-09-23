<?php
// Archivo: config/config.php

// Detección automática o ruta fija de tu proyecto
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// ⚠️ CAMBIA "sistema-academico" por el nombre EXACTO de la carpeta de tu proyecto en htdocs
define("BASE_URL", $protocolo . "://" . $host . "/sistema_academico/");
?>