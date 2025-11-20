<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Archivo de conexión a la base de datos
 */

// Configuración de la base de datos
// =====================================================
// DESARROLLO: MySQL local (Windows tiene problemas DNS con Supabase)
// PRODUCCIÓN: Descomentar Supabase cuando subas a un servidor real
// =====================================================

// MYSQL (localhost) - DESARROLLO LOCAL
$servidor = "localhost";
$usuario = "root";
$clave = "root";
$base_datos = "db_elecciones_2026";
$conexion = mysqli_connect($servidor, $usuario, $clave, $base_datos);

// =====================================================
// SUPABASE (PostgreSQL) - PRODUCCIÓN (Funcionará en servidor Linux)
// =====================================================
// $supabase_host = "db.kvjnvvwbxdlporvwdupy.supabase.co";
// $supabase_user = "postgres";
// $supabase_password = "kikomoreno1";
// $supabase_database = "postgres";
// $supabase_port = 5432;
// $conexion = pg_connect("host=$supabase_host port=$supabase_port dbname=$supabase_database user=$supabase_user password=$supabase_password sslmode=require");

// Verificar conexión
if (!$conexion) {
    // MySQL:
    die("Error de conexión: " . mysqli_connect_error());
    // PostgreSQL:
    // die("Error de conexión a Supabase: " . pg_last_error());
}

// Configurar zona horaria
date_default_timezone_set('America/Lima');

// Función para limpiar datos de entrada (MySQL)
function limpiar_dato($dato) {
    global $conexion;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    $dato = mysqli_real_escape_string($conexion, $dato);
    // PostgreSQL: $dato = pg_escape_string($conexion, $dato);
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
