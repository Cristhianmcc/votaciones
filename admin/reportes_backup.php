<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Módulo de Reportes y Estadísticas
 */

session_start();
include '../conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

// Obtener estadísticas generales
if ($is_production) {
    // Estadísticas de votos
    $query_stats = "SELECT 
                    COUNT(*) as total_votos,
                    COUNT(CASE WHEN tipo_voto = 'VALIDO' THEN 1 END) as votos_validos,
                    COUNT(CASE WHEN tipo_voto = 'BLANCO' THEN 1 END) as votos_blancos,
                    COUNT(CASE WHEN tipo_voto = 'NULO' THEN 1 END) as votos_nulos
                    FROM tbl_voto";
    $res_stats = pg_query($conexion, $query_stats);
    $stats = pg_fetch_assoc($res_stats);
    
    // Total de ciudadanos
    $query_ciudadanos = "SELECT COUNT(*) as total FROM tbl_ciudadano WHERE estado = true";
    $res_ciudadanos = pg_query($conexion, $query_ciudadanos);
    $ciudadanos = pg_fetch_assoc($res_ciudadanos);
    
    // Total de partidos
    $query_partidos = "SELECT COUNT(*) as total FROM tbl_partido WHERE estado = true";
    $res_partidos = pg_query($conexion, $query_partidos);
    $partidos = pg_fetch_assoc($res_partidos);
    
    // Votos por partido
    $query_votos_partido = "SELECT 
                            p.siglas, 
                            p.nombre_completo,
                            p.color,
                            COUNT(v.id) as total_votos,
                            ROUND((COUNT(v.id)::numeric / NULLIF((SELECT COUNT(*) FROM tbl_voto WHERE tipo_voto = 'VALIDO'), 0)) * 100, 2) as porcentaje
                            FROM tbl_partido p
                            LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.tipo_voto = 'VALIDO'
                            WHERE p.estado = true
                            GROUP BY p.id, p.siglas, p.nombre_completo, p.color
                            ORDER BY total_votos DESC";
    $res_votos_partido = pg_query($conexion, $query_votos_partido);
    $votos_por_partido = [];
    while ($fila = pg_fetch_assoc($res_votos_partido)) {
        $votos_por_partido[] = $fila;
    }
    
    // Votos por departamento
    $query_votos_depto = "SELECT 
                          c.departamento,
                          COUNT(v.id) as total_votos
                          FROM tbl_voto v
                          INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
                          GROUP BY c.departamento
                          ORDER BY total_votos DESC
                          LIMIT 10";
    $res_votos_depto = pg_query($conexion, $query_votos_depto);
    $votos_por_depto = [];
    while ($fila = pg_fetch_assoc($res_votos_depto)) {
        $votos_por_depto[] = $fila;
    }
    
    // Votos por hora
    $query_votos_hora = "SELECT 
                         EXTRACT(HOUR FROM fecha_hora) as hora,
                         COUNT(*) as total_votos
                         FROM tbl_voto
                         GROUP BY hora
                         ORDER BY hora";
    $res_votos_hora = pg_query($conexion, $query_votos_hora);
    $votos_por_hora = [];
    while ($fila = pg_fetch_assoc($res_votos_hora)) {
        $votos_por_hora[] = $fila;
    }
    
    pg_close($conexion);
    
} else {
    // MYSQL Local
    // Estadísticas de votos
    $query_stats = "SELECT 
                    COUNT(*) as total_votos,
                    SUM(CASE WHEN voto_tipo = 'VALIDO' THEN 1 ELSE 0 END) as votos_validos,
                    SUM(CASE WHEN voto_tipo = 'BLANCO' THEN 1 ELSE 0 END) as votos_blancos,
                    SUM(CASE WHEN voto_tipo = 'NULO' THEN 1 ELSE 0 END) as votos_nulos
                    FROM tbl_voto";
    $res_stats = mysqli_query($conexion, $query_stats);
    $stats = mysqli_fetch_assoc($res_stats);
    
    // Total de ciudadanos
    $query_ciudadanos = "SELECT COUNT(*) as total FROM tbl_ciudadano WHERE estado = 1";
    $res_ciudadanos = mysqli_query($conexion, $query_ciudadanos);
    $ciudadanos = mysqli_fetch_assoc($res_ciudadanos);
    
    // Total de partidos
    $query_partidos = "SELECT COUNT(*) as total FROM tbl_partido WHERE estado = 1";
    $res_partidos = mysqli_query($conexion, $query_partidos);
    $partidos = mysqli_fetch_assoc($res_partidos);
    
    // Votos por partido
    $query_votos_partido = "SELECT 
                            p.siglas, 
                            p.nombre_completo,
                            p.color_primario as color,
                            COUNT(v.id) as total_votos,
                            ROUND((COUNT(v.id) / NULLIF((SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'VALIDO'), 0)) * 100, 2) as porcentaje
                            FROM tbl_partido p
                            LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.voto_tipo = 'VALIDO'
                            WHERE p.estado = 1
                            GROUP BY p.id, p.siglas, p.nombre_completo, p.color_primario
                            ORDER BY total_votos DESC";
    $res_votos_partido = mysqli_query($conexion, $query_votos_partido);
    $votos_por_partido = [];
    while ($fila = mysqli_fetch_assoc($res_votos_partido)) {
        $votos_por_partido[] = $fila;
    }
    
    // Votos por departamento
    $query_votos_depto = "SELECT 
                          c.departamento,
                          COUNT(v.id) as total_votos
                          FROM tbl_voto v
                          INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
                          GROUP BY c.departamento
                          ORDER BY total_votos DESC
                          LIMIT 10";
    $res_votos_depto = mysqli_query($conexion, $query_votos_depto);
    $votos_por_depto = [];
    while ($fila = mysqli_fetch_assoc($res_votos_depto)) {
        $votos_por_depto[] = $fila;
    }
    
    // Votos por hora
    $query_votos_hora = "SELECT 
                         HOUR(fecha_voto) as hora,
                         COUNT(*) as total_votos
                         FROM tbl_voto
                         GROUP BY hora
                         ORDER BY hora";
    $res_votos_hora = mysqli_query($conexion, $query_votos_hora);
    $votos_por_hora = [];
    while ($fila = mysqli_fetch_assoc($res_votos_hora)) {
        $votos_por_hora[] = $fila;
    }
    
    mysqli_close($conexion);
}

