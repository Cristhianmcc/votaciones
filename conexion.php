<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Archivo de conexión a la base de datos
 */

// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root";
$clave = "root";
$base_datos = "db_elecciones_2026";

// Crear conexión
$conexion = mysqli_connect($servidor, $usuario, $clave, $base_datos);

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Configurar charset UTF-8
mysqli_set_charset($conexion, "utf8mb4");

// Configurar zona horaria
date_default_timezone_set('America/Lima');

// Función para limpiar datos de entrada
function limpiar_dato($dato) {
    global $conexion;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    $dato = mysqli_real_escape_string($conexion, $dato);
    return $dato;
}

// Función para obtener IP del cliente
function obtener_ip_cliente() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>
