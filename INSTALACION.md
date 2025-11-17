# 📥 GUÍA DE INSTALACIÓN - SISTEMA ELECTORAL PERÚ 2026

## 🎯 PASO A PASO COMPLETO

---

## ✅ PASO 1: VERIFICAR REQUISITOS

### **Software Necesario:**
- ✅ Apache 2.4 o superior
- ✅ PHP 8.0 o superior
- ✅ MySQL 8.0 o superior
- ✅ Navegador moderno (Chrome, Firefox, Edge)

### **Verificar instalación de Apache:**
```powershell
# Abrir PowerShell y ejecutar:
httpd.exe -v
```

Debe mostrar algo como:
```
Server version: Apache/2.4.57 (Win64)
```

### **Verificar instalación de PHP:**
```powershell
php -v
```

Debe mostrar algo como:
```
PHP 8.2.x (cli) (built: ...)
```

### **Verificar instalación de MySQL:**
```powershell
mysql --version
```

Debe mostrar algo como:
```
mysql  Ver 8.0.x for Win64
```

---

## ✅ PASO 2: COPIAR ARCHIVOS

La carpeta del proyecto ya está en:
```
c:\Apache24\htdocs\elecciones_peru_2026\
```

Si necesitas moverla o copiarla, asegúrate de que esté en la carpeta `htdocs` de Apache.

### **Estructura esperada:**
```
c:\Apache24\htdocs\elecciones_peru_2026\
├── index.php
├── conexion.php
├── database_electoral.sql
├── login_electoral.php
├── cedula_votacion.php
├── procesar_voto.php
├── confirmacion_voto.php
├── resultados_publicos.php
├── logout.php
├── README.md
└── assets/
    ├── css/
    ├── js/
    └── img/
        ├── candidatos/
        └── partidos/
```

---

## ✅ PASO 3: CREAR BASE DE DATOS

### **Opción A: Usando phpMyAdmin**

1. Abre tu navegador y ve a:
   ```
   http://localhost/phpmyadmin/
   ```

2. Haz clic en la pestaña **"SQL"**

3. Copia y pega TODO el contenido del archivo `database_electoral.sql`

4. Haz clic en **"Continuar"** o **"Go"**

5. Espera a que se ejecute (puede tardar 10-20 segundos)

6. Verifica que se creó la base de datos `db_elecciones_2026`

### **Opción B: Usando MySQL Workbench**

1. Abre **MySQL Workbench**

2. Conecta a tu servidor local:
   - Host: `localhost`
   - Port: `3306`
   - User: `root`
   - Password: (tu contraseña o vacío)

3. Ve a **File → Open SQL Script**

4. Selecciona el archivo:
   ```
   c:\Apache24\htdocs\elecciones_peru_2026\database_electoral.sql
   ```

5. Haz clic en el botón de **"Execute"** (rayo)

6. Verifica en el panel izquierdo que se creó `db_elecciones_2026`

### **Opción C: Usando línea de comandos**

```powershell
# Abrir PowerShell en la carpeta del proyecto
cd c:\Apache24\htdocs\elecciones_peru_2026

# Ejecutar el script SQL
mysql -u root -p < database_electoral.sql

# Ingresar contraseña de MySQL (si tiene)
```

---

## ✅ PASO 4: CONFIGURAR CONEXIÓN

1. Abre el archivo `conexion.php` con tu editor favorito

2. Verifica que los datos de conexión sean correctos:

```php
<?php
$servidor = "localhost";      // ← No cambiar
$usuario = "root";            // ← Tu usuario de MySQL
$clave = "";                  // ← Tu contraseña de MySQL (vacío si no tiene)
$base_datos = "db_elecciones_2026";  // ← No cambiar
?>
```

3. **Si tu MySQL tiene contraseña**, cámbiala:
```php
$clave = "tu_contraseña_aqui";
```

4. Guarda el archivo

---

## ✅ PASO 5: INICIAR SERVICIOS

### **Iniciar Apache:**

