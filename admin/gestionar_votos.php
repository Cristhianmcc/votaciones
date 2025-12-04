<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Gestión de Votos Registrados - Panel Administrativo
 */

session_start();
include '../conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_rol'] != 'SUPERADMIN') {
    header("Location: index.php");
    exit();
}

// Filtros
$tipo_voto = isset($_GET['tipo']) ? limpiar_dato($_GET['tipo']) : '';
$buscar = isset($_GET['buscar']) ? limpiar_dato($_GET['buscar']) : '';
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$por_pagina = 50;
$offset = ($pagina - 1) * $por_pagina;

// Construir query según filtros
$where_clauses = array();
$params = array();
$param_count = 1;

if ($tipo_voto != '') {
    if ($is_production) {
        $where_clauses[] = "v.tipo_voto = $$param_count";
        $params[] = $tipo_voto;
        $param_count++;
    } else {
        $where_clauses[] = "v.voto_tipo = '$tipo_voto'";
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

// Obtener votos registrados
if ($is_production) {
    // PostgreSQL: tipo_voto, fecha_hora
    $query = "SELECT v.id, v.tipo_voto, v.fecha_hora, 
                     c.dni, c.nombres, c.apellido_paterno, c.apellido_materno, c.departamento,
                     p.siglas as partido_siglas, p.nombre_completo as partido_nombre,
                     cand.nombres as candidato_nombres, cand.apellido_paterno as candidato_paterno
              FROM tbl_voto v
              INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
              LEFT JOIN tbl_partido p ON v.partido_id = p.id
              LEFT JOIN tbl_candidato cand ON p.id = cand.partido_id AND cand.cargo = 'PRESIDENTE'
              $where_sql
              ORDER BY v.fecha_hora DESC
              LIMIT $por_pagina OFFSET $offset";
    
    if (count($params) > 0) {
        $resultado = pg_query_params($conexion, $query, $params);
    } else {
        $resultado = pg_query($conexion, $query);
    }
    
    $votos = [];
    while ($fila = pg_fetch_assoc($resultado)) {
        $votos[] = $fila;
    }
    
    // Total de registros
    $query_total = "SELECT COUNT(*) as total FROM tbl_voto v INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id $where_sql";
    if (count($params) > 0) {
        $res_total = pg_query_params($conexion, $query_total, $params);
    } else {
        $res_total = pg_query($conexion, $query_total);
    }
    $total_row = pg_fetch_assoc($res_total);
    $total_registros = $total_row['total'];
    
} else {
    // MySQL: voto_tipo, fecha_voto, tipo_candidato
    $query = "SELECT v.id, v.voto_tipo as tipo_voto, v.fecha_voto as fecha_hora, 
                     c.dni, c.nombres, c.apellido_paterno, c.apellido_materno, c.departamento,
                     p.siglas as partido_siglas, p.nombre_completo as partido_nombre,
                     cand.nombres as candidato_nombres, cand.apellido_paterno as candidato_paterno
              FROM tbl_voto v
              INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
              LEFT JOIN tbl_partido p ON v.partido_id = p.id
              LEFT JOIN tbl_candidato cand ON p.id = cand.partido_id AND cand.tipo_candidato = 'PRESIDENTE'
              $where_sql
              ORDER BY v.fecha_voto DESC
              LIMIT $por_pagina OFFSET $offset";
    
    $resultado = mysqli_query($conexion, $query);
    $votos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $votos[] = $fila;
    }
    
    // Total de registros
    $query_total = "SELECT COUNT(*) as total FROM tbl_voto v INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id $where_sql";
    $res_total = mysqli_query($conexion, $query_total);
    $total_row = mysqli_fetch_assoc($res_total);
    $total_registros = $total_row['total'];
}

$total_paginas = ceil($total_registros / $por_pagina);

// Estadísticas generales
if ($is_production) {
    $stats_query = "SELECT 
                    COUNT(CASE WHEN tipo_voto = 'VALIDO' THEN 1 END) as votos_validos,
                    COUNT(CASE WHEN tipo_voto = 'BLANCO' THEN 1 END) as votos_blancos,
                    COUNT(CASE WHEN tipo_voto = 'NULO' THEN 1 END) as votos_nulos,
                    COUNT(*) as total_votos
                    FROM tbl_voto";
    $stats_res = pg_query($conexion, $stats_query);
    $stats = pg_fetch_assoc($stats_res);
} else {
    $stats_query = "SELECT 
                    SUM(CASE WHEN voto_tipo = 'VALIDO' THEN 1 ELSE 0 END) as votos_validos,
                    SUM(CASE WHEN voto_tipo = 'BLANCO' THEN 1 ELSE 0 END) as votos_blancos,
                    SUM(CASE WHEN voto_tipo = 'NULO' THEN 1 ELSE 0 END) as votos_nulos,
                    COUNT(*) as total_votos
                    FROM tbl_voto";
    $stats_res = mysqli_query($conexion, $stats_query);
    $stats = mysqli_fetch_assoc($stats_res);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votos Registrados - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white p-3 vh-100">
                <h5>Panel Admin</h5>
                <hr>
                <a href="dashboard.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="gestionar_partidos.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-flag"></i> Partidos
                </a>
                <a href="gestionar_candidatos.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-users"></i> Candidatos
                </a>
                <a href="gestionar_padron.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-address-book"></i> Padrón Electoral
                </a>
                <a href="gestionar_votos.php" class="text-white text-decoration-none d-block mb-2 fw-bold">
                    <i class="fas fa-vote-yea"></i> Votos Registrados
                </a>
                <hr>
                <a href="../logout.php" class="text-danger text-decoration-none">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>

            <!-- Contenido -->
            <div class="col-md-10 p-4">
                <h2><i class="fas fa-vote-yea text-warning"></i> Votos Registrados</h2>
                <hr>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3><?php echo number_format($stats['votos_validos'] ?? 0); ?></h3>
                                <p class="mb-0">Votos Válidos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h3><?php echo number_format($stats['votos_blancos'] ?? 0); ?></h3>
                                <p class="mb-0">Votos en Blanco</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3><?php echo number_format($stats['votos_nulos'] ?? 0); ?></h3>
                                <p class="mb-0">Votos Nulos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3><?php echo number_format($stats['total_votos'] ?? 0); ?></h3>
                                <p class="mb-0">Total de Votos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label>Tipo de Voto</label>
                                <select name="tipo" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="VALIDO" <?php echo $tipo_voto == 'VALIDO' ? 'selected' : ''; ?>>Válido</option>
                                    <option value="BLANCO" <?php echo $tipo_voto == 'BLANCO' ? 'selected' : ''; ?>>Blanco</option>
                                    <option value="NULO" <?php echo $tipo_voto == 'NULO' ? 'selected' : ''; ?>>Nulo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Buscar Ciudadano</label>
                                <input type="text" name="buscar" class="form-control" placeholder="DNI o nombre..." value="<?php echo htmlspecialchars($buscar); ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-info me-2">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="gestionar_votos.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Votos -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Fecha y Hora</th>
                                <th>DNI</th>
                                <th>Ciudadano</th>
                                <th>Departamento</th>
                                <th>Tipo de Voto</th>
                                <th>Partido/Candidato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($votos) > 0): ?>
                                <?php foreach ($votos as $voto): ?>
                                <tr>
                                    <td><?php echo $voto['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($voto['fecha_hora'])); ?></td>
                                    <td><strong><?php echo $voto['dni']; ?></strong></td>
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($voto['nombres'] . ' ' . 
                                             $voto['apellido_paterno'] . ' ' . 
                                             $voto['apellido_materno']); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($voto['departamento']); ?></td>
                                    <td>
                                        <?php 
                                        $badge_class = $voto['tipo_voto'] == 'VALIDO' ? 'bg-success' : 
                                                      ($voto['tipo_voto'] == 'BLANCO' ? 'bg-secondary' : 'bg-danger');
                                        echo '<span class="badge ' . $badge_class . '">' . $voto['tipo_voto'] . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($voto['tipo_voto'] == 'VALIDO' && $voto['partido_siglas']): ?>
                                            <strong><?php echo htmlspecialchars($voto['partido_siglas']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php 
                                                if ($voto['candidato_nombres']) {
                                                    echo htmlspecialchars($voto['candidato_nombres'] . ' ' . $voto['candidato_paterno']);
                                                }
                                                ?>
                                            </small>
                                        <?php elseif ($voto['tipo_voto'] == 'BLANCO'): ?>
                                            <span class="text-muted"><i>Voto en blanco</i></span>
                                        <?php else: ?>
                                            <span class="text-muted"><i>Voto nulo o viciado</i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mt-3 mb-2"></i>
                                        <p>No hay votos registrados con los filtros seleccionados</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php 
                        $url_params = '';
                        if ($tipo_voto) $url_params .= '&tipo=' . urlencode($tipo_voto);
                        if ($buscar) $url_params .= '&buscar=' . urlencode($buscar);
                        
                        for ($i = 1; $i <= $total_paginas; $i++): 
                        ?>
                            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i . $url_params; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <!-- Botón Exportar -->
                <div class="mt-3">
                    <a href="exportar_votos.php<?php echo $tipo_voto || $buscar ? '?' . http_build_query(['tipo' => $tipo_voto, 'buscar' => $buscar]) : ''; ?>" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
