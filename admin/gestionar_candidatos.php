<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Gestión de Candidatos - Panel Administrativo
 */

session_start();
include '../conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_rol'] != 'SUPERADMIN') {
    header("Location: index.php");
    exit();
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion == 'crear') {
        $partido_id = intval($_POST['partido_id']);
        $cargo = limpiar_dato($_POST['cargo']);
        $nombres = limpiar_dato($_POST['nombres']);
        $apellido_paterno = limpiar_dato($_POST['apellido_paterno']);
        $apellido_materno = limpiar_dato($_POST['apellido_materno']);
        $profesion = limpiar_dato($_POST['profesion']);
        $foto_url = "assets/img/candidatos/" . strtolower(str_replace(' ', '_', $nombres)) . ".jpg";
        
        if ($is_production) {
            $query = "INSERT INTO tbl_candidato (partido_id, cargo, nombres, apellido_paterno, apellido_materno, profesion, foto_url) 
                      VALUES ($1, $2, $3, $4, $5, $6, $7)";
            $result = pg_query_params($conexion, $query, array($partido_id, $cargo, $nombres, $apellido_paterno, $apellido_materno, $profesion, $foto_url));
        } else {
            $query = "INSERT INTO tbl_candidato (partido_id, cargo, nombres, apellido_paterno, apellido_materno, profesion, foto_url) 
                      VALUES ($partido_id, '$cargo', '$nombres', '$apellido_paterno', '$apellido_materno', '$profesion', '$foto_url')";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Candidato creado exitosamente" : "Error al crear candidato";
        header("Location: gestionar_candidatos.php");
        exit();
    }
    
    if ($accion == 'editar') {
        $id = intval($_POST['id']);
        $partido_id = intval($_POST['partido_id']);
        $cargo = limpiar_dato($_POST['cargo']);
        $nombres = limpiar_dato($_POST['nombres']);
        $apellido_paterno = limpiar_dato($_POST['apellido_paterno']);
        $apellido_materno = limpiar_dato($_POST['apellido_materno']);
        $profesion = limpiar_dato($_POST['profesion']);
        
        if ($is_production) {
            $query = "UPDATE tbl_candidato SET partido_id = $1, cargo = $2, nombres = $3, apellido_paterno = $4, 
                      apellido_materno = $5, profesion = $6 WHERE id = $7";
            $result = pg_query_params($conexion, $query, array($partido_id, $cargo, $nombres, $apellido_paterno, $apellido_materno, $profesion, $id));
        } else {
            $query = "UPDATE tbl_candidato SET partido_id = $partido_id, cargo = '$cargo', nombres = '$nombres', 
                      apellido_paterno = '$apellido_paterno', apellido_materno = '$apellido_materno', profesion = '$profesion' WHERE id = $id";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Candidato actualizado" : "Error al actualizar";
        header("Location: gestionar_candidatos.php");
        exit();
    }
    
    if ($accion == 'eliminar') {
        $id = intval($_POST['id']);
        
        if ($is_production) {
            $query = "DELETE FROM tbl_candidato WHERE id = $1";
            $result = pg_query_params($conexion, $query, array($id));
        } else {
            $query = "DELETE FROM tbl_candidato WHERE id = $id";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Candidato eliminado" : "Error al eliminar";
        header("Location: gestionar_candidatos.php");
        exit();
    }
}

// Obtener candidatos con información del partido
if ($is_production) {
    $query = "SELECT c.*, p.siglas as partido_siglas, p.nombre_completo as partido_nombre 
              FROM tbl_candidato c 
              INNER JOIN tbl_partido p ON c.partido_id = p.id 
              ORDER BY p.siglas, c.cargo";
    $resultado = pg_query($conexion, $query);
    $candidatos = [];
    while ($fila = pg_fetch_assoc($resultado)) {
        $candidatos[] = $fila;
    }
    
    // Obtener partidos para el select
    $query_partidos = "SELECT id, siglas, nombre_completo FROM tbl_partido ORDER BY siglas";
    $resultado_partidos = pg_query($conexion, $query_partidos);
    $partidos = [];
    while ($fila = pg_fetch_assoc($resultado_partidos)) {
        $partidos[] = $fila;
    }
} else {
    $query = "SELECT c.*, p.siglas as partido_siglas, p.nombre_completo as partido_nombre 
              FROM tbl_candidato c 
              INNER JOIN tbl_partido p ON c.partido_id = p.id 
              ORDER BY p.siglas, c.cargo";
    $resultado = mysqli_query($conexion, $query);
    $candidatos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $candidatos[] = $fila;
    }
    
    // Obtener partidos
    $query_partidos = "SELECT id, siglas, nombre_completo FROM tbl_partido ORDER BY siglas";
    $resultado_partidos = mysqli_query($conexion, $query_partidos);
    $partidos = [];
    while ($fila = mysqli_fetch_assoc($resultado_partidos)) {
        $partidos[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Candidatos - Admin</title>
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
                <a href="gestionar_candidatos.php" class="text-white text-decoration-none d-block mb-2 fw-bold">
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
                <h2><i class="fas fa-users text-primary"></i> Gestión de Candidatos</h2>
                <hr>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-info alert-dismissible">
                        <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nuevo Candidato
                </button>

                <!-- Tabla de Candidatos -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Partido</th>
                                <th>Cargo</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Profesión</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidatos as $candidato): ?>
                            <tr>
                                <td><?php echo $candidato['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($candidato['partido_siglas']); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $badge_class = $candidato['cargo'] == 'PRESIDENTE' ? 'bg-danger' : 
                                                  ($candidato['cargo'] == 'VICEPRESIDENTE_1' ? 'bg-warning' : 'bg-info');
                                    echo '<span class="badge ' . $badge_class . '">' . $candidato['cargo'] . '</span>';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($candidato['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($candidato['apellido_paterno'] . ' ' . $candidato['apellido_materno']); ?></td>
                                <td><?php echo htmlspecialchars($candidato['profesion']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='editarCandidato(<?php echo json_encode($candidato); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $candidato['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar candidato?')">
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Candidato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Partido Político *</label>
                                <select name="partido_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($partidos as $p): ?>
                                        <option value="<?php echo $p['id']; ?>">
                                            <?php echo htmlspecialchars($p['siglas'] . ' - ' . $p['nombre_completo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Cargo *</label>
                                <select name="cargo" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="PRESIDENTE">Presidente</option>
                                    <option value="VICEPRESIDENTE_1">Vicepresidente 1ro</option>
                                    <option value="VICEPRESIDENTE_2">Vicepresidente 2do</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Nombres *</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Apellido Paterno *</label>
                                <input type="text" name="apellido_paterno" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellido Materno *</label>
                                <input type="text" name="apellido_materno" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Profesión *</label>
                            <input type="text" name="profesion" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear Candidato</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Candidato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Partido Político *</label>
                                <select name="partido_id" id="edit_partido" class="form-select" required>
                                    <?php foreach ($partidos as $p): ?>
                                        <option value="<?php echo $p['id']; ?>">
                                            <?php echo htmlspecialchars($p['siglas'] . ' - ' . $p['nombre_completo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Cargo *</label>
                                <select name="cargo" id="edit_cargo" class="form-select" required>
                                    <option value="PRESIDENTE">Presidente</option>
                                    <option value="VICEPRESIDENTE_1">Vicepresidente 1ro</option>
                                    <option value="VICEPRESIDENTE_2">Vicepresidente 2do</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Nombres *</label>
                            <input type="text" name="nombres" id="edit_nombres" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Apellido Paterno *</label>
                                <input type="text" name="apellido_paterno" id="edit_paterno" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellido Materno *</label>
                                <input type="text" name="apellido_materno" id="edit_materno" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Profesión *</label>
                            <input type="text" name="profesion" id="edit_profesion" class="form-control" required>
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
        function editarCandidato(candidato) {
            document.getElementById('edit_id').value = candidato.id;
            document.getElementById('edit_partido').value = candidato.partido_id;
            document.getElementById('edit_cargo').value = candidato.cargo;
            document.getElementById('edit_nombres').value = candidato.nombres;
            document.getElementById('edit_paterno').value = candidato.apellido_paterno;
            document.getElementById('edit_materno').value = candidato.apellido_materno;
            document.getElementById('edit_profesion').value = candidato.profesion;
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }
    </script>
</body>
</html>
