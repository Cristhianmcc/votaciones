<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Exportar Resultados Electorales a Excel (CSV)
 */

session_start();
include '../conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

// Obtener resultados por partido
if ($is_production) {
    $query = "SELECT 
              p.siglas, 
              p.nombre_completo,
              COUNT(v.id) as total_votos,
              ROUND((COUNT(v.id)::numeric / NULLIF((SELECT COUNT(*) FROM tbl_voto WHERE tipo_voto = 'VALIDO'), 0)) * 100, 2) as porcentaje
              FROM tbl_partido p
              LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.tipo_voto = 'VALIDO'
              WHERE p.estado = true
              GROUP BY p.id, p.siglas, p.nombre_completo
              ORDER BY total_votos DESC";
    $resultado = pg_query($conexion, $query);
    $partidos = [];
    while ($fila = pg_fetch_assoc($resultado)) {
        $partidos[] = $fila;
    }
    
    // Estadísticas generales
    $query_stats = "SELECT 
                    COUNT(*) as total_votos,
                    COUNT(CASE WHEN tipo_voto = 'VALIDO' THEN 1 END) as votos_validos,
                    COUNT(CASE WHEN tipo_voto = 'BLANCO' THEN 1 END) as votos_blancos,
                    COUNT(CASE WHEN tipo_voto = 'NULO' THEN 1 END) as votos_nulos
                    FROM tbl_voto";
    $res_stats = pg_query($conexion, $query_stats);
    $stats = pg_fetch_assoc($res_stats);
    
    pg_close($conexion);
    
} else {
    $query = "SELECT 
              p.siglas, 
              p.nombre_completo,
              COUNT(v.id) as total_votos,
              ROUND((COUNT(v.id) / NULLIF((SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'VALIDO'), 0)) * 100, 2) as porcentaje
              FROM tbl_partido p
              LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.voto_tipo = 'VALIDO'
              WHERE p.estado = 1
              GROUP BY p.id, p.siglas, p.nombre_completo
              ORDER BY total_votos DESC";
    $resultado = mysqli_query($conexion, $query);
    $partidos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $partidos[] = $fila;
    }
    
    // Estadísticas generales
    $query_stats = "SELECT 
                    COUNT(*) as total_votos,
                    SUM(CASE WHEN voto_tipo = 'VALIDO' THEN 1 ELSE 0 END) as votos_validos,
                    SUM(CASE WHEN voto_tipo = 'BLANCO' THEN 1 ELSE 0 END) as votos_blancos,
                    SUM(CASE WHEN voto_tipo = 'NULO' THEN 1 ELSE 0 END) as votos_nulos
                    FROM tbl_voto";
    $res_stats = mysqli_query($conexion, $query_stats);
    $stats = mysqli_fetch_assoc($res_stats);
    
    mysqli_close($conexion);
}

// Preparar para exportar
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=resultados_electorales_' . date('Y-m-d_His') . '.csv');

// Crear output
$output = fopen('php://output', 'w');

// BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Título del reporte
fputcsv($output, array('RESULTADOS ELECTORALES - ELECCIONES PERÚ 2026'));
fputcsv($output, array('Fecha de Generación: ' . date('d/m/Y H:i:s')));
fputcsv($output, array(''));

// Resumen General
fputcsv($output, array('RESUMEN GENERAL'));
fputcsv($output, array('Total de Votos:', $stats['total_votos']));
fputcsv($output, array('Votos Válidos:', $stats['votos_validos'], round(($stats['votos_validos'] / $stats['total_votos']) * 100, 2) . '%'));
fputcsv($output, array('Votos en Blanco:', $stats['votos_blancos'], round(($stats['votos_blancos'] / $stats['total_votos']) * 100, 2) . '%'));
fputcsv($output, array('Votos Nulos:', $stats['votos_nulos'], round(($stats['votos_nulos'] / $stats['total_votos']) * 100, 2) . '%'));
fputcsv($output, array(''));

// Resultados por Partido
fputcsv($output, array('RESULTADOS POR PARTIDO POLÍTICO'));
fputcsv($output, array('Posición', 'Siglas', 'Nombre del Partido', 'Total de Votos', 'Porcentaje'));

$posicion = 1;
foreach ($partidos as $partido) {
    fputcsv($output, array(
        $posicion,
        $partido['siglas'],
        $partido['nombre_completo'],
        $partido['total_votos'],
        number_format($partido['porcentaje'], 2) . '%'
    ));
    $posicion++;
}

fclose($output);
exit();
?>
