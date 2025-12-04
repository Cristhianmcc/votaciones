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
    
    // Configuración de Supabase Storage
    define('SUPABASE_URL', 'https://matatan05sproject.supabase.co');
    define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1hdGF0YW4wNXNwcm9qZWN0Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzMxOTQ4MjEsImV4cCI6MjA0ODc3MDgyMX0.CkWkUbcxYK_cxPPNKZdqDEbkexqJnzumfFMKiUAE_8g');
    
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

/**
 * Subir archivo a Supabase Storage o sistema de archivos local
 * @param array $file Array de $_FILES['nombre_campo']
 * @param string $bucket Nombre del bucket (candidatos, partidos)
 * @param string $filename Nombre del archivo
 * @return string|false URL del archivo o false si falla
 */
function subir_archivo($file, $bucket, $filename) {
    global $is_production;
    
    if ($is_production) {
        // PRODUCCIÓN: Subir a Supabase Storage
        $file_content = file_get_contents($file['tmp_name']);
        $mime_type = mime_content_type($file['tmp_name']);
        
        $url = SUPABASE_URL . "/storage/v1/object/$bucket/$filename";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file_content);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . SUPABASE_KEY,
            'Content-Type: ' . $mime_type,
            'apikey: ' . SUPABASE_KEY
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code == 200 || $http_code == 201) {
            // Retornar URL pública de Supabase
            return SUPABASE_URL . "/storage/v1/object/public/$bucket/$filename";
        }
        
        return false;
    } else {
        // LOCAL: Guardar en sistema de archivos
        $ruta_local = '../assets/img/' . $bucket . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $ruta_local)) {
            return 'assets/img/' . $bucket . '/' . $filename;
        }
        
        return false;
    }
}
?>
