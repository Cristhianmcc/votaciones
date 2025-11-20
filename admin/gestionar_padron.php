<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Gestión de Padrón Electoral - Panel Administrativo
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
        $dni = limpiar_dato($_POST['dni']);
        $nombres = limpiar_dato($_POST['nombres']);
        $apellido_paterno = limpiar_dato($_POST['apellido_paterno']);
        $apellido_materno = limpiar_dato($_POST['apellido_materno']);
        $departamento = limpiar_dato($_POST['departamento']);
        
        if ($is_production) {
            // Verificar si ya existe
            $check = pg_query_params($conexion, "SELECT dni FROM tbl_ciudadano WHERE dni = $1", array($dni));
            if (pg_num_rows($check) > 0) {
                $_SESSION['mensaje'] = "DNI ya existe en el padrón";
                header("Location: gestionar_padron.php");
                exit();
            }
            
            $query = "INSERT INTO tbl_ciudadano (dni, nombres, apellido_paterno, apellido_materno, departamento, estado) 
                      VALUES ($1, $2, $3, $4, $5, TRUE)";
            $result = pg_query_params($conexion, $query, array($dni, $nombres, $apellido_paterno, $apellido_materno, $departamento));
        } else {
            // Verificar si ya existe
            $check = mysqli_query($conexion, "SELECT dni FROM tbl_ciudadano WHERE dni = '$dni'");
            if (mysqli_num_rows($check) > 0) {
                $_SESSION['mensaje'] = "DNI ya existe en el padrón";
                header("Location: gestionar_padron.php");
                exit();
            }
            
            $query = "INSERT INTO tbl_ciudadano (dni, nombres, apellido_paterno, apellido_materno, departamento, estado) 
                      VALUES ('$dni', '$nombres', '$apellido_paterno', '$apellido_materno', '$departamento', 1)";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Ciudadano agregado al padrón" : "Error al agregar ciudadano";
        header("Location: gestionar_padron.php");
        exit();
    }
    
    if ($accion == 'editar') {
        $id = intval($_POST['id']);
        $nombres = limpiar_dato($_POST['nombres']);
        $apellido_paterno = limpiar_dato($_POST['apellido_paterno']);
        $apellido_materno = limpiar_dato($_POST['apellido_materno']);
        $departamento = limpiar_dato($_POST['departamento']);
        $estado = isset($_POST['estado']) ? 1 : 0;
        
        if ($is_production) {
            $estado_pg = $estado ? 'TRUE' : 'FALSE';
            $query = "UPDATE tbl_ciudadano SET nombres = $1, apellido_paterno = $2, apellido_materno = $3, 
                      departamento = $4, estado = $estado_pg WHERE id = $5";
            $result = pg_query_params($conexion, $query, array($nombres, $apellido_paterno, $apellido_materno, $departamento, $id));
        } else {
            $query = "UPDATE tbl_ciudadano SET nombres = '$nombres', apellido_paterno = '$apellido_paterno', 
                      apellido_materno = '$apellido_materno', departamento = '$departamento', estado = $estado WHERE id = $id";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Ciudadano actualizado" : "Error al actualizar";
        header("Location: gestionar_padron.php");
        exit();
    }
    
    if ($accion == 'eliminar') {
        $id = intval($_POST['id']);
        
        if ($is_production) {
            $query = "DELETE FROM tbl_ciudadano WHERE id = $1";
            $result = pg_query_params($conexion, $query, array($id));
        } else {
            $query = "DELETE FROM tbl_ciudadano WHERE id = $id";
            $result = mysqli_query($conexion, $query);
        }
        
        $_SESSION['mensaje'] = $result ? "Ciudadano eliminado" : "Error al eliminar";
        header("Location: gestionar_padron.php");
        exit();
    }
    
    if ($accion == 'importar_csv') {
        if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] != 0) {
            $_SESSION['mensaje'] = "Error al subir archivo";
            header("Location: gestionar_padron.php");
            exit();
        }
        
        $archivo = $_FILES['archivo_csv']['tmp_name'];
        $handle = fopen($archivo, 'r');
        $primera_linea = true;
        $importados = 0;
        $errores = 0;
        
        while (($datos = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if ($primera_linea) {
                $primera_linea = false;
                continue; // Saltar encabezados
            }
            
            if (count($datos) < 5) continue;
            
            $dni = limpiar_dato($datos[0]);
            $nombres = limpiar_dato($datos[1]);
            $paterno = limpiar_dato($datos[2]);
            $materno = limpiar_dato($datos[3]);
            $depto = limpiar_dato($datos[4]);
            
            if (strlen($dni) != 8 || !is_numeric($dni)) {
                $errores++;
                continue;
            }
            
            if ($is_production) {
                $check = pg_query_params($conexion, "SELECT dni FROM tbl_ciudadano WHERE dni = $1", array($dni));
                if (pg_num_rows($check) > 0) {
                    $errores++;
                    continue;
                }
                
                $query = "INSERT INTO tbl_ciudadano (dni, nombres, apellido_paterno, apellido_materno, departamento, estado) 
                          VALUES ($1, $2, $3, $4, $5, TRUE)";
                $result = pg_query_params($conexion, $query, array($dni, $nombres, $paterno, $materno, $depto));
            } else {
                $check = mysqli_query($conexion, "SELECT dni FROM tbl_ciudadano WHERE dni = '$dni'");
                if (mysqli_num_rows($check) > 0) {
                    $errores++;
                    continue;
                }
                
                $query = "INSERT INTO tbl_ciudadano (dni, nombres, apellido_paterno, apellido_materno, departamento, estado) 
                          VALUES ('$dni', '$nombres', '$paterno', '$materno', '$depto', 1)";
                $result = mysqli_query($conexion, $query);
            }
            
            if ($result) {
                $importados++;
            } else {
                $errores++;
            }
        }
        
        fclose($handle);
        $_SESSION['mensaje'] = "Importación completada: $importados ciudadanos agregados, $errores errores";
        header("Location: gestionar_padron.php");
        exit();
    }
}

