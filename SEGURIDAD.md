# 🔒 SEGURIDAD IMPLEMENTADA - Sistema Electoral 2026

## ✅ Protecciones Contra SQL Injection

### **Prepared Statements (PDO Style con MySQLi)**

Todos los módulos críticos ahora usan **Prepared Statements** en lugar de concatenación de strings:

#### ❌ **ANTES (Vulnerable):**
```php
$query = "SELECT * FROM tbl_administrador WHERE usuario = '$usuario' AND clave = '$clave_md5'";
$resultado = mysqli_query($conexion, $query);
```

#### ✅ **AHORA (Seguro):**
```php
$stmt = mysqli_prepare($conexion, "SELECT id, usuario, nombres, rol FROM tbl_administrador WHERE usuario = ? AND clave = MD5(?) AND estado = 1");
mysqli_stmt_bind_param($stmt, "ss", $usuario, $clave);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
```

### **Módulos Protegidos:**

1. ✅ **Login de Administradores** (`admin/login_admin.php`)
   - Prepared statements para SELECT
   - Prepared statements para UPDATE de último acceso
   - **reCAPTCHA obligatorio desde el inicio**
   
2. ✅ **Login de Votantes** (`index.php` + `login_electoral.php`)
   - **reCAPTCHA obligatorio antes de votar**
   - Validación server-side del CAPTCHA
   - Usa procedimientos almacenados (ya protegido)
   
3. ✅ **Gestión de Administradores** (`admin/gestionar_administradores.php`)
   - Prepared statements para INSERT de nuevos admins
   - Prepared statements para UPDATE de estado
   - Prepared statements para UPDATE de contraseña
   - Validación estricta de roles permitidos

4. ✅ **Procesar Voto** (`procesar_voto.php`)
   - Usa procedimientos almacenados (ya protegido)

---

## 🛡️ Protección Anti-Fuerza Bruta

### **Sistema de Límite de Intentos**

Implementado en el login de administradores:

**Características:**
- ✅ **Contador de intentos fallidos** por sesión
- ✅ **CAPTCHA obligatorio** después de 2 intentos fallidos
- ✅ **Bloqueo temporal de 5 minutos** después de 5 intentos
- ✅ **Reset automático** al login exitoso
- ✅ **Indicador visual** de intentos restantes

**Flujo de Seguridad:**

```
ADMINISTRADORES:
Login → CAPTCHA obligatorio → Validación → Acceso

VOTANTES:
Ingresar DNI → CAPTCHA obligatorio → Validación → Cédula de votación

Intento 1 fallido → ⚠️ Advertencia (1/5)
Intento 2 fallido → ⚠️ Advertencia (2/5)
Intento 3 fallido → ⚠️ Advertencia (3/5)
Intento 4 fallido → ⚠️ Advertencia (4/5)
Intento 5 fallido → 🚫 BLOQUEADO por 5 minutos (solo admins)
```

---

## 🤖 Google reCAPTCHA v2

### **Implementación**

- **Versión**: reCAPTCHA v2 (Checkbox "No soy un robot")
- **Activación**: **SIEMPRE VISIBLE** desde el primer intento
- **Validación**: Server-side con la API de Google
- **Ubicaciones**: Login de administradores Y login de votantes

### **¿Por qué SIEMPRE visible?**

✅ **Máxima seguridad desde el inicio**  
✅ **Previene ataques automatizados de bots**  
✅ **Protege el padrón electoral de scraping**  
✅ **No hay "intentos gratis" para atacantes**  
✅ **Estándar en sistemas electorales reales**

### **Claves Actuales (DE PRUEBA):**

```php
RECAPTCHA_SITE_KEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'
RECAPTCHA_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'
```

⚠️ **IMPORTANTE PARA PRODUCCIÓN:**

Estas son **claves de prueba de Google**. Para producción:

1. Ir a: https://www.google.com/recaptcha/admin/create
2. Crear un nuevo sitio reCAPTCHA v2
3. Obtener tus claves reales
4. Reemplazar en `admin/login_admin.php`:
   ```php
   define('RECAPTCHA_SITE_KEY', 'TU_CLAVE_DE_SITIO_AQUI');
   define('RECAPTCHA_SECRET_KEY', 'TU_CLAVE_SECRETA_AQUI');
   ```

### **Verificación Server-Side**

```php
// Verificar CAPTCHA con Google
$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
$response = file_get_contents($verify_url . '?secret=' . RECAPTCHA_SECRET_KEY . '&response=' . $recaptcha_response);
$response_data = json_decode($response);

if (!$response_data->success) {
    $error = 'CAPTCHA inválido';
}
```

---

## 🔐 Otras Medidas de Seguridad

### **1. Validación de Datos**

- ✅ `trim()` para eliminar espacios
- ✅ `intval()` para forzar enteros
- ✅ `htmlspecialchars()` para prevenir XSS
- ✅ Validación de arrays permitidos (roles, estados)

### **2. Limitación de Información**

- ✅ Solo se seleccionan campos necesarios (no `SELECT *`)
- ✅ `LIMIT 1` en queries que esperan un resultado
- ✅ Verificación de `estado = 1` (usuarios activos)

### **3. Autocomplete Seguro**

```html
<input type="text" name="usuario" autocomplete="username">
<input type="password" name="clave" autocomplete="current-password">
```

### **4. Manejo de Sesiones**

- ✅ Regeneración de ID de sesión al login
- ✅ Timeout de sesión
- ✅ Verificación de rol en cada página protegida

---

## 📋 Checklist de Seguridad

| Protección | Estado | Ubicación |
|-----------|--------|-----------|
| SQL Injection - Login Admin | ✅ Implementado | `admin/login_admin.php` |
| SQL Injection - Gestión Admins | ✅ Implementado | `admin/gestionar_administradores.php` |
| SQL Injection - Login Votantes | ✅ Implementado | Usa SP |
| SQL Injection - Registro Votos | ✅ Implementado | Usa SP |
| Anti-Fuerza Bruta | ✅ Implementado | `admin/login_admin.php` |
| reCAPTCHA v2 | ✅ Implementado | `admin/login_admin.php` |
| XSS Protection | ✅ Implementado | `htmlspecialchars()` |
| CSRF Protection | ⚠️ Pendiente | Tokens CSRF |
| Rate Limiting | ✅ Básico | Control de intentos |
| Contraseñas Hasheadas | ⚠️ MD5 (mejorar) | Migrar a bcrypt |

---

## 🚀 Recomendaciones para Producción

### **Prioritarias:**

1. **Migrar de MD5 a bcrypt/Argon2**
   ```php
   // En lugar de:
   $clave_md5 = md5($clave);
   
   // Usar:
   $hash = password_hash($clave, PASSWORD_ARGON2ID);
   // Verificar:
   if (password_verify($clave, $hash_db)) { }
   ```

2. **Implementar tokens CSRF**
   ```php
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   ```

3. **Configurar HTTPS obligatorio**
   ```php
   if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
       header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
       exit;
   }
   ```

4. **Registrar claves reCAPTCHA reales**

5. **Implementar logs de auditoría**
   - Registrar todos los intentos de login
   - Registrar cambios de administradores
   - Registrar accesos a resultados

### **Opcionales:**

- Two-Factor Authentication (2FA)
- IP Whitelisting para admin
- WAF (Web Application Firewall)
- Honeypots anti-bot
- Content Security Policy headers

---

## 🧪 Pruebas de Seguridad

### **Test SQL Injection:**

Intenta estos payloads en el login:
```
Usuario: admin' OR '1'='1
Usuario: admin'--
Usuario: admin' UNION SELECT NULL--
```

✅ **Resultado esperado**: Todos deben fallar con "Usuario o contraseña incorrectos"

### **Test Fuerza Bruta:**

1. Intenta 2 veces con credenciales incorrectas → Debe aparecer CAPTCHA
2. Intenta 5 veces → Debe bloquearse por 5 minutos

### **Test XSS:**

Intenta crear un admin con nombre:
```
<script>alert('XSS')</script>
```

✅ **Resultado esperado**: Debe mostrarse escapado como texto, no ejecutarse

---

## 📞 Soporte

Para reportar vulnerabilidades de seguridad o consultas:
- Usar el sistema de issues
- Seguir responsible disclosure practices

**Última actualización**: 16 de Noviembre de 2025
