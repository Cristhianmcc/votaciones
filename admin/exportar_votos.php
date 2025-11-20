<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Exportar Votos Registrados a Excel (CSV)
 */

session_start();
include '../conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_rol'] != 'SUPERADMIN') {
    die("Acceso denegado");
}

// Filtros
$tipo_voto = isset($_GET['tipo']) ? limpiar_dato($_GET['tipo']) : '';
$buscar = isset($_GET['buscar']) ? limpiar_dato($_GET['buscar']) : '';

// Construir query
$where_clauses = array();
$params = array();
$param_count = 1;

if ($tipo_voto != '') {
    if ($is_production) {
        $where_clauses[] = "v.tipo_voto = $$param_count";
        $params[] = $tipo_voto;
        $param_count++;
    } else {
        $where_clauses[] = "v.tipo_voto = '$tipo_voto'";
    }
}

if ($buscar != '') {
    if ($is_production) {
        $where_clauses[] = "(c.dni LIKE $$param_count OR c.nombres ILIKE $$param_count OR c.apellido_paterno ILIKE $$param_count)";
        $params[] = "%$buscar%";
        $param_count++;
    } else {
        $where_clauses[] = "(c.dni LIKE '%$buscar%' OR c.nombres LIKE '%$buscar%' OR c.apellido_paterno LIKE '%$buscar%')";
    }
}

$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Obtener votos
if ($is_production) {
    $query = "SELECT v.id, v.tipo_voto, v.fecha_hora, 
                     c.dni, c.nombres, c.apellido_paterno, c.apellido_materno, c.departamento,
                     p.siglas as partido_siglas, p.nombre_completo as partido_nombre,
                     cand.nombres as candidato_nombres, cand.apellido_paterno as candidato_paterno
              FROM tbl_voto v
              INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
              LEFT JOIN tbl_partido p ON v.partido_id = p.id
              LEFT JOIN tbl_candidato cand ON p.id = cand.partido_id AND cand.cargo = 'PRESIDENTE'
              $where_sql
              ORDER BY v.fecha_hora DESC";
    
    if (count($params) > 0) {
        $resultado = pg_query_params($conexion, $query, $params);
    } else {
        $resultado = pg_query($conexion, $query);
    }
    
    $votos = [];
    while ($fila = pg_fetch_assoc($resultado)) {
        $votos[] = $fila;
    }
} else {
    $query = "SELECT v.id, v.tipo_voto, v.fecha_hora, 
                     c.dni, c.nombres, c.apellido_paterno, c.apellido_materno, c.departamento,
                     p.siglas as partido_siglas, p.nombre_completo as partido_nombre,
                     cand.nombres as candidato_nombres, cand.apellido_paterno as candidato_paterno
              FROM tbl_voto v
              INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
              LEFT JOIN tbl_partido p ON v.partido_id = p.id
              LEFT JOIN tbl_candidato cand ON p.id = cand.partido_id AND cand.cargo = 'PRESIDENTE'
              $where_sql
              ORDER BY v.fecha_hora DESC";
    
    $resultado = mysqli_query($conexion, $query);
    $votos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $votos[] = $fila;
    }
}

// Preparar para exportar
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=votos_registrados_' . date('Y-m-d_His') . '.csv');

// Crear output
$output = fopen('php://output', 'w');

// BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezados
fputcsv($output, array(
    'ID',
    'Fecha y Hora',
    'DNI',
    'Nombres',
    'Apellido Paterno',
    'Apellido Materno',
    'Departamento',
    'Tipo de Voto',
    'Partido (Siglas)',
    'Partido (Nombre Completo)',
    'Candidato Presidente'
));

// Datos
foreach ($votos as $voto) {
    $candidato = '';
    if ($voto['tipo_voto'] == 'VALIDO' && $voto['candidato_nombres']) {
        $candidato = $voto['candidato_nombres'] . ' ' . $voto['candidato_paterno'];
    } elseif ($voto['tipo_voto'] == 'BLANCO') {
        $candidato = 'VOTO EN BLANCO';
    } else {
        $candidato = 'VOTO NULO O VICIADO';
    }
    
    fputcsv($output, array(
        $voto['id'],
        date('d/m/Y H:i:s', strtotime($voto['fecha_hora'])),
        $voto['dni'],
        $voto['nombres'],
        $voto['apellido_paterno'],
        $voto['apellido_materno'],
        $voto['departamento'],
        $voto['tipo_voto'],
        $voto['partido_siglas'] ?? '',
        $voto['partido_nombre'] ?? '',
        $candidato
    ));
}

fclose($output);
exit();
