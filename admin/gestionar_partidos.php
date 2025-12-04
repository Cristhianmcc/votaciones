<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Gestión de Partidos Políticos - Panel Administrativo
 * Última actualización: 2025-12-04
 */

session_start();
include '../conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_rol'] != 'SUPERADMIN') {
    header("Location: index.php");
    exit();
}

// Procesar acciones (crear, editar, eliminar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion == 'crear') {
        $siglas = limpiar_dato($_POST['siglas']);
        $nombre_completo = limpiar_dato($_POST['nombre_completo']);
        $color_primario = limpiar_dato($_POST['color_primario']);
        $color_secundario = limpiar_dato($_POST['color_secundario'] ?? '#FFFFFF');
        
        // Procesar logo subido
        $logo_url = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $tipos_permitidos = ['jpg', 'jpeg', 'png'];
            
            if (in_array($extension, $tipos_permitidos) && $_FILES['logo']['size'] <= 5000000) {
                $nombre_archivo = strtoupper($siglas) . '.' . $extension;
                
                // Usar función que maneja local y Supabase automáticamente
                $logo_url = subir_archivo($_FILES['logo'], 'partidos', $nombre_archivo);
            }
        }
        
        // Si no hay logo subido, usar placeholder
        if (!$logo_url) {
            $logo_url = "assets/img/partidos/" . strtolower($siglas) . ".png";
        }
        
        if ($is_production) {
            $query = "INSERT INTO tbl_partido (siglas, nombre_completo, nombre_corto, color_primario, color_secundario, logo_url, estado) 
                      VALUES ($1, $2, $3, $4, $5, $6, TRUE)";
            $result = pg_query_params($conexion, $query, array($siglas, $nombre_completo, $siglas, $color_primario, $color_secundario, $logo_url));
        } else {
            $query = "INSERT INTO tbl_partido (siglas, nombre_completo, nombre_corto, color_primario, color_secundario, logo_url, estado) 
                      VALUES ('$siglas', '$nombre_completo', '$siglas', '$color_primario', '$color_secundario', '$logo_url', 1)";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Partido creado exitosamente" : "Error al crear partido";
        header("Location: gestionar_partidos.php");
        exit();
    }
    
    if ($accion == 'editar') {
        $id = intval($_POST['id']);
        $siglas = limpiar_dato($_POST['siglas']);
        $nombre_completo = limpiar_dato($_POST['nombre_completo']);
        $color_primario = limpiar_dato($_POST['color_primario']);
        $color_secundario = limpiar_dato($_POST['color_secundario']);
        
        // Procesar logo subido (opcional en edición)
        $logo_url = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $tipos_permitidos = ['jpg', 'jpeg', 'png'];
            
            if (in_array($extension, $tipos_permitidos) && $_FILES['logo']['size'] <= 5000000) {
                $nombre_archivo = strtoupper($siglas) . '.' . $extension;
                
                // Usar función que maneja local y Supabase automáticamente
                $logo_url = subir_archivo($_FILES['logo'], 'partidos', $nombre_archivo);
            }
        }
        
        if ($is_production) {
            if ($logo_url) {
                $query = "UPDATE tbl_partido SET siglas = $1, nombre_completo = $2, color_primario = $3, color_secundario = $4, logo_url = $5 WHERE id = $6";
                $result = pg_query_params($conexion, $query, array($siglas, $nombre_completo, $color_primario, $color_secundario, $logo_url, $id));
            } else {
                $query = "UPDATE tbl_partido SET siglas = $1, nombre_completo = $2, color_primario = $3, color_secundario = $4 WHERE id = $5";
                $result = pg_query_params($conexion, $query, array($siglas, $nombre_completo, $color_primario, $color_secundario, $id));
            }
        } else {
            if ($logo_url) {
                $query = "UPDATE tbl_partido SET siglas = '$siglas', nombre_completo = '$nombre_completo', 
                          color_primario = '$color_primario', color_secundario = '$color_secundario', logo_url = '$logo_url' WHERE id = $id";
            } else {
                $query = "UPDATE tbl_partido SET siglas = '$siglas', nombre_completo = '$nombre_completo', 
                          color_primario = '$color_primario', color_secundario = '$color_secundario' WHERE id = $id";
            }
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Partido actualizado" : "Error al actualizar";
        header("Location: gestionar_partidos.php");
        exit();
    }
    
    if ($accion == 'eliminar') {
        $id = intval($_POST['id']);
        
        if ($is_production) {
            $query = "DELETE FROM tbl_partido WHERE id = $1";
            $result = pg_query_params($conexion, $query, array($id));
        } else {
            $query = "DELETE FROM tbl_partido WHERE id = $id";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Partido eliminado" : "Error al eliminar";
        header("Location: gestionar_partidos.php");
        exit();
    }
}

