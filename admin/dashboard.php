<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Dashboard Principal de Administración
 */

session_start();

// Verificar que sea un administrador logueado
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - ONPE 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .header-admin {
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            padding: 25px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            margin-bottom: 40px;
        }

        .menu-card {
            background: linear-gradient(135deg, #1a1f3a 0%, #2d3561 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            border: 2px solid rgba(220, 20, 60, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            color: white;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(220, 20, 60, 0.5);
            border-color: #DC143C;
            color: white;
        }

        .menu-icon {
            font-size: 60px;
            color: #DC143C;
            margin-bottom: 20px;
        }

        .menu-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .menu-description {
            color: #a0a0a0;
            font-size: 14px;
        }

        .user-info {
            background: rgba(255,255,255,0.1);
            padding: 15px 25px;
            border-radius: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-admin">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 style="font-size: 32px; margin: 0;">
                        <i class="fas fa-shield-alt me-3"></i>PANEL DE ADMINISTRACIÓN
                    </h1>
                    <p style="margin: 5px 0 0 0; opacity: 0.9;">Sistema Electoral Perú 2026 - ONPE</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="user-info">
                        <small style="opacity: 0.8; display: block;">
                            <i class="fas fa-user-shield me-1"></i>Administrador
                        </small>
                        <strong><?php echo htmlspecialchars($_SESSION['admin_nombres']); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Ver Resultados -->
            <div class="col-md-6 col-lg-4">
                <a href="../resultados_publicos.php" class="menu-card">
                    <div class="text-center">
                        <i class="fas fa-chart-line menu-icon"></i>
                        <div class="menu-title">Resultados en Tiempo Real</div>
                        <div class="menu-description">
                            Visualiza estadísticas y gráficos de votación en tiempo real
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gestión de Votos -->
            <div class="col-md-6 col-lg-4">
                <a href="gestionar_votos.php" class="menu-card">
                    <div class="text-center">
                        <i class="fas fa-vote-yea menu-icon"></i>
                        <div class="menu-title">Votos Registrados</div>
                        <div class="menu-description">
                            Administra y verifica los votos registrados
                        </div>
                    </div>
                </a>
            </div>

            <!-- Padrón Electoral -->
            <div class="col-md-6 col-lg-4">
                <a href="gestionar_padron.php" class="menu-card">
                    <div class="text-center">
                        <i class="fas fa-address-book menu-icon"></i>
                        <div class="menu-title">Padrón Electoral</div>
                        <div class="menu-description">
                            Gestiona ciudadanos habilitados para votar
                        </div>
                    </div>
                </a>
            </div>

            <!-- Partidos Políticos -->
            <div class="col-md-6 col-lg-4">
                <a href="gestionar_partidos.php" class="menu-card">
                    <div class="text-center">
                        <i class="fas fa-flag menu-icon"></i>
                        <div class="menu-title">Partidos Políticos</div>
                        <div class="menu-description">
                            Administra partidos políticos registrados
                        </div>
                    </div>
                </a>
            </div>

            <!-- Candidatos -->
            <div class="col-md-6 col-lg-4">
                <a href="gestionar_candidatos.php" class="menu-card">
                    <div class="text-center">
                        <i class="fas fa-users menu-icon"></i>
                        <div class="menu-title">Candidatos</div>
                        <div class="menu-description">
                            Gestiona candidatos presidenciales y vicepresidentes
                        </div>
                    </div>
                </a>
            </div>

            <!-- Reportes -->
            <div class="col-md-6 col-lg-4">
                <a href="#" class="menu-card" onclick="alert('Módulo en desarrollo'); return false;">
                    <div class="text-center">
                        <i class="fas fa-file-alt menu-icon"></i>
                        <div class="menu-title">Reportes</div>
                        <div class="menu-description">
                            Genera reportes y exporta datos electorales
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gestionar Administradores (Solo SUPERADMIN) -->
            <?php if ($_SESSION['admin_rol'] === 'SUPERADMIN'): ?>
            <div class="col-md-6 col-lg-4">
                <a href="gestionar_administradores.php" class="menu-card" style="border-color: rgba(255, 193, 7, 0.5);">
                    <div class="text-center">
                        <i class="fas fa-users-cog menu-icon" style="color: #ffc107;"></i>
                        <div class="menu-title">Administradores</div>
                        <div class="menu-description">
                            Crear y gestionar usuarios administradores
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>

            <!-- Cerrar Sesión -->
            <div class="col-md-6 col-lg-4">
                <a href="logout_admin.php" class="menu-card" style="border-color: rgba(220, 20, 60, 0.5);">
                    <div class="text-center">
                        <i class="fas fa-sign-out-alt menu-icon"></i>
                        <div class="menu-title">Cerrar Sesión</div>
                        <div class="menu-description">
                            Salir del panel administrativo
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="text-center">
            <small style="opacity: 0.6;">
                <i class="fas fa-shield-alt me-1"></i>
                Sistema Electoral Perú 2026 - Panel de Administración v1.0
            </small>
        </div>
    </div>
</body>
</html>
