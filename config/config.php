<?php
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$rutaBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), "/\\");

define("BASE_URL", $protocolo . "://" . $host . ($rutaBase === "" ? "/" : $rutaBase . "/"));
?>