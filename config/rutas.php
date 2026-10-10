<?php
/**
 * RUTAS DEL SISTEMA - fuente única de verdad
 *
 * La usan plantilla.php (para permitir o negar el acceso) y sidebar.php
 * (para armar el menú). Cada ruta indica:
 *   - titulo : texto del menú
 *   - icono  : clase de Bootstrap Icons
 *   - roles  : roles que pueden entrar (valores del ENUM usuarios.rol)
 *   - menu   : false para rutas que no aparecen en el menú (por defecto true)
 *
 * Para agregar un módulo nuevo:
 *   1. crear vistas/modulos/<ruta>.php
 *   2. agregar una línea aquí
 * El orden de este arreglo es el orden del menú.
 */

$todos   = ["admin", "director", "secretaria", "docente", "tutor", "estudiante"];
$gestion = ["admin", "director", "secretaria"];
$gestionInscripciones = ["admin", "director", "secretaria", "tutor"];

return [
    "inicio" => [
        "titulo" => "Inicio",
        "icono"  => "bi-grid-fill",
        "roles"  => $todos
    ],
    "inscripciones" => [
        "titulo" => "Inscripciones",
        "icono"  => "bi-file-earmark-medical-fill",
        "roles"  => $gestionInscripciones
    ],
    "materias" => [
        "titulo" => "Materias",
        "icono"  => "bi-stack",
        "roles"  => $gestion
    ],
    "calificaciones" => [
        "titulo" => "Calificaciones",
        "icono"  => "bi-grid-1x2-fill",
        "roles"  => ["admin", "director", "secretaria", "docente"]
    ],
    // Pendientes de reemplazar por los módulos de Cursos y Docentes (esquema anterior)
    "grupos" => [
        "titulo" => "Grupos",
        "icono"  => "bi-people-fill",
        "roles"  => ["admin", "director"]
    ],
    "usuarios" => [
        "titulo" => "Usuarios",
        "icono"  => "bi-person-fill",
        "roles"  => ["admin"]
    ],
    "salir" => [
        "titulo" => "Salir",
        "icono"  => "bi-box-arrow-right",
        "roles"  => $todos,
        "menu"   => false
    ]
];