// Calcular porcentajes
$total_votos = intval($stats['total_votos']);
$total_ciudadanos = intval($ciudadanos['total']);
$participacion = $total_ciudadanos > 0 ? round(($total_votos / $total_ciudadanos) * 100, 2) : 0;

$porcentaje_validos = $total_votos > 0 ? round(($stats['votos_validos'] / $total_votos) * 100, 2) : 0;
$porcentaje_blancos = $total_votos > 0 ? round(($stats['votos_blancos'] / $total_votos) * 100, 2) : 0;
$porcentaje_nulos = $total_votos > 0 ? round(($stats['votos_nulos'] / $total_votos) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Estadísticas - ONPE 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        border: "hsl(214.3 31.8% 91.4%)",
                        input: "hsl(214.3 31.8% 91.4%)",
                        ring: "hsl(222.2 84% 4.9%)",
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(222.2 84% 4.9%)",
                        primary: {
                            DEFAULT: "hsl(222.2 47.4% 11.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        secondary: {
                            DEFAULT: "hsl(210 40% 96.1%)",
                            foreground: "hsl(222.2 47.4% 11.2%)",
                        },
                        destructive: {
                            DEFAULT: "hsl(0 84.2% 60.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        muted: {
                            DEFAULT: "hsl(210 40% 96.1%)",
                            foreground: "hsl(215.4 16.3% 46.9%)",
                        },
                        accent: {
                            DEFAULT: "hsl(210 40% 96.1%)",
                            foreground: "hsl(222.2 47.4% 11.2%)",
                        },
                        popover: {
                            DEFAULT: "hsl(0 0% 100%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                        card: {
                            DEFAULT: "hsl(0 0% 100%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                    },
                    borderRadius: {
                        lg: "0.5rem",
                        md: "calc(0.5rem - 2px)",
                        sm: "calc(0.5rem - 4px)",
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(to bottom right, #f8fafc, #e2e8f0);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .card {
            background: white;
            border: 1px solid hsl(214.3 31.8% 91.4%);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            transition: all 0.2s;
        }
        
        .card:hover {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        
        .stat-card {
            background: white;
            border: 1px solid hsl(214.3 31.8% 91.4%);
            border-radius: 0.5rem;
            padding: 1.5rem;
            transition: all 0.2s;
        }
        
        .stat-card:hover {
            border-color: hsl(222.2 47.4% 11.2%);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(to right, #DC143C, #8B0000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: hsl(215.4 16.3% 46.9%);
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #DC143C, #8B0000);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        
        .btn-secondary {
            background: hsl(210 40% 96.1%);
            color: hsl(222.2 47.4% 11.2%);
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid hsl(214.3 31.8% 91.4%);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: white;
            border-color: hsl(222.2 47.4% 11.2%);
        }
        
        .progress-bar-modern {
            background: hsl(210 40% 96.1%);
            border-radius: 9999px;
            height: 0.5rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
        
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-modern thead th {
            background: hsl(210 40% 96.1%);
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: hsl(222.2 47.4% 11.2%);
            border-bottom: 1px solid hsl(214.3 31.8% 91.4%);
        }
        
        .table-modern thead th:first-child {
            border-top-left-radius: 0.5rem;
        }
        
        .table-modern thead th:last-child {
            border-top-right-radius: 0.5rem;
        }
        
        .table-modern tbody tr {
            border-bottom: 1px solid hsl(214.3 31.8% 91.4%);
            transition: background 0.2s;
        }
        
        .table-modern tbody tr:hover {
            background: hsl(210 40% 98%);
        }
        
        .table-modern tbody td {
            padding: 1rem;
            font-size: 0.875rem;
        }
        
        .chart-container-modern {
            background: white;
            border: 1px solid hsl(214.3 31.8% 91.4%);
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-3">
                        <i class="fas fa-chart-line"></i>
                        Reportes y Estadísticas
                    </h1>
                    <p class="text-red-100 mt-1">Sistema Electoral Perú 2026</p>
                </div>
                <a href="dashboard.php" class="btn-secondary bg-white/10 text-white border-white/20 hover:bg-white/20">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3"><i class="fas fa-chart-bar"></i> Resumen General</h3>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?php echo number_format($total_ciudadanos); ?></div>
                    <div class="stat-label">Ciudadanos Habilitados</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?php echo number_format($total_votos); ?></div>
                    <div class="stat-label">Votos Registrados</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?php echo $participacion; ?>%</div>
                    <div class="stat-label">Participación Electoral</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?php echo $partidos['total']; ?></div>
                    <div class="stat-label">Partidos Políticos</div>
                </div>
            </div>
        </div>

        <!-- Distribución de Votos -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3"><i class="fas fa-pie-chart"></i> Distribución de Votos</h3>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="mb-3"><i class="fas fa-check-circle text-success"></i> Votos Válidos</h5>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?php echo $porcentaje_validos; ?>%">
                            <?php echo $porcentaje_validos; ?>%
                        </div>
                    </div>
                    <p class="mb-0"><?php echo number_format($stats['votos_validos']); ?> votos</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="mb-3"><i class="fas fa-file text-warning"></i> Votos en Blanco</h5>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: <?php echo $porcentaje_blancos; ?>%">
                            <?php echo $porcentaje_blancos; ?>%
                        </div>
                    </div>
                    <p class="mb-0"><?php echo number_format($stats['votos_blancos']); ?> votos</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="mb-3"><i class="fas fa-times-circle text-danger"></i> Votos Nulos</h5>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: <?php echo $porcentaje_nulos; ?>%">
                            <?php echo $porcentaje_nulos; ?>%
                        </div>
                    </div>
                    <p class="mb-0"><?php echo number_format($stats['votos_nulos']); ?> votos</p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <!-- Gráfico de Votos por Partido -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="mb-4"><i class="fas fa-flag"></i> Votos por Partido Político</h4>
                    <canvas id="chartPartidos"></canvas>
                </div>
            </div>

            <!-- Gráfico de Tipo de Votos -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="mb-4"><i class="fas fa-chart-pie"></i> Distribución por Tipo de Voto</h4>
                    <canvas id="chartTipoVotos"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla de Resultados por Partido -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-trophy"></i> Resultados Detallados por Partido</h4>
                        <a href="exportar_resultados.php" class="btn btn-export">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Partido</th>
                                    <th>Siglas</th>
                                    <th>Total Votos</th>
                                    <th>Porcentaje</th>
                                    <th>Visualización</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $posicion = 1;
                                foreach ($votos_por_partido as $partido): 
                                    $porcentaje_partido = $partido['porcentaje'] ?? 0;
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($posicion == 1): ?>
                                            <i class="fas fa-trophy text-warning"></i>
                                        <?php elseif ($posicion == 2): ?>
                                            <i class="fas fa-medal" style="color: #C0C0C0;"></i>
                                        <?php elseif ($posicion == 3): ?>
                                            <i class="fas fa-medal" style="color: #CD7F32;"></i>
                                        <?php else: ?>
                                            <span class="text-muted"><?php echo $posicion; ?>°</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($partido['nombre_completo']); ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo $partido['color']; ?>">
                                            <?php echo htmlspecialchars($partido['siglas']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo number_format($partido['total_votos']); ?></strong></td>
                                    <td>
                                        <strong style="color: #DC143C;"><?php echo number_format($porcentaje_partido, 2); ?>%</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" 
                                                 style="width: <?php echo $porcentaje_partido; ?>%; 
                                                        background-color: <?php echo $partido['color']; ?>">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                $posicion++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Votos por Departamento -->
        <?php if (count($votos_por_depto) > 0): ?>
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="mb-4"><i class="fas fa-map-marked-alt"></i> Top 10 Departamentos con Más Votos</h4>
                    <canvas id="chartDepartamentos"></canvas>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="mb-4"><i class="fas fa-clock"></i> Votos por Hora del Día</h4>
                    <canvas id="chartHoras"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Botones de Acción -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <a href="gestionar_votos.php" class="btn btn-export me-3">
                    <i class="fas fa-vote-yea"></i> Ver Votos Registrados
                </a>
                <a href="exportar_votos.php" class="btn btn-export">
                    <i class="fas fa-download"></i> Exportar Todos los Votos
                </a>
            </div>
        </div>
    </div>

    <script>
        // Datos para gráficos
        const partidosData = <?php echo json_encode($votos_por_partido); ?>;
        const tipoVotosData = {
            validos: <?php echo $stats['votos_validos']; ?>,
            blancos: <?php echo $stats['votos_blancos']; ?>,
            nulos: <?php echo $stats['votos_nulos']; ?>
        };
        const departamentosData = <?php echo json_encode($votos_por_depto); ?>;
        const horasData = <?php echo json_encode($votos_por_hora); ?>;

        // Gráfico de Partidos
        const ctxPartidos = document.getElementById('chartPartidos').getContext('2d');
        new Chart(ctxPartidos, {
            type: 'bar',
            data: {
                labels: partidosData.map(p => p.siglas),
                datasets: [{
                    label: 'Votos',
                    data: partidosData.map(p => p.total_votos),
                    backgroundColor: partidosData.map(p => p.color),
                    borderColor: partidosData.map(p => p.color),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' votos';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Gráfico de Tipo de Votos
        const ctxTipoVotos = document.getElementById('chartTipoVotos').getContext('2d');
        new Chart(ctxTipoVotos, {
            type: 'doughnut',
            data: {
                labels: ['Votos Válidos', 'Votos en Blanco', 'Votos Nulos'],
                datasets: [{
                    data: [tipoVotosData.validos, tipoVotosData.blancos, tipoVotosData.nulos],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderColor: '#1a1f3a',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'white',
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(2);
                                return context.label + ': ' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Departamentos
        <?php if (count($votos_por_depto) > 0): ?>
        const ctxDepartamentos = document.getElementById('chartDepartamentos').getContext('2d');
        new Chart(ctxDepartamentos, {
            type: 'horizontalBar',
            data: {
                labels: departamentosData.map(d => d.departamento),
                datasets: [{
                    label: 'Votos',
                    data: departamentosData.map(d => d.total_votos),
                    backgroundColor: 'rgba(220, 20, 60, 0.7)',
                    borderColor: '#DC143C',
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Gráfico de Horas
        const ctxHoras = document.getElementById('chartHoras').getContext('2d');
        new Chart(ctxHoras, {
            type: 'line',
            data: {
                labels: horasData.map(h => h.hora + ':00'),
                datasets: [{
                    label: 'Votos por Hora',
                    data: horasData.map(h => h.total_votos),
                    backgroundColor: 'rgba(220, 20, 60, 0.2)',
                    borderColor: '#DC143C',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
