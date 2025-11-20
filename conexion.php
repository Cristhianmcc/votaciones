<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Archivo de conexión a la base de datos
 */

date_default_timezone_set('America/Lima');

// =====================================================
// CONFIGURACIÓN AUTOMÁTICA: Local vs Producción
// =====================================================

// Detectar si estamos en producción (Render) o local (Windows)
$is_production = isset($_SERVER['RENDER']) || isset($_SERVER['RAILWAY_ENVIRONMENT']) || 
                 (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'onrender.com') !== false);

if ($is_production) {
    // =====================================================
    // PRODUCCIÓN: Railway PostgreSQL
    // =====================================================
    $db_host = getenv('PGHOST') ?: 'gondola.proxy.rlwy.net';
    $db_port = getenv('PGPORT') ?: '16689';
    $db_user = getenv('PGUSER') ?: 'postgres';
    $db_password = getenv('PGPASSWORD') ?: 'aGYdhNjZOzgKBaboFadrLUuwMJwhMPft';
    $db_name = getenv('PGDATABASE') ?: 'railway';
    
    $conexion = @pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_password sslmode=require");
    
    if (!$conexion) {
        die("Error de conexión a Railway PostgreSQL: " . pg_last_error());
    }
    
    pg_set_client_encoding($conexion, "UTF8");
    
} else {
    // =====================================================
    // DESARROLLO LOCAL: MySQL
    // =====================================================
    $servidor = "localhost";
    $usuario = "root";
    $clave = "root";
    $base_datos = "db_elecciones_2026";
    $conexion = mysqli_connect($servidor, $usuario, $clave, $base_datos);
    
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conexion, "utf8mb4");
}

// Función para limpiar datos de entrada
function limpiar_dato($dato) {
    global $conexion, $is_production;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    
    if ($is_production) {
        $dato = pg_escape_string($conexion, $dato); // PostgreSQL
    } else {
        $dato = mysqli_real_escape_string($conexion, $dato); // MySQL
    }
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