// Obtener lista de ciudadanos
$buscar = isset($_GET['buscar']) ? limpiar_dato($_GET['buscar']) : '';
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$por_pagina = 50;
$offset = ($pagina - 1) * $por_pagina;

if ($is_production) {
    if ($buscar != '') {
        $query = "SELECT * FROM tbl_ciudadano WHERE dni LIKE $1 OR nombres ILIKE $2 OR apellido_paterno ILIKE $2 
                  ORDER BY apellido_paterno, apellido_materno LIMIT $por_pagina OFFSET $offset";
        $resultado = pg_query_params($conexion, $query, array("%$buscar%", "%$buscar%"));
        
        $query_total = "SELECT COUNT(*) as total FROM tbl_ciudadano WHERE dni LIKE $1 OR nombres ILIKE $2 OR apellido_paterno ILIKE $2";
        $res_total = pg_query_params($conexion, $query_total, array("%$buscar%", "%$buscar%"));
        $total_row = pg_fetch_assoc($res_total);
    } else {
        $query = "SELECT * FROM tbl_ciudadano ORDER BY apellido_paterno, apellido_materno LIMIT $por_pagina OFFSET $offset";
        $resultado = pg_query($conexion, $query);
        
        $query_total = "SELECT COUNT(*) as total FROM tbl_ciudadano";
        $res_total = pg_query($conexion, $query_total);
        $total_row = pg_fetch_assoc($res_total);
    }
    
    $ciudadanos = [];
    while ($fila = pg_fetch_assoc($resultado)) {
        $ciudadanos[] = $fila;
    }
    $total_registros = $total_row['total'];
} else {
    if ($buscar != '') {
        $query = "SELECT * FROM tbl_ciudadano WHERE dni LIKE '%$buscar%' OR nombres LIKE '%$buscar%' OR apellido_paterno LIKE '%$buscar%' 
                  ORDER BY apellido_paterno, apellido_materno LIMIT $por_pagina OFFSET $offset";
        $resultado = mysqli_query($conexion, $query);
        
        $query_total = "SELECT COUNT(*) as total FROM tbl_ciudadano WHERE dni LIKE '%$buscar%' OR nombres LIKE '%$buscar%' OR apellido_paterno LIKE '%$buscar%'";
        $res_total = mysqli_query($conexion, $query_total);
        $total_row = mysqli_fetch_assoc($res_total);
    } else {
        $query = "SELECT * FROM tbl_ciudadano ORDER BY apellido_paterno, apellido_materno LIMIT $por_pagina OFFSET $offset";
        $resultado = mysqli_query($conexion, $query);
        
        $query_total = "SELECT COUNT(*) as total FROM tbl_ciudadano";
        $res_total = mysqli_query($conexion, $query_total);
        $total_row = mysqli_fetch_assoc($res_total);
    }
    
    $ciudadanos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $ciudadanos[] = $fila;
    }
    $total_registros = $total_row['total'];
}

