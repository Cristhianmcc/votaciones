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
                    COUNT(CASE WHEN partido_id IS NOT NULL THEN 1 END) as votos_validos,
                    COUNT(CASE WHEN partido_id IS NULL THEN 1 END) as votos_blancos,
                    0 as votos_nulos
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
    
    // Votos por partido (incluyendo blanco)
    $query_votos_partido = "SELECT 
                            p.siglas, 
                            p.nombre_completo,
                            p.color,
                            COUNT(v.id) as total_votos,
                            ROUND((COUNT(v.id)::numeric / NULLIF((SELECT COUNT(*) FROM tbl_voto), 0)) * 100, 2) as porcentaje
                            FROM tbl_partido p
                            LEFT JOIN tbl_voto v ON p.id = v.partido_id
                            WHERE p.estado = true
                            GROUP BY p.id, p.siglas, p.nombre_completo, p.color
                            
                            UNION ALL
                            
                            SELECT 
                            '' as siglas,
                            'Voto en Blanco' as nombre_completo,
                            '#FFFFFF' as color,
                            COUNT(*)::bigint as total_votos,
                            ROUND((COUNT(*)::numeric / NULLIF((SELECT COUNT(*) FROM tbl_voto), 0)) * 100, 2) as porcentaje
                            FROM tbl_voto
                            WHERE partido_id IS NULL
                            
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
                    SUM(CASE WHEN partido_id IS NOT NULL THEN 1 ELSE 0 END) as votos_validos,
                    SUM(CASE WHEN partido_id IS NULL THEN 1 ELSE 0 END) as votos_blancos,
                    0 as votos_nulos
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
    
    // Votos por partido (incluyendo blanco)
    $query_votos_partido = "SELECT 
                            p.siglas, 
                            p.nombre_completo,
                            p.color_primario as color,
                            COUNT(v.id) as total_votos,
                            ROUND((COUNT(v.id) / NULLIF((SELECT COUNT(*) FROM tbl_voto), 0)) * 100, 2) as porcentaje
                            FROM tbl_partido p
                            LEFT JOIN tbl_voto v ON p.id = v.partido_id
                            WHERE p.estado = 1
                            GROUP BY p.id, p.siglas, p.nombre_completo, p.color_primario
                            
                            UNION ALL
                            
                            SELECT 
                            '' as siglas,
                            'Voto en Blanco' as nombre_completo,
                            '#FFFFFF' as color,
                            COUNT(*) as total_votos,
                            ROUND((COUNT(*) / NULLIF((SELECT COUNT(*) FROM tbl_voto), 0)) * 100, 2) as porcentaje
                            FROM tbl_voto
                            WHERE partido_id IS NULL
                            
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
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold flex items-center gap-3">
                        <i class="fas fa-chart-line"></i>
                        Reportes y Estadísticas
                    </h1>
                    <p class="text-red-100 mt-2 text-sm">Sistema Electoral Perú 2026</p>
                </div>
                <a href="dashboard.php" class="inline-flex items-center gap-2 bg-white text-red-600 px-6 py-3 rounded-lg font-semibold hover:bg-red-50 transition-all shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Estadísticas Generales -->
        <div class="mb-10">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-chart-bar text-red-600"></i> Resumen General
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium mb-2">Ciudadanos Habilitados</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent">
                                <?php echo number_format($total_ciudadanos); ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-full">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium mb-2">Votos Registrados</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent">
                                <?php echo number_format($total_votos); ?>
                            </p>
                        </div>
                        <div class="bg-purple-100 p-4 rounded-full">
                            <i class="fas fa-vote-yea text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium mb-2">Participación Electoral</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent">
                                <?php echo $participacion; ?>%
                            </p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-full">
                            <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium mb-2">Partidos Políticos</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent">
                                <?php echo $partidos['total']; ?>
                            </p>
                        </div>
                        <div class="bg-orange-100 p-4 rounded-full">
                            <i class="fas fa-flag text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Votos -->
        <div class="mb-10">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-pie-chart text-red-600"></i> Distribución de Votos
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-900">Votos Válidos</h5>
                    </div>
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['votos_validos']); ?></span>
                            <span class="text-green-600 font-bold"><?php echo $porcentaje_validos; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all" 
                                 style="width: <?php echo $porcentaje_validos; ?>%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500"><?php echo number_format($stats['votos_validos']); ?> votos</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <i class="fas fa-file text-yellow-600 text-2xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-900">Votos en Blanco</h5>
                    </div>
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['votos_blancos']); ?></span>
                            <span class="text-yellow-600 font-bold"><?php echo $porcentaje_blancos; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-3 rounded-full transition-all" 
                                 style="width: <?php echo $porcentaje_blancos; ?>%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500"><?php echo number_format($stats['votos_blancos']); ?> votos</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-900">Votos Nulos</h5>
                    </div>
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['votos_nulos']); ?></span>
                            <span class="text-red-600 font-bold"><?php echo $porcentaje_nulos; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-3 rounded-full transition-all" 
                                 style="width: <?php echo $porcentaje_nulos; ?>%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500"><?php echo number_format($stats['votos_nulos']); ?> votos</p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <!-- Gráfico de Votos por Partido -->
            <div class="chart-container">
                <h4 class="mb-4 flex items-center gap-2"><i class="fas fa-flag"></i> Votos por Partido Político</h4>
                <canvas id="chartPartidos"></canvas>
            </div>

            <!-- Gráfico de Tipo de Votos -->
            <div class="chart-container">
                <h4 class="mb-4 flex items-center gap-2"><i class="fas fa-chart-pie"></i> Distribución por Tipo de Voto</h4>
                <canvas id="chartTipoVotos"></canvas>
            </div>
        </div>

        <!-- Tabla de Resultados por Partido -->
        <div class="mb-10">
            <div class="chart-container">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h4 class="flex items-center gap-2 mb-0"><i class="fas fa-trophy"></i> Resultados Detallados por Partido</h4>
                    <a href="exportar_resultados.php" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors gap-2 text-sm font-medium">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Posición</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Partido</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Siglas</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Votos</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Porcentaje</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Visualización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php 
                            $posicion = 1;
                            foreach ($votos_por_partido as $partido): 
                                $porcentaje_partido = $partido['porcentaje'] ?? 0;
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <?php if ($posicion == 1): ?>
                                        <i class="fas fa-trophy text-yellow-500 text-xl"></i>
                                    <?php elseif ($posicion == 2): ?>
                                        <i class="fas fa-medal text-gray-400 text-xl"></i>
                                    <?php elseif ($posicion == 3): ?>
                                        <i class="fas fa-medal text-orange-600 text-xl"></i>
                                    <?php else: ?>
                                        <span class="text-gray-500 font-medium"><?php echo $posicion; ?>°</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium"><?php echo htmlspecialchars($partido['nombre_completo']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-white text-sm font-medium" style="background-color: <?php echo $partido['color']; ?>">
                                        <?php echo htmlspecialchars($partido['siglas']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-900"><?php echo number_format($partido['total_votos']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-red-600"><?php echo number_format($porcentaje_partido, 2); ?>%</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="w-full bg-gray-200 rounded-full h-5">
                                        <div class="h-5 rounded-full transition-all" 
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

        <!-- Votos por Departamento -->
        <?php if (count($votos_por_depto) > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <div class="chart-container">
                <h4 class="mb-4 flex items-center gap-2"><i class="fas fa-map-marked-alt"></i> Top 10 Departamentos con Más Votos</h4>
                <canvas id="chartDepartamentos"></canvas>
            </div>

            <div class="chart-container">
                <h4 class="mb-4 flex items-center gap-2"><i class="fas fa-clock"></i> Votos por Hora del Día</h4>
                <canvas id="chartHoras"></canvas>
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