```powershell
# Opción 1: Desde PowerShell
cd c:\Apache24\bin
.\httpd.exe

# Opción 2: Si tienes Apache como servicio
net start Apache2.4
```

### **Iniciar MySQL:**

```powershell
# Opción 1: Desde PowerShell
cd "c:\Program Files\MySQL\MySQL Server 8.0\bin"
.\mysqld.exe

# Opción 2: Si tienes MySQL como servicio
net start MySQL80
```

### **Verificar que están corriendo:**

Abre tu navegador y ve a:
```
http://localhost/
```

Deberías ver la página de inicio de Apache o un listado de carpetas.

---

## ✅ PASO 6: ACCEDER AL SISTEMA

### **Abrir el Sistema Electoral:**

En tu navegador, ve a:
```
http://localhost/elecciones_peru_2026/
```

Deberías ver la pantalla de login con:
- Logo de votación
- Campo para ingresar DNI
- Botón "INGRESAR A VOTAR"
- Botón "Ver Resultados en Tiempo Real"

---

## ✅ PASO 7: PROBAR EL SISTEMA

### **Prueba 1: Login y Votación**

1. En la pantalla de login, ingresa un DNI de prueba:
   ```
   12345678
   ```

2. Haz clic en **"INGRESAR A VOTAR"**

3. Deberías ver la cédula de votación con 8 candidatos

4. Haz clic en cualquier candidato para seleccionarlo

5. Verás que la tarjeta se resalta con un check ✓

6. Haz clic en **"CONFIRMAR MI VOTO"**

7. Confirma en el diálogo que aparece

8. Deberías ver la página de confirmación con:
   - ✓ Tu voto ha sido registrado
   - Tus datos (DNI, nombre, fecha)
   - Botón para ver resultados

### **Prueba 2: Ver Resultados**

1. Haz clic en **"VER RESULTADOS EN TIEMPO REAL"**

2. Deberías ver:
   - Dashboard oscuro estilo ONPE
   - 4 tarjetas con estadísticas
   - Gráfico de barras con Chart.js
   - Lista de candidatos con fotos y votos
   - Actualización automática cada 5 segundos

### **Prueba 3: Intentar Votar Nuevamente**

1. Ve a la página de inicio:
   ```
   http://localhost/elecciones_peru_2026/
   ```

2. Intenta ingresar el mismo DNI: `12345678`

3. Deberías ver un mensaje de error:
   ```
   ⚠️ Ya emitiste tu voto. Solo puedes votar una vez.
   ```

---

## ✅ PASO 8: VOTAR CON MÁS CIUDADANOS

Para ver el dashboard más completo, vota con varios DNIs:

```
12345678  →  JUAN CARLOS PEREZ GARCIA
87654321  →  MARIA ELENA RODRIGUEZ LOPEZ
11223344  →  PEDRO JOSE GONZALES MARTINEZ
44332211  →  ANA LUCIA FERNANDEZ TORRES
55667788  →  CARLOS ALBERTO SANCHEZ DIAZ
88776655  →  ROSA MARIA VARGAS MENDOZA
99887766  →  JOSE LUIS RAMIREZ CASTRO
66778899  →  CARMEN ROSA FLORES SILVA
77889900  →  MIGUEL ANGEL TORRES RUIZ
00998877  →  LUCIA PATRICIA CHAVEZ MORALES
```

**Proceso:**
1. Cierra sesión o abre una ventana de incógnito
2. Ingresa con otro DNI
3. Vota por otro candidato
4. Ve los resultados actualizándose en tiempo real

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### **❌ Error: "No se puede conectar a la base de datos"**

**Causa:** MySQL no está corriendo o credenciales incorrectas

**Solución:**
1. Verifica que MySQL esté corriendo:
   ```powershell
   netstat -an | findstr "3306"
   ```
   Debe mostrar una línea con el puerto 3306

2. Verifica usuario y contraseña en `conexion.php`

3. Intenta conectarte manualmente:
   ```powershell
   mysql -u root -p
   ```

---

### **❌ Error: "La página no se encuentra"**