$total_paginas = ceil($total_registros / $por_pagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padrón Electoral - Admin</title>
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
                <a href="gestionar_padron.php" class="text-white text-decoration-none d-block mb-2 fw-bold">
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
                <h2><i class="fas fa-address-book text-success"></i> Padrón Electoral</h2>
                <p class="text-muted">Total de ciudadanos habilitados: <strong><?php echo number_format($total_registros); ?></strong></p>
                <hr>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-info alert-dismissible">
                        <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Barra de herramientas -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="fas fa-plus"></i> Nuevo Ciudadano
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalImportar">
                            <i class="fas fa-file-upload"></i> Importar CSV
                        </button>
                    </div>
                    <div class="col-md-8">
                        <form method="GET" class="d-flex">
                            <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por DNI o nombre..." value="<?php echo htmlspecialchars($buscar); ?>">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <?php if ($buscar != ''): ?>
                                <a href="gestionar_padron.php" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Ciudadanos -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>DNI</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Departamento</th>
                                <th>Estado</th>
                                <th>Votó</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ciudadanos as $ciudadano): ?>
                            <tr>
                                <td><strong><?php echo $ciudadano['dni']; ?></strong></td>
                                <td><?php echo htmlspecialchars($ciudadano['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($ciudadano['apellido_paterno'] . ' ' . $ciudadano['apellido_materno']); ?></td>
                                <td><?php echo htmlspecialchars($ciudadano['departamento']); ?></td>
                                <td>
                                    <?php 
                                    $activo = $is_production ? ($ciudadano['estado'] == 't') : ($ciudadano['estado'] == 1);
                                    echo $activo ? '<span class="badge bg-success">Habilitado</span>' : '<span class="badge bg-danger">Deshabilitado</span>'; 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $voto = $is_production ? ($ciudadano['ha_votado'] == 't') : ($ciudadano['ha_votado'] == 1);
                                    echo $voto ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-secondary"></i>'; 
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='editarCiudadano(<?php echo json_encode($ciudadano); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $ciudadano['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar ciudadano del padrón?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo $buscar ? '&buscar=' . urlencode($buscar) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar Ciudadano al Padrón</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        <div class="mb-3">
                            <label>DNI *</label>
                            <input type="text" name="dni" class="form-control" required pattern="[0-9]{8}" maxlength="8" placeholder="8 dígitos">
                        </div>
                        <div class="mb-3">
                            <label>Nombres *</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Apellido Paterno *</label>
                            <input type="text" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Apellido Materno *</label>
                            <input type="text" name="apellido_materno" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Departamento *</label>
                            <select name="departamento" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="LIMA">Lima</option>
                                <option value="AREQUIPA">Arequipa</option>
                                <option value="CUSCO">Cusco</option>
                                <option value="LA LIBERTAD">La Libertad</option>
                                <option value="PIURA">Piura</option>
                                <option value="JUNIN">Junín</option>
                                <option value="LAMBAYEQUE">Lambayeque</option>
                                <option value="CAJAMARCA">Cajamarca</option>
                                <option value="PUNO">Puno</option>
                                <option value="ANCASH">Ancash</option>
                                <option value="ICA">Ica</option>
                                <option value="LORETO">Loreto</option>
                                <option value="HUANUCO">Huánuco</option>
                                <option value="UCAYALI">Ucayali</option>
                                <option value="SAN MARTIN">San Martín</option>
                                <option value="AYACUCHO">Ayacucho</option>
                                <option value="APURIMAC">Apurímac</option>
                                <option value="TACNA">Tacna</option>
                                <option value="HUANCAVELICA">Huancavelica</option>
                                <option value="AMAZONAS">Amazonas</option>
                                <option value="MOQUEGUA">Moquegua</option>
                                <option value="PASCO">Pasco</option>
                                <option value="TUMBES">Tumbes</option>
                                <option value="MADRE DE DIOS">Madre de Dios</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Agregar</button>
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
                        <h5 class="modal-title">Editar Ciudadano</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label>DNI</label>
                            <input type="text" id="edit_dni" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Nombres *</label>
                            <input type="text" name="nombres" id="edit_nombres" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Apellido Paterno *</label>
                            <input type="text" name="apellido_paterno" id="edit_paterno" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Apellido Materno *</label>
                            <input type="text" name="apellido_materno" id="edit_materno" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Departamento *</label>
                            <select name="departamento" id="edit_depto" class="form-select" required>
                                <option value="LIMA">Lima</option>
                                <option value="AREQUIPA">Arequipa</option>
                                <option value="CUSCO">Cusco</option>
                                <!-- Agregar resto de departamentos -->
                            </select>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="estado" id="edit_estado" class="form-check-input" value="1">
                            <label class="form-check-label">Habilitado para votar</label>
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

    <!-- Modal Importar CSV -->
    <div class="modal fade" id="modalImportar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Importar Padrón Electoral desde CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="importar_csv">
                        <div class="alert alert-info">
                            <h6>Formato del archivo CSV:</h6>
                            <p class="mb-0">El archivo debe tener las siguientes columnas (con encabezado):</p>
                            <code>DNI,NOMBRES,APELLIDO_PATERNO,APELLIDO_MATERNO,DEPARTAMENTO</code>
                            <p class="mt-2 mb-0"><strong>Ejemplo:</strong></p>
                            <pre class="mb-0">DNI,NOMBRES,APELLIDO_PATERNO,APELLIDO_MATERNO,DEPARTAMENTO
12345678,JUAN CARLOS,PEREZ,GARCIA,LIMA
87654321,MARIA ELENA,RODRIGUEZ,LOPEZ,AREQUIPA</pre>
                        </div>
                        <div class="mb-3">
                            <label>Seleccionar archivo CSV *</label>
                            <input type="file" name="archivo_csv" class="form-control" accept=".csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Importar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarCiudadano(ciudadano) {
            document.getElementById('edit_id').value = ciudadano.id;
            document.getElementById('edit_dni').value = ciudadano.dni;
            document.getElementById('edit_nombres').value = ciudadano.nombres;
            document.getElementById('edit_paterno').value = ciudadano.apellido_paterno;
            document.getElementById('edit_materno').value = ciudadano.apellido_materno;
            document.getElementById('edit_depto').value = ciudadano.departamento;
            
            // Estado depende de si es PostgreSQL o MySQL
            const estadoActivo = (ciudadano.estado == 't' || ciudadano.estado == 1);
            document.getElementById('edit_estado').checked = estadoActivo;
            
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }
    </script>
</body>
</html>
