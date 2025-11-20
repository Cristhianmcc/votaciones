<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Gestión de Partidos Políticos - Panel Administrativo
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
        
        if ($is_production) {
            $query = "INSERT INTO tbl_partido (siglas, nombre_completo, color_primario, color_secundario, logo_url, estado) 
                      VALUES ($1, $2, $3, $4, $5, TRUE)";
            $logo_url = "assets/img/partidos/" . strtolower($siglas) . ".png";
            $result = pg_query_params($conexion, $query, array($siglas, $nombre_completo, $color_primario, $color_secundario, $logo_url));
        } else {
            $logo_url = "assets/img/partidos/" . strtolower($siglas) . ".png";
            $query = "INSERT INTO tbl_partido (siglas, nombre_completo, color_primario, color_secundario, logo_url, estado) 
                      VALUES ('$siglas', '$nombre_completo', '$color_primario', '$color_secundario', '$logo_url', 1)";
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
        
        if ($is_production) {
            $query = "UPDATE tbl_partido SET siglas = $1, nombre_completo = $2, color_primario = $3, color_secundario = $4 WHERE id = $5";
            $result = pg_query_params($conexion, $query, array($siglas, $nombre_completo, $color_primario, $color_secundario, $id));
        } else {
            $query = "UPDATE tbl_partido SET siglas = '$siglas', nombre_completo = '$nombre_completo', 
                      color_primario = '$color_primario', color_secundario = '$color_secundario' WHERE id = $id";
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
                <a href="gestionar_partidos.php" class="text-white text-decoration-none d-block mb-2 fw-bold">
                    <i class="fas fa-flag"></i> Partidos
                </a>
                <a href="gestionar_candidatos.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-users"></i> Candidatos
                </a>
                <a href="gestionar_padron.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-address-book"></i> Padrón Electoral
                </a>
                <a href="gestionar_votos.php" class="text-white text-decoration-none d-block mb-2">
                    <i class="fas fa-vote-yea"></i> Votos Registrados
                </a>
                <hr>
                <a href="../logout.php" class="text-danger text-decoration-none">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>

            <!-- Contenido -->
            <div class="col-md-10 p-4">
                <h2><i class="fas fa-flag text-danger"></i> Gestión de Partidos Políticos</h2>
                <hr>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-info alert-dismissible">
                        <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Botón Crear -->
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
                <form method="POST">
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
                <form method="POST">
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
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }
    </script>
</body>
</html>
