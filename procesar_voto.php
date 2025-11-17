<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Procesamiento del Voto
 */

session_start();
include 'conexion.php';

// Verificar que el ciudadano esté logueado
if (!isset($_SESSION['ciudadano_dni'])) {
    header("Location: index.php");
    exit();
}

// Verificar si ya votó
if (isset($_SESSION['ha_votado']) && $_SESSION['ha_votado'] == 1) {
    header("Location: confirmacion_voto.php");
    exit();
}

// Verificar que sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cedula_votacion.php");
    exit();
}

// Obtener datos del voto
$partido_id = intval($_POST['partido_id'] ?? 0);
$tiempo_votacion = intval($_POST['tiempo_votacion'] ?? 0);
$dni_ciudadano = $_SESSION['ciudadano_dni'];
$ip_address = obtener_ip_cliente();

// Validar que se haya seleccionado un partido
if ($partido_id <= 0) {
    header("Location: cedula_votacion.php?error=no_seleccion");
    exit();
}

try {
    // Registrar el voto usando procedimiento almacenado
    $query = "CALL sp_registrar_voto('$dni_ciudadano', $partido_id, 'VALIDO', '$ip_address', $tiempo_votacion)";
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado) {
        $respuesta = mysqli_fetch_assoc($resultado);
        
        // Actualizar sesión
        $_SESSION['ha_votado'] = 1;
        $_SESSION['voto_registrado'] = true;
        $_SESSION['partido_votado'] = $partido_id;
        $_SESSION['fecha_voto'] = date('Y-m-d H:i:s');
        
        mysqli_close($conexion);
        
        // Redirigir a confirmación
        header("Location: confirmacion_voto.php");
        exit();
    } else {
        throw new Exception(mysqli_error($conexion));
    }
    
} catch (Exception $e) {
    mysqli_close($conexion);
    
    // Verificar tipo de error
    $error_msg = $e->getMessage();
    
    if (strpos($error_msg, 'ya emitió su voto') !== false) {
        $_SESSION['ha_votado'] = 1;
        header("Location: confirmacion_voto.php");
    } else {
        header("Location: cedula_votacion.php?error=registro_fallido&msg=" . urlencode($error_msg));
    }
    exit();
}
?>