// Obtener lista de partidos
if ($is_production) {
    $query = "SELECT * FROM tbl_partido ORDER BY siglas";
    $resultado = pg_query($conexion, $query);
    $partidos = [];
    while ($fila = pg_fetch_assoc($resultado)) {
        $partidos[] = $fila;
    }
} else {
    $query = "SELECT * FROM tbl_partido ORDER BY siglas";
    $resultado = mysqli_query($conexion, $query);
    $partidos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $partidos[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Partidos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .admin-container { margin-top: 30px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-flag"></i> Gestión de Partidos Políticos
            </span>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </nav>

    <div class="container admin-container">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-list"></i> Partidos Políticos Registrados</h4>
            </div>
            <div class="card-body">
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nuevo Partido
                </button>

                <!-- Tabla de Partidos -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Siglas</th>
                                <th>Nombre Completo</th>
                                <th>Color</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partidos as $partido): ?>
                            <tr>
                                <td><?php echo $partido['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($partido['siglas']); ?></strong></td>
                                <td><?php echo htmlspecialchars($partido['nombre_completo']); ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $partido['color_primario']; ?>">
                                        <?php echo $partido['color_primario']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $activo = $is_production ? ($partido['estado'] == 't') : ($partido['estado'] == 1);
                                    echo $activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'; 
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='editarPartido(<?php echo json_encode($partido); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $partido['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar partido?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Partido Político</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        <div class="mb-3">
                            <label>Siglas *</label>
                            <input type="text" name="siglas" class="form-control" required maxlength="20">
                        </div>
                        <div class="mb-3">
                            <label>Nombre Completo *</label>
                            <input type="text" name="nombre_completo" class="form-control" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label>Color Primario *</label>
                            <input type="color" name="color_primario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Color Secundario</label>
                            <input type="color" name="color_secundario" class="form-control" value="#FFFFFF">
                        </div>
                        <div class="mb-3">
                            <label>Logo del Partido</label>
                            <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png" onchange="previewLogo(this, 'preview_crear')">
                            <small class="text-muted">Formatos: JPG, JPEG, PNG (máx. 5MB)</small>
                            <div class="mt-2 text-center">
                                <img id="preview_crear" src="" alt="Vista previa" style="display:none; max-width:200px; max-height:200px; border:1px solid #ddd; padding:5px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear Partido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Partido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label>Siglas *</label>
                            <input type="text" name="siglas" id="edit_siglas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nombre Completo *</label>
                            <input type="text" name="nombre_completo" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Color Primario *</label>
                            <input type="color" name="color_primario" id="edit_color" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Color Secundario</label>
                            <input type="color" name="color_secundario" id="edit_color2" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Cambiar Logo (opcional)</label>
                            <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png" onchange="previewLogo(this, 'preview_editar')">
                            <small class="text-muted">Formatos: JPG, JPEG, PNG (máx. 5MB). Si no selecciona archivo, se mantiene el logo actual.</small>
                            <div class="mt-2 text-center">
                                <img id="preview_editar" src="" alt="Vista previa" style="display:none; max-width:200px; max-height:200px; border:1px solid #ddd; padding:5px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarPartido(partido) {
            document.getElementById('edit_id').value = partido.id;
            document.getElementById('edit_siglas').value = partido.siglas;
            document.getElementById('edit_nombre').value = partido.nombre_completo;
            document.getElementById('edit_color').value = partido.color_primario;
            document.getElementById('edit_color2').value = partido.color_secundario || '#FFFFFF';
            
            // Limpiar preview anterior
            document.getElementById('preview_editar').style.display = 'none';
            document.getElementById('preview_editar').src = '';
            
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }

        function previewLogo(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. Máximo 5MB.');
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Formato no válido. Use JPG, JPEG o PNG.');
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
