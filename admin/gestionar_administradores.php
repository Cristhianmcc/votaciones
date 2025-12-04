<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Gestión de Administradores
 */

session_start();

// Verificar que sea un administrador logueado
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

// Solo SUPERADMIN puede gestionar otros administradores
if ($_SESSION['admin_rol'] !== 'SUPERADMIN') {
    header("Location: dashboard.php?error=no_autorizado");
    exit();
}

include '../conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';
    
    // CREAR NUEVO ADMINISTRADOR
    if ($accion === 'crear') {
        $usuario = trim($_POST['usuario'] ?? '');
        $clave = $_POST['clave'] ?? '';
        $nombres = trim($_POST['nombres'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rol = $_POST['rol'] ?? 'MODERADOR';
        
        // Validar rol
        $roles_validos = ['SUPERADMIN', 'MODERADOR', 'OBSERVADOR'];
        if (!in_array($rol, $roles_validos)) {
            $rol = 'MODERADOR';
        }
        
        if (empty($usuario) || empty($clave) || empty($nombres)) {
            $mensaje = 'Todos los campos son obligatorios';
            $tipo_mensaje = 'danger';
        } else {
            // Verificar que el usuario no exista con PREPARED STATEMENT - Modo dual
            $usuario_existe = false;
            
            if ($is_production) {
                // POSTGRESQL (Producción):
                $stmt_check_name = 'check_user_' . uniqid();
                pg_prepare($conexion, $stmt_check_name, "SELECT id FROM tbl_administrador WHERE usuario = $1");
                $result_check = pg_execute($conexion, $stmt_check_name, array($usuario));
                $usuario_existe = (pg_num_rows($result_check) > 0);
            } else {
                // MYSQL (Local):
                $stmt_check = mysqli_prepare($conexion, "SELECT id FROM tbl_administrador WHERE usuario = ?");
                mysqli_stmt_bind_param($stmt_check, "s", $usuario);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $usuario_existe = (mysqli_num_rows($result_check) > 0);
                mysqli_stmt_close($stmt_check);
            }
            
            if ($usuario_existe) {
                $mensaje = 'El usuario ya existe';
                $tipo_mensaje = 'warning';
            } else {
                // Crear admin con PREPARED STATEMENT - Modo dual
                $clave_md5 = md5($clave);
                $insert_exitoso = false;
                
                if ($is_production) {
                    // POSTGRESQL (Producción):
                    $stmt_insert_name = 'insert_admin_' . uniqid();
                    pg_prepare($conexion, $stmt_insert_name, "INSERT INTO tbl_administrador (usuario, clave, nombres, email, rol) VALUES ($1, $2, $3, $4, $5)");
                    $result_insert = pg_execute($conexion, $stmt_insert_name, array($usuario, $clave_md5, $nombres, $email, $rol));
                    $insert_exitoso = (bool)$result_insert;
                } else {
                    // MYSQL (Local):
                    $stmt_insert = mysqli_prepare($conexion, "INSERT INTO tbl_administrador (usuario, clave, nombres, email, rol) VALUES (?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt_insert, "sssss", $usuario, $clave_md5, $nombres, $email, $rol);
                    $insert_exitoso = mysqli_stmt_execute($stmt_insert);
                    mysqli_stmt_close($stmt_insert);
                }
                
                if ($insert_exitoso) {
                    $mensaje = 'Administrador creado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al crear el administrador';
                    $tipo_mensaje = 'danger';
                }
            }
        }
    }
    
    // CAMBIAR ESTADO
    if ($accion === 'cambiar_estado') {
        $admin_id = intval($_POST['admin_id'] ?? 0);
        $nuevo_estado = intval($_POST['nuevo_estado'] ?? 0);
        
        // Validar estado (0 o 1 solamente)
        $nuevo_estado = ($nuevo_estado === 1) ? 1 : 0;
        
        // No permitir desactivarse a sí mismo
        if ($admin_id == $_SESSION['admin_id']) {
            $mensaje = 'No puedes desactivar tu propia cuenta';
            $tipo_mensaje = 'warning';
        } elseif ($admin_id > 0) {
            // PREPARED STATEMENT - Modo dual
            $update_exitoso = false;
            
            if ($is_production) {
                // POSTGRESQL (Producción): estado es booleano (true/false)
                $estado_bool = ($nuevo_estado === 1) ? 'true' : 'false';
                $stmt_estado_name = 'update_estado_' . uniqid();
                pg_prepare($conexion, $stmt_estado_name, "UPDATE tbl_administrador SET estado = $1 WHERE id = $2");
                $result = pg_execute($conexion, $stmt_estado_name, array($estado_bool, $admin_id));
                $update_exitoso = (bool)$result;
            } else {
                // MYSQL (Local): estado es tinyint (0/1)
                $stmt = mysqli_prepare($conexion, "UPDATE tbl_administrador SET estado = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ii", $nuevo_estado, $admin_id);
                $update_exitoso = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            if ($update_exitoso) {
                $mensaje = 'Estado actualizado correctamente';
                $tipo_mensaje = 'success';
            }
        }
    }
    
    // CAMBIAR CONTRASEÑA
    if ($accion === 'cambiar_clave') {
        $admin_id = intval($_POST['admin_id'] ?? 0);
        $nueva_clave = $_POST['nueva_clave'] ?? '';
        
        if (!empty($nueva_clave) && $admin_id > 0) {
            // PREPARED STATEMENT - Modo dual
            $clave_md5 = md5($nueva_clave);
            $update_exitoso = false;
            
            if ($is_production) {
                // POSTGRESQL (Producción):
                $stmt_clave_name = 'update_clave_' . uniqid();
                pg_prepare($conexion, $stmt_clave_name, "UPDATE tbl_administrador SET clave = $1 WHERE id = $2");
                $result = pg_execute($conexion, $stmt_clave_name, array($clave_md5, $admin_id));
                $update_exitoso = (bool)$result;
            } else {
                // MYSQL (Local):
                $stmt = mysqli_prepare($conexion, "UPDATE tbl_administrador SET clave = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $clave_md5, $admin_id);
                $update_exitoso = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            if ($update_exitoso) {
                $mensaje = 'Contraseña actualizada correctamente';
                $tipo_mensaje = 'success';
            }
        }
    }
}

// Obtener lista de administradores - Modo dual
$query_admins = "SELECT * FROM tbl_administrador ORDER BY id ASC";
$administradores = [];

if ($is_production) {
    // POSTGRESQL (Producción):
    $resultado_admins = pg_query($conexion, $query_admins);
    while ($admin = pg_fetch_assoc($resultado_admins)) {
        $administradores[] = $admin;
    }
    pg_close($conexion);
} else {
    // MYSQL (Local):
    $resultado_admins = mysqli_query($conexion, $query_admins);
    while ($admin = mysqli_fetch_assoc($resultado_admins)) {
        $administradores[] = $admin;
    }
    mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores - ONPE 2026</title>
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
            margin-bottom: 30px;
        }

        .content-card {
            background: linear-gradient(135deg, #1a1f3a 0%, #2d3561 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            border: 2px solid rgba(220, 20, 60, 0.3);
        }

        .table {
            color: white;
        }

        .table thead {
            background: rgba(220, 20, 60, 0.2);
        }

        .badge-rol {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-accion {
            padding: 5px 15px;
            font-size: 12px;
            border-radius: 5px;
            margin: 2px;
        }

        .form-control, .form-select {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15);
            border-color: #DC143C;
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .form-label {
            color: #e0e0e0;
            font-weight: 600;
        }

        .modal-content {
            background: linear-gradient(135deg, #2d3561 0%, #1a1f3a 100%);
            color: white;
            border: 2px solid rgba(220, 20, 60, 0.3);
        }

        .modal-header {
            border-bottom: 1px solid rgba(220, 20, 60, 0.3);
        }

        .modal-footer {
            border-top: 1px solid rgba(220, 20, 60, 0.3);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-admin">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 style="font-size: 28px; margin: 0;">
                        <i class="fas fa-users-cog me-3"></i>GESTIÓN DE ADMINISTRADORES
                    </h1>
                </div>
                <div class="col-md-4 text-end">
                    <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
            <i class="fas fa-info-circle me-2"></i><?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Formulario de Nuevo Administrador -->
        <div class="content-card">
            <h4 class="mb-4">
                <i class="fas fa-user-plus me-2"></i>Crear Nuevo Administrador
            </h4>

            <form method="POST" action="">
                <input type="hidden" name="accion" value="crear">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario *</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                   placeholder="Ej: admin2" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="clave" name="clave" 
                                   placeholder="Mínimo 6 caracteres" required minlength="6">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="nombres" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" 
                                   placeholder="Nombre del administrador" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol *</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="MODERADOR">Moderador</option>
                                <option value="SUPERADMIN">Super Admin</option>
                                <option value="OBSERVADOR">Observador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="mb-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email (opcional)</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de Administradores -->
        <div class="content-card">
            <h4 class="mb-4">
                <i class="fas fa-list me-2"></i>Administradores Registrados
            </h4>

            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($administradores as $admin): ?>
                        <tr>
                            <td><?php echo $admin['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($admin['usuario']); ?></strong>
                                <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                <span class="badge bg-info">Tú</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($admin['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email'] ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                $color = 'secondary';
                                if ($admin['rol'] == 'SUPERADMIN') $color = 'danger';
                                if ($admin['rol'] == 'MODERADOR') $color = 'primary';
                                if ($admin['rol'] == 'OBSERVADOR') $color = 'warning';
                                ?>
                                <span class="badge badge-rol bg-<?php echo $color; ?>">
                                    <?php echo $admin['rol']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($admin['estado'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ($admin['ultimo_acceso']) {
                                    echo date('d/m/Y H:i', strtotime($admin['ultimo_acceso']));
                                } else {
                                    echo '<span class="text-muted">Nunca</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                <!-- Cambiar Estado -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                    <input type="hidden" name="nuevo_estado" value="<?php echo $admin['estado'] == 1 ? 0 : 1; ?>">
                                    <button type="submit" class="btn btn-accion btn-sm btn-<?php echo $admin['estado'] == 1 ? 'warning' : 'success'; ?>" 
                                            onclick="return confirm('¿Cambiar estado?')">
                                        <i class="fas fa-<?php echo $admin['estado'] == 1 ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                </form>

                                <!-- Cambiar Contraseña -->
                                <button class="btn btn-accion btn-sm btn-info" 
                                        onclick="cambiarClave(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['usuario']); ?>')">
                                    <i class="fas fa-key"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Cambiar Contraseña -->
    <div class="modal fade" id="modalCambiarClave" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="accion" value="cambiar_clave">
                    <input type="hidden" name="admin_id" id="modal_admin_id">
                    <div class="modal-body">
                        <p>Usuario: <strong id="modal_usuario"></strong></p>
                        <div class="mb-3">
                            <label for="nueva_clave" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" 
                                   required minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Nueva Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cambiarClave(adminId, usuario) {
            document.getElementById('modal_admin_id').value = adminId;
            document.getElementById('modal_usuario').textContent = usuario;
            const modal = new bootstrap.Modal(document.getElementById('modalCambiarClave'));
            modal.show();
        }
    </script>
</body>
</html>