**Causa:** Apache no está corriendo o ruta incorrecta

**Solución:**
1. Verifica que Apache esté corriendo:
   ```powershell
   netstat -an | findstr "80"
   ```
   Debe mostrar el puerto 80

2. Verifica que la carpeta esté en:
   ```
   c:\Apache24\htdocs\elecciones_peru_2026\
   ```

3. Intenta acceder a:
   ```
   http://localhost/
   ```

---

### **❌ Error: "Call to undefined function mysqli_connect()"**

**Causa:** Extensión mysqli de PHP no está habilitada

**Solución:**
1. Abre el archivo `php.ini` (ubicado en `c:\php\php.ini`)

2. Busca la línea:
   ```ini
   ;extension=mysqli
   ```

3. Quita el punto y coma para descomentarla:
   ```ini
   extension=mysqli
   ```

4. Guarda y reinicia Apache

---

### **❌ Error: "Base de datos 'db_elecciones_2026' no existe"**

**Causa:** No se ejecutó el script SQL correctamente

**Solución:**
1. Abre phpMyAdmin o MySQL Workbench

2. Verifica si existe la base de datos `db_elecciones_2026`

3. Si no existe, ejecuta nuevamente el archivo `database_electoral.sql`

4. Verifica que se crearon las tablas:
   - tbl_ciudadano
   - tbl_partido
   - tbl_candidato
   - tbl_voto
   - tbl_administrador

---

### **❌ Los resultados no se actualizan automáticamente**

**Causa:** JavaScript deshabilitado o error en el navegador

**Solución:**
1. Abre la consola del navegador (F12)

2. Verifica si hay errores en JavaScript

3. Recarga la página manualmente (F5)

4. Asegúrate de tener conexión a CDN de Chart.js

---

### **❌ Las imágenes de candidatos no se muestran**

**Causa:** Rutas de imágenes no configuradas o archivos faltantes

**Solución:**
1. Las imágenes están configuradas para cargar desde:
   ```
   assets/img/candidatos/
   assets/img/partidos/
   ```

2. Por defecto, usa imágenes de placeholder si no existen

3. Para agregar imágenes reales:
   - Descarga fotos de candidatos
   - Renómbralas según el script SQL
   - Colócalas en las carpetas correspondientes

---

## 📊 VERIFICAR INSTALACIÓN COMPLETA

### **Checklist de Verificación:**

- [ ] Apache está corriendo (puerto 80)
- [ ] MySQL está corriendo (puerto 3306)
- [ ] Base de datos `db_elecciones_2026` existe
- [ ] Tabla `tbl_ciudadano` tiene 10 registros
- [ ] Tabla `tbl_partido` tiene 10 registros (8 + blanco + nulo)
- [ ] Tabla `tbl_candidato` tiene registros
- [ ] Puedo acceder a `http://localhost/elecciones_peru_2026/`
- [ ] Puedo hacer login con DNI `12345678`
- [ ] Puedo ver la cédula de votación
- [ ] Puedo votar y ver confirmación
- [ ] Puedo ver resultados en tiempo real
- [ ] El dashboard se actualiza cada 5 segundos

---

## 🎉 ¡INSTALACIÓN COMPLETADA!

Si todos los pasos fueron exitosos, tu Sistema Electoral Perú 2026 está funcionando correctamente.

### **Próximos pasos:**

1. ✅ Explora todas las funcionalidades
2. ✅ Vota con diferentes DNIs
3. ✅ Observa los resultados en tiempo real
4. ✅ Personaliza colores y diseños
5. ✅ Agrega más candidatos si deseas
6. ✅ Exporta resultados

---

## 📞 SOPORTE

Si tienes problemas durante la instalación:

1. **Revisa esta guía** paso por paso
2. **Verifica los logs** de Apache y MySQL
3. **Consulta la consola** del navegador (F12)
4. **Revisa el README.md** para más detalles

---

**¡Disfruta del Sistema Electoral Perú 2026!** 🗳️🇵🇪
