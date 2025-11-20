<?php
/**
 * SISTEMA ELECTORAL PERÚ 2026
 * Login de Administradores
 * PROTEGIDO CONTRA SQL INJECTION Y FUERZA BRUTA
 */

session_start();

// Configuración reCAPTCHA
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'); // Clave de prueba de Google
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'); // Secreto de prueba

// Si ya está logueado como admin, redirigir al dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Control de intentos fallidos (protección fuerza bruta)
if (!isset($_SESSION['login_intentos'])) {
    $_SESSION['login_intentos'] = 0;
    $_SESSION['login_bloqueado_hasta'] = 0;
}

// Procesar login
$error = '';
$mostrar_captcha = true; // CAPTCHA SIEMPRE VISIBLE desde el inicio

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include '../conexion.php';
    
    // Verificar si está bloqueado temporalmente
    if (time() < $_SESSION['login_bloqueado_hasta']) {
        $segundos_restantes = $_SESSION['login_bloqueado_hasta'] - time();
        $error = "Demasiados intentos fallidos. Espera $segundos_restantes segundos.";
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $clave = $_POST['clave'] ?? '';
        
        if (empty($usuario) || empty($clave)) {
            $error = 'Por favor complete todos los campos';
            $_SESSION['login_intentos']++;
        } else {
            // Verificar reCAPTCHA SIEMPRE
            $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
            
            // Detectar si estamos en localhost (desarrollo)
            $es_localhost = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');
            
            if (!$es_localhost) {
                // Solo validar CAPTCHA en producción
                if (empty($recaptcha_response)) {
                    $error = 'Por favor complete el CAPTCHA de seguridad';
                    $_SESSION['login_intentos']++;
                } else {
                    // Verificar CAPTCHA con Google
                    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
                    $response = file_get_contents($verify_url . '?secret=' . RECAPTCHA_SECRET_KEY . '&response=' . $recaptcha_response);
                    $response_data = json_decode($response);
                    
                    if (!$response_data->success) {
                        $error = 'CAPTCHA inválido. Inténtalo de nuevo.';
                        $_SESSION['login_intentos']++;
                    } else {
                        // CAPTCHA válido - continuar con login
                        $this_captcha_valid = true;
                    }
                }
            } else {
                // En localhost, omitir validación de CAPTCHA para desarrollo
                $this_captcha_valid = true;
            }
            
            // Si CAPTCHA es válido (o estamos en localhost), procesar login
            if (isset($this_captcha_valid) && $this_captcha_valid) {
                // PREPARED STATEMENT para prevenir SQL Injection
                // MYSQL (comentado):
                // $stmt = mysqli_prepare($conexion, "SELECT id, usuario, nombres, rol FROM tbl_administrador WHERE usuario = ? AND clave = MD5(?) AND estado = 1 LIMIT 1");
                // mysqli_stmt_bind_param($stmt, "ss", $usuario, $clave);
                // mysqli_stmt_execute($stmt);
                // $resultado = mysqli_stmt_get_result($stmt);
                
                // POSTGRESQL (Supabase):
                $stmt_name = 'login_admin_' . uniqid();
                $query = "SELECT id, usuario, nombres, rol FROM tbl_administrador WHERE usuario = $1 AND clave = MD5($2) AND estado = true LIMIT 1";
                pg_prepare($conexion, $stmt_name, $query);
                $resultado = pg_execute($conexion, $stmt_name, array($usuario, $clave));
                
                // MYSQL: if ($resultado && mysqli_num_rows($resultado) === 1)
                if ($resultado && pg_num_rows($resultado) === 1) {
                    $admin = pg_fetch_assoc($resultado);
                    
                    // Login exitoso - resetear intentos
                    $_SESSION['login_intentos'] = 0;
                    $_SESSION['login_bloqueado_hasta'] = 0;
                    
                    // Establecer sesión de administrador
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_usuario'] = $admin['usuario'];
                    $_SESSION['admin_nombres'] = $admin['nombres'];
                    $_SESSION['admin_rol'] = $admin['rol'];
                    $_SESSION['admin_login_time'] = time();
                    
                    // Actualizar último acceso con prepared statement
                    // MYSQL (comentado):
                    // $stmt_update = mysqli_prepare($conexion, "UPDATE tbl_administrador SET ultimo_acceso = NOW() WHERE id = ?");
                    // mysqli_stmt_bind_param($stmt_update, "i", $admin['id']);
                    // mysqli_stmt_execute($stmt_update);
                    // mysqli_stmt_close($stmt_update);
                    
                    // POSTGRESQL (Supabase):
                    $stmt_update_name = 'update_acceso_' . uniqid();
                    pg_prepare($conexion, $stmt_update_name, "UPDATE tbl_administrador SET ultimo_acceso = NOW() WHERE id = $1");
                    pg_execute($conexion, $stmt_update_name, array($admin['id']));
                    
                    pg_close($conexion);
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = 'Usuario o contraseña incorrectos';
                    $_SESSION['login_intentos']++;
                    
                    // Bloqueo temporal después de 5 intentos fallidos
                    if ($_SESSION['login_intentos'] >= 5) {
                        $_SESSION['login_bloqueado_hasta'] = time() + 300; // 5 minutos
                        $error = 'Demasiados intentos fallidos. Bloqueado por 5 minutos.';
                    }
                }
            }
        }
        
        // MYSQL: mysqli_close($conexion);
        pg_close($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador - ONPE 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            overflow: hidden;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .admin-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .admin-icon i {
            font-size: 50px;
            color: #DC143C;
        }
        
        .admin-body {
            padding: 40px 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            height: 50px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0 20px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: #DC143C;
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            border: none;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(220, 20, 60, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .btn-volver {
            color: #6c757d;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 20px;
            transition: color 0.3s;
        }
        
        .btn-volver:hover {
            color: #DC143C;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="admin-card">
                <div class="admin-header">
                    <div class="admin-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2 style="font-size: 24px; font-weight: 700; margin: 0;">PANEL ADMINISTRATIVO</h2>
                    <p style="font-size: 14px; opacity: 0.9; margin: 5px 0 0 0;">Sistema Electoral Perú 2026</p>
                </div>
                
                <div class="admin-body">
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">
                                <i class="fas fa-user me-2"></i>Usuario
                            </label>
                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                   placeholder="Ingrese su usuario" required autofocus autocomplete="username">
                        </div>
                        
                        <div class="mb-3">
                            <label for="clave" class="form-label">
                                <i class="fas fa-lock me-2"></i>Contraseña
                            </label>
                            <input type="password" class="form-control" id="clave" name="clave" 
                                   placeholder="Ingrese su contraseña" required autocomplete="current-password">
                        </div>
                        
                        <?php if ($mostrar_captcha): ?>
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>" style="transform:scale(0.9);transform-origin:0 0;"></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['login_intentos'] > 0 && $_SESSION['login_intentos'] < 5): ?>
                        <div class="alert alert-warning py-2">
                            <small>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Intentos fallidos: <?php echo $_SESSION['login_intentos']; ?>/5
                            </small>
                        </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>ACCEDER AL PANEL
                        </button>
                    </form>
                    
                    <a href="../index.php" class="btn-volver">
                        <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                    </a>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-white-50">
                    <i class="fas fa-shield-alt me-1"></i>
                    Acceso restringido solo para personal autorizado
                </small>
            </div>
        </div>
    </div>

    <!-- Script de reCAPTCHA - SIEMPRE CARGADO -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
