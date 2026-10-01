<?php
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

define("BASE_URL", $protocolo . "://" . $host . "/sistema_academico/");
?>