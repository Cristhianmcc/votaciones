# 🚀 MIGRACIÓN A SUPABASE (PostgreSQL)

## 📋 Resumen de Cambios

Se ha migrado la base de datos de **MySQL (localhost/InfinityFree)** a **PostgreSQL (Supabase)**.

Todo el código MySQL está **COMENTADO** para que puedas volver a usarlo si es necesario.

---

## 🗂️ Archivos Modificados

### 1. **conexion.php** - Conexión a Base de Datos
**Cambios principales:**
- ✅ Cambiado de `mysqli_connect()` a `pg_connect()`
- ✅ Configuración para Supabase con SSL requerido
- ✅ Función `limpiar_dato()` usa `pg_escape_string()` en lugar de `mysqli_real_escape_string()`

**Configuración actual:**
```php
$supabase_host = "db.kvjnvvwbxdlporvwdupy.supabase.co";
$supabase_user = "postgres";
$supabase_password = "kikomoreno1";
$supabase_database = "postgres";
$supabase_port = 5432;
```

---

### 2. **login_electoral.php** - Login de Ciudadanos
**Cambios MySQL → PostgreSQL:**

| MySQL | PostgreSQL |
|-------|-----------|
| `CALL sp_validar_ciudadano('$dni')` | `SELECT * FROM sp_validar_ciudadano('$dni')` |
| `mysqli_query()` | `pg_query()` |
| `mysqli_fetch_assoc()` | `pg_fetch_assoc()` |
| `$ciudadano['estado'] != 1` | `$ciudadano['estado'] != 't'` (booleano) |
| `$ciudadano['ha_votado'] == 1` | `$ciudadano['ha_votado'] == 't'` |
| `mysqli_close()` | `pg_close()` |

---

### 3. **cedula_votacion.php** - Cédula Electoral
**Cambios MySQL → PostgreSQL:**

| MySQL | PostgreSQL |
|-------|-----------|
| `CALL sp_obtener_cedula()` | `SELECT * FROM sp_obtener_cedula()` |
| `mysqli_query()` | `pg_query()` |
| `mysqli_fetch_assoc()` | `pg_fetch_assoc()` |

---

### 4. **procesar_voto.php** - Registrar Voto
**Cambios MySQL → PostgreSQL:**

| MySQL | PostgreSQL |
|-------|-----------|
| `CALL sp_registrar_voto(...)` | `SELECT * FROM sp_registrar_voto(...)` |
| `mysqli_query()` | `pg_query()` |
| `mysqli_error()` | `pg_last_error()` |

---

### 5. **resultados_publicos.php** - Dashboard de Resultados
**Cambios MySQL → PostgreSQL:**

| MySQL | PostgreSQL |
|-------|-----------|
| `mysqli_query()` | `pg_query()` |
| `mysqli_fetch_assoc()` | `pg_fetch_assoc()` |

**Nota:** Las VISTAS funcionan igual en ambos sistemas.

---

### 6. **admin/login_admin.php** - Login Administradores
**Cambios MySQL → PostgreSQL:**

**Prepared Statements:**
```php
// MYSQL (comentado):
// $stmt = mysqli_prepare($conexion, "SELECT ... WHERE usuario = ? AND clave = MD5(?)");
// mysqli_stmt_bind_param($stmt, "ss", $usuario, $clave);
// mysqli_stmt_execute($stmt);
// $resultado = mysqli_stmt_get_result($stmt);

// POSTGRESQL (activo):
$stmt_name = 'login_admin_' . uniqid();
pg_prepare($conexion, $stmt_name, "SELECT ... WHERE usuario = $1 AND clave = MD5($2)");
$resultado = pg_execute($conexion, $stmt_name, array($usuario, $clave));
```

**Booleanos:**
- MySQL: `estado = 1`
- PostgreSQL: `estado = true`

---

### 7. **admin/gestionar_administradores.php** - Gestión de Admins
**Cambios principales:**
- ✅ Todos los `mysqli_prepare()` → `pg_prepare()`
- ✅ Todos los `mysqli_stmt_bind_param()` eliminados (PostgreSQL usa arrays)
- ✅ `mysqli_stmt_execute()` → `pg_execute()`
- ✅ Estados convertidos a booleanos: `1/0` → `true/false`

---

## 🔑 Diferencias Clave: MySQL vs PostgreSQL

### 1. **Procedimientos Almacenados**
```sql
-- MySQL
CALL sp_registrar_voto(...);

-- PostgreSQL
SELECT * FROM sp_registrar_voto(...);
```

### 2. **Prepared Statements**
```php
// MySQL
$stmt = mysqli_prepare($conexion, "SELECT * FROM tabla WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// PostgreSQL
$stmt_name = 'query_' . uniqid();
pg_prepare($conexion, $stmt_name, "SELECT * FROM tabla WHERE id = $1");
$result = pg_execute($conexion, $stmt_name, array($id));
```

### 3. **Tipos de Datos Booleanos**
```php
// MySQL (TINYINT: 0 o 1)
if ($fila['activo'] == 1)

// PostgreSQL (BOOLEAN: true/false o 't'/'f')
if ($fila['activo'] == 't' || $fila['activo'] === true)
```

### 4. **Funciones de Consulta**
| Operación | MySQL | PostgreSQL |
|-----------|-------|------------|
| Conectar | `mysqli_connect()` | `pg_connect()` |
| Consultar | `mysqli_query()` | `pg_query()` |
| Fetch | `mysqli_fetch_assoc()` | `pg_fetch_assoc()` |
| Num rows | `mysqli_num_rows()` | `pg_num_rows()` |
| Error | `mysqli_error()` | `pg_last_error()` |
| Cerrar | `mysqli_close()` | `pg_close()` |
| Escape | `mysqli_real_escape_string()` | `pg_escape_string()` |

---

## 📦 Base de Datos en Supabase

### Archivo SQL Compatible
📄 **database_electoral_supabase.sql**

**Principales diferencias con MySQL:**
- `SERIAL` en lugar de `AUTO_INCREMENT`
- `BOOLEAN` en lugar de `TINYINT(1)`
- `INTEGER` en lugar de `YEAR`
- Funciones (`CREATE FUNCTION`) en lugar de procedimientos (`CREATE PROCEDURE`)
- `$$` como delimitador en lugar de `DELIMITER //`

### Importar en Supabase
1. Ve a tu proyecto: https://supabase.com/dashboard
2. Click en **SQL Editor**
3. Pega el contenido completo de `database_electoral_supabase.sql`
4. Click **Run**
5. Verifica que se crearon:
   - 5 tablas: `tbl_ciudadano`, `tbl_partido`, `tbl_candidato`, `tbl_voto`, `tbl_administrador`
   - 2 vistas: `v_resultados_tiempo_real`, `v_estadisticas_elecciones`
   - 3 funciones: `sp_registrar_voto()`, `sp_obtener_cedula()`, `sp_validar_ciudadano()`

---

## 🔄 Cómo Volver a MySQL

Si necesitas volver a usar MySQL (localhost):

### 1. Edita `conexion.php`:
```php
// Comenta la sección de Supabase y descomenta MySQL:

// MYSQL (localhost):
$servidor = "localhost";
$usuario = "root";
$clave = "root";
$base_datos = "db_elecciones_2026";
$conexion = mysqli_connect($servidor, $usuario, $clave, $base_datos);

// SUPABASE (comentar):
// $supabase_host = "db.kvjnvvwbxdlporvwdupy.supabase.co";
// ...
```

### 2. En cada archivo PHP:
Descomenta las líneas marcadas con `// MYSQL (comentado):` y comenta las de `// POSTGRESQL (Supabase):`

---

## ✅ Ventajas de Supabase vs InfinityFree

| Característica | InfinityFree | Supabase |
|----------------|--------------|----------|
| Base de datos | MySQL 5.7 | PostgreSQL 15+ |
| Límite de tamaño | 5MB - 512MB | 500MB (gratis) |
| Velocidad | Lenta | Rápida |
| SSL/TLS | No | Sí (requerido) |
| VIEWs | Limitadas | Completas |
| Procedimientos | Limitados | Funciones completas |
| API REST | No | Sí (automática) |
| Backups | Manual | Automático |
| Uptime | 95-98% | 99.9% |

---

## 🧪 Probar la Migración

### 1. Verifica la conexión:
```php
<?php
include 'conexion.php';
if ($conexion) {
    echo "✅ Conectado a Supabase";
} else {
    echo "❌ Error: " . pg_last_error();
}
?>
```

### 2. Prueba el login:
- Ve a `index.php`
- Ingresa DNI: `12345678`
- Completa CAPTCHA
- Deberías ver la cédula de votación

### 3. Prueba el voto:
- Selecciona un partido
- Click en "Confirmar Voto"
- Verifica que se registre correctamente

### 4. Prueba admin:
- Ve a `admin/login_admin.php`
- Usuario: `admin`
- Contraseña: `admin123`
- Verifica dashboard y gestión de admins

---

## 📞 Soporte

Si encuentras errores, verifica:
1. ✅ Importaste correctamente el SQL en Supabase
2. ✅ Los datos de conexión en `conexion.php` son correctos
3. ✅ La extensión `php_pgsql` está habilitada en tu servidor
4. ✅ Revisa los logs de error de PHP

---

## 📝 Notas Importantes

⚠️ **MD5 para contraseñas**: Actualmente se usa MD5 (no seguro para producción). En producción, migra a **bcrypt** o **Argon2**.

⚠️ **CAPTCHA en localhost**: El código detecta automáticamente localhost y omite la validación de CAPTCHA en desarrollo.

⚠️ **Booleanos**: PostgreSQL usa `true/false` o `'t'/'f'` para booleanos, no `1/0` como MySQL.

---

**Fecha de migración:** 20 de Noviembre, 2025
**Sistema:** Elecciones Perú 2026
**Desarrollador:** @Cristhianmcc
