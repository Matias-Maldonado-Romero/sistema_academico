<?php
// Archivo: controladores/plantilla.controlador.php

class ControladorPlantilla {

    // Método que llama a la plantilla
    public function ctrPlantilla() {
        // Incluimos el archivo que contiene todo el HTML y la lógica de rutas
        include "vistas/plantilla.php";
    }

}