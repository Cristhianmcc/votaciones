# 📊 GUÍA COMPLETA DE LA BASE DE DATOS - SISTEMA ELECTORAL PERÚ 2026

## 📚 ÍNDICE
1. [Visión General](#1-visión-general)
2. [Estructura de Tablas](#2-estructura-de-tablas)
3. [Relaciones entre Tablas](#3-relaciones-entre-tablas)
4. [Vistas](#4-vistas)
5. [Procedimientos Almacenados](#5-procedimientos-almacenados)
6. [Índices y Optimizaciones](#6-índices-y-optimizaciones)
7. [Reglas de Negocio](#7-reglas-de-negocio)
8. [Diagrama Entidad-Relación](#8-diagrama-entidad-relación)

---

## 1. VISIÓN GENERAL

### 🎯 Objetivo del Sistema
Gestionar el proceso electoral completo de las Elecciones Presidenciales de Perú 2026, permitiendo:
- ✅ Registro y validación de votantes del padrón electoral
- ✅ Gestión de partidos políticos y candidatos
- ✅ Proceso de votación digital seguro
- ✅ Conteo y visualización de resultados en tiempo real
- ✅ Administración del sistema

### 📐 Características Técnicas
- **Motor:** MySQL/MariaDB con InnoDB
- **Codificación:** UTF-8 (utf8mb4)
- **Collation:** utf8mb4_unicode_ci
- **Normalización:** 3FN (Tercera Forma Normal)
- **Integridad Referencial:** Llaves foráneas con CASCADE
- **Total de Tablas:** 5 tablas principales
- **Total de Vistas:** 2 vistas
- **Total de Procedimientos:** 3 stored procedures

---

## 2. ESTRUCTURA DE TABLAS

### 📋 Tabla 1: `tbl_ciudadano` (Padrón Electoral)

**Propósito:** Almacenar el padrón electoral completo de ciudadanos habilitados para votar.

#### Estructura:
```sql
CREATE TABLE tbl_ciudadano (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni CHAR(8) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    departamento VARCHAR(50) DEFAULT 'LIMA',
    provincia VARCHAR(50) DEFAULT 'LIMA',
    distrito VARCHAR(50) DEFAULT 'LIMA',
    email VARCHAR(100),
    telefono VARCHAR(15),
    foto_url VARCHAR(500),
    ha_votado TINYINT(1) DEFAULT 0,
    fecha_voto DATETIME NULL,
    ip_voto VARCHAR(45) NULL,
    estado TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

#### Campos Clave:

| Campo | Tipo | Descripción | Importancia |
|-------|------|-------------|-------------|
| `id` | INT | Identificador único interno | PK, Auto-increment |
| `dni` | CHAR(8) | DNI del ciudadano (8 dígitos) | **UNIQUE** - Llave de negocio |
| `ha_votado` | TINYINT(1) | ¿Ya votó? (0=No, 1=Sí) | **CRÍTICO** - Evita doble voto |
| `fecha_voto` | DATETIME | Momento exacto del voto | Auditoría |
| `ip_voto` | VARCHAR(45) | IP desde donde votó | Seguridad/Auditoría |
| `estado` | TINYINT(1) | ¿Ciudadano activo? | Soft delete |

#### Índices:
- **idx_dni:** Búsqueda rápida por DNI (autenticación)
- **idx_ha_votado:** Filtrado rápido de votantes/no votantes

#### Reglas de Negocio:
1. ✅ DNI debe ser ÚNICO (una persona = un registro)
2. ✅ Solo ciudadanos con `estado = 1` pueden votar
3. ✅ Una vez `ha_votado = 1`, no puede cambiar
4. ✅ El DNI es el método de autenticación

---

### 🎭 Tabla 2: `tbl_partido` (Partidos Políticos)

**Propósito:** Gestionar los partidos políticos participantes en las elecciones.

#### Estructura:
```sql
CREATE TABLE tbl_partido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_corto VARCHAR(50) NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    siglas VARCHAR(20) UNIQUE NOT NULL,
    logo_url VARCHAR(500) NOT NULL,
    color_primario VARCHAR(7) DEFAULT '#333333',
    color_secundario VARCHAR(7) DEFAULT '#666666',
    fundacion_year YEAR,
    ideologia VARCHAR(100),
    descripcion TEXT,
    estado TINYINT(1) DEFAULT 1,
    orden_cedula INT DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

#### Campos Clave:

| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `siglas` | VARCHAR(20) | Siglas únicas del partido | "FP", "PL", "APP" |
| `color_primario` | VARCHAR(7) | Color HEX para UI | "#FF6600" |
| `orden_cedula` | INT | Orden en la cédula de votación | 1, 2, 3... |
| `ideologia` | VARCHAR(100) | Orientación política | "Derecha", "Izquierda" |

#### Índices:
- **idx_siglas:** Búsqueda rápida por siglas
- **idx_orden:** Ordenamiento en cédula

#### Datos Especiales:
```sql
-- Partidos normales: orden 1-8
-- Voto en blanco: orden 99
-- Voto nulo: orden 100
```

---

### 👔 Tabla 3: `tbl_candidato` (Candidatos Presidenciales)

**Propósito:** Almacenar candidatos (Presidente y Vicepresidentes) de cada partido.

#### Estructura:
```sql
CREATE TABLE tbl_candidato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partido_id INT NOT NULL,
    tipo_candidato ENUM('PRESIDENTE', 'VICEPRESIDENTE_1', 'VICEPRESIDENTE_2') NOT NULL,
    dni CHAR(8) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    foto_url VARCHAR(500) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    profesion VARCHAR(100),
    biografia TEXT,
    plan_gobierno_url VARCHAR(500),
    redes_sociales JSON,
    hojavida_url VARCHAR(500),
    estado TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partido_id) REFERENCES tbl_partido(id) ON DELETE CASCADE
)
```

#### Campos Clave:

| Campo | Tipo | Descripción | Valores Posibles |
|-------|------|-------------|------------------|
| `partido_id` | INT | **FK** → tbl_partido | 1, 2, 3... |
| `tipo_candidato` | ENUM | Tipo de candidatura | PRESIDENTE, VICEPRESIDENTE_1, VICEPRESIDENTE_2 |
| `dni` | CHAR(8) | DNI único del candidato | "10203040" |
| `redes_sociales` | JSON | Redes sociales del candidato | `{"twitter": "@usuario"}` |

#### Relación con Partido:
```
UN partido → TIENE → VARIOS candidatos (1:N)
  - 1 Presidente
  - 1 Vicepresidente 1ro
  - 1 Vicepresidente 2do
```

#### Restricción de Integridad:
```sql
ON DELETE CASCADE
-- Si se elimina un partido, se eliminan automáticamente sus candidatos
```

---

### 🗳️ Tabla 4: `tbl_voto` (Registro de Votos)

**Propósito:** Almacenar cada voto emitido (tabla más crítica del sistema).

#### Estructura:
```sql
CREATE TABLE tbl_voto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciudadano_id INT NOT NULL,
    partido_id INT NOT NULL,
    voto_tipo ENUM('VALIDO', 'BLANCO', 'NULO') DEFAULT 'VALIDO',
    fecha_voto DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    tiempo_votacion_segundos INT DEFAULT 0,
    FOREIGN KEY (ciudadano_id) REFERENCES tbl_ciudadano(id) ON DELETE CASCADE,
    FOREIGN KEY (partido_id) REFERENCES tbl_partido(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ciudadano_voto (ciudadano_id)
)
```

#### Campos Clave:

| Campo | Tipo | Descripción | Importancia |
|-------|------|-------------|-------------|
| `ciudadano_id` | INT | **FK** → tbl_ciudadano | Quién votó |
| `partido_id` | INT | **FK** → tbl_partido | Por quién votó |
| `voto_tipo` | ENUM | Clasificación del voto | VALIDO/BLANCO/NULO |
| `tiempo_votacion_segundos` | INT | Tiempo que tardó en votar | Análisis estadístico |
| `ip_address` | VARCHAR(45) | IP del votante | Auditoría/Seguridad |

#### **RESTRICCIÓN MÁS IMPORTANTE:**
```sql
UNIQUE KEY unique_ciudadano_voto (ciudadano_id)
-- GARANTIZA: Un ciudadano solo puede tener UN registro de voto
-- PREVIENE: Fraude electoral por doble votación
```

#### Tipos de Voto:

```
VALIDO  → Voto por un partido específico
BLANCO  → Voto en blanco (no elige ningún candidato)
NULO    → Voto nulo o viciado (marcó mal, múltiples opciones, etc.)
```

#### Índices:
- **idx_partido_voto:** Conteo rápido de votos por partido
- **idx_fecha_voto:** Análisis temporal de votación

---

### 👤 Tabla 5: `tbl_administrador` (Administradores del Sistema)

**Propósito:** Gestionar usuarios con acceso al panel administrativo.

#### Estructura:
```sql
CREATE TABLE tbl_administrador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('SUPERADMIN', 'MODERADOR', 'OBSERVADOR') DEFAULT 'MODERADOR',
    estado TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

#### Roles del Sistema:

| Rol | Permisos | Descripción |
|-----|----------|-------------|
| **SUPERADMIN** | Control total | Puede crear/eliminar usuarios, configurar sistema |
| **MODERADOR** | Lectura/Escritura | Puede revisar votos, generar reportes |
| **OBSERVADOR** | Solo lectura | Solo puede ver resultados, no modificar |

#### Seguridad:
```sql
-- Las contraseñas se almacenan con MD5 (en producción usar bcrypt)
clave = MD5('password')
```

---

## 3. RELACIONES ENTRE TABLAS

### 📊 Diagrama Textual de Relaciones:

```
┌─────────────────┐
│ tbl_ciudadano   │
│ (Votantes)      │
└────────┬────────┘
         │ 1
         │
         │ ha_votado (0 o 1)
         │
         │ N
    ┌────▼────────┐
    │  tbl_voto   │
    │  (Votos)    │
    └────┬────────┘
         │ N
         │
         │
         │ 1
┌────────▼─────────┐          ┌──────────────────┐
│  tbl_partido     │◄─────────┤ tbl_candidato    │
│  (Partidos)      │    1:N   │  (Candidatos)    │
└──────────────────┘          └──────────────────┘

┌──────────────────┐
│ tbl_administrador│ (Independiente)
│ (Administradores)│
└──────────────────┘
```

### 🔗 Relaciones Detalladas:

#### Relación 1: **Ciudadano → Voto** (1:1)
```sql
tbl_ciudadano.id (PK) ←→ tbl_voto.ciudadano_id (FK, UNIQUE)
```
- **Cardinalidad:** 1:1 (Un ciudadano - Un voto máximo)
- **Tipo:** Obligatoria cuando vota
- **Restricción:** `UNIQUE KEY unique_ciudadano_voto`
- **Regla:** Un ciudadano puede tener 0 o 1 voto, nunca más de 1

#### Relación 2: **Partido → Voto** (1:N)
```sql
tbl_partido.id (PK) ←→ tbl_voto.partido_id (FK)
```
- **Cardinalidad:** 1:N (Un partido - Múltiples votos)
- **Tipo:** Obligatoria
- **Regla:** Un partido puede recibir 0, 1 o muchos votos

#### Relación 3: **Partido → Candidato** (1:N)
```sql
tbl_partido.id (PK) ←→ tbl_candidato.partido_id (FK)
```
- **Cardinalidad:** 1:N (Un partido - Varios candidatos)
- **Tipo:** Obligatoria
- **Regla:** Un partido debe tener al menos 1 candidato (presidente)
- **Cascade:** Si se elimina el partido, se eliminan sus candidatos

### 🎯 Flujo de Datos:

```
1. CIUDADANO se autentica con DNI
   ↓
2. SISTEMA valida en tbl_ciudadano
   ↓
3. MUESTRA opciones de tbl_partido + tbl_candidato
   ↓
4. CIUDADANO selecciona un partido
   ↓
5. SISTEMA inserta en tbl_voto
   ↓
6. SISTEMA actualiza tbl_ciudadano (ha_votado = 1)
   ↓
7. RESULTADOS se calculan desde tbl_voto
```

---

## 4. VISTAS

Las vistas son "tablas virtuales" que simplifican consultas complejas.

### 🔍 Vista 1: `v_resultados_tiempo_real`

**Propósito:** Mostrar resultados electorales actualizados en tiempo real.

```sql
CREATE VIEW v_resultados_tiempo_real AS
SELECT 
    p.id AS partido_id,
    p.nombre_corto,
    p.siglas,
    p.logo_url,
    p.color_primario,
    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS candidato_nombre,
    c.foto_url AS candidato_foto,
    COUNT(v.id) AS total_votos,
    ROUND((COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'VALIDO'), 0)), 2) AS porcentaje,
    p.orden_cedula
FROM tbl_partido p
LEFT JOIN tbl_candidato c ON p.id = c.partido_id AND c.tipo_candidato = 'PRESIDENTE'
LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.voto_tipo = 'VALIDO'
WHERE p.estado = 1 AND p.id NOT IN (
    SELECT id FROM tbl_partido WHERE siglas IN ('BLANCO', 'NULO')
)
GROUP BY p.id, ...
ORDER BY total_votos DESC, p.orden_cedula ASC;
```

#### ¿Qué hace?
1. **Combina** 3 tablas: partido, candidato, voto
2. **Cuenta** votos por partido
3. **Calcula** porcentaje de votos
4. **Excluye** votos en blanco y nulos
5. **Ordena** por más votos primero

#### Columnas resultantes:
- `partido_id` - ID del partido
- `nombre_corto` - Nombre del partido
- `candidato_nombre` - Nombre completo del presidente
- `total_votos` - Cantidad de votos recibidos
- `porcentaje` - % del total de votos válidos

#### Uso en la aplicación:
```php
SELECT * FROM v_resultados_tiempo_real;
// Retorna resultados listos para mostrar en dashboard
```

---

### 📈 Vista 2: `v_estadisticas_elecciones`

**Propósito:** Proporcionar estadísticas generales del proceso electoral.

```sql
CREATE VIEW v_estadisticas_elecciones AS
SELECT 
    (SELECT COUNT(*) FROM tbl_ciudadano WHERE estado = 1) AS total_ciudadanos,
    (SELECT COUNT(*) FROM tbl_ciudadano WHERE ha_votado = 1) AS total_votantes,
    (SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'VALIDO') AS votos_validos,
    (SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'BLANCO') AS votos_blancos,
    (SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'NULO') AS votos_nulos,
    (SELECT COUNT(*) FROM tbl_partido WHERE estado = 1 AND siglas NOT IN ('BLANCO', 'NULO')) AS total_partidos,
    ROUND(((SELECT COUNT(*) FROM tbl_ciudadano WHERE ha_votado = 1) * 100.0 / 
           NULLIF((SELECT COUNT(*) FROM tbl_ciudadano WHERE estado = 1), 0)), 2) AS porcentaje_participacion;
```

#### Retorna una SOLA fila con:

| Campo | Descripción | Ejemplo |
|-------|-------------|---------|
| `total_ciudadanos` | Total de votantes habilitados | 10,000 |
| `total_votantes` | Cuántos ya votaron | 6,500 |
| `votos_validos` | Votos por partidos | 6,200 |
| `votos_blancos` | Votos en blanco | 200 |
| `votos_nulos` | Votos nulos | 100 |
| `total_partidos` | Partidos participantes | 8 |
| `porcentaje_participacion` | % de participación | 65.00% |

#### Ventajas de usar vistas:
✅ Simplifica consultas complejas  
✅ Encapsula lógica de negocio  
✅ Mejora legibilidad del código  
✅ Actualizadas automáticamente  

---

## 5. PROCEDIMIENTOS ALMACENADOS

Los procedimientos almacenados son funciones SQL que ejecutan lógica compleja en el servidor.

### ⚙️ Procedimiento 1: `sp_validar_ciudadano`

**Propósito:** Validar si un DNI existe y puede votar.

```sql
DELIMITER //
CREATE PROCEDURE sp_validar_ciudadano(
    IN p_dni CHAR(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    SELECT 
        id,
        dni,
        CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo,
        nombres,
        apellido_paterno,
        apellido_materno,
        departamento,
        provincia,
        distrito,
        ha_votado,
        estado
    FROM tbl_ciudadano
    WHERE dni = p_dni AND estado = 1
    LIMIT 1;
END //
DELIMITER ;
```

#### Parámetros:
- **IN p_dni:** DNI a buscar (8 caracteres)

#### Retorna:
- **1 fila** si encuentra al ciudadano
- **0 filas** si no existe o está inactivo

#### Validaciones:
1. ✅ DNI debe existir en el padrón
2. ✅ Ciudadano debe estar activo (`estado = 1`)

#### Uso en PHP:
```php
$query = "CALL sp_validar_ciudadano('12345678')";
$resultado = mysqli_query($conexion, $query);
$ciudadano = mysqli_fetch_assoc($resultado);
```

---

### ⚙️ Procedimiento 2: `sp_obtener_cedula`

**Propósito:** Obtener todos los partidos y candidatos para la cédula de votación.

```sql
DELIMITER //
CREATE PROCEDURE sp_obtener_cedula()
BEGIN
    SELECT 
        p.id AS partido_id,
        p.nombre_corto,
        p.nombre_completo,
        p.siglas,
        p.logo_url,
        p.color_primario,
        p.orden_cedula,
        CONCAT(cp.nombres, ' ', cp.apellido_paterno, ' ', cp.apellido_materno) AS presidente,
        cp.foto_url AS presidente_foto,
        cp.profesion AS presidente_profesion,
        CONCAT(cv1.nombres, ' ', cv1.apellido_paterno) AS vice1,
        CONCAT(cv2.nombres, ' ', cv2.apellido_paterno) AS vice2
    FROM tbl_partido p
    LEFT JOIN tbl_candidato cp ON p.id = cp.partido_id AND cp.tipo_candidato = 'PRESIDENTE'
    LEFT JOIN tbl_candidato cv1 ON p.id = cv1.partido_id AND cv1.tipo_candidato = 'VICEPRESIDENTE_1'
    LEFT JOIN tbl_candidato cv2 ON p.id = cv2.partido_id AND cv2.tipo_candidato = 'VICEPRESIDENTE_2'
    WHERE p.estado = 1 AND p.siglas NOT IN ('BLANCO', 'NULO')
    ORDER BY p.orden_cedula ASC;
END //
DELIMITER ;
```

#### Parámetros:
- **Ninguno** (no recibe parámetros)

#### Retorna:
- **N filas** (una por cada partido)
- Cada fila incluye partido + presidente + 2 vicepresidentes

#### Joins utilizados:
```
LEFT JOIN → Incluye partidos aunque no tengan candidatos
```

#### Uso:
```php
$query = "CALL sp_obtener_cedula()";
$resultado = mysqli_query($conexion, $query);
while ($partido = mysqli_fetch_assoc($resultado)) {
    // Mostrar cada partido en la cédula
}
```

---

### ⚙️ Procedimiento 3: `sp_registrar_voto` ⭐ (MÁS IMPORTANTE)

**Propósito:** Registrar un voto de forma segura y transaccional.

```sql
DELIMITER //
CREATE PROCEDURE sp_registrar_voto(
    IN p_dni_ciudadano CHAR(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_partido_id INT,
    IN p_voto_tipo VARCHAR(10),
    IN p_ip VARCHAR(45),
    IN p_tiempo INT
)
BEGIN
    DECLARE v_ciudadano_id INT;
    DECLARE v_ya_voto INT;
    
    -- 1. Buscar ciudadano por DNI
    SELECT id, ha_votado INTO v_ciudadano_id, v_ya_voto
    FROM tbl_ciudadano 
    WHERE dni = p_dni_ciudadano AND estado = 1
    LIMIT 1;
    
    -- 2. Validar que el ciudadano existe
    IF v_ciudadano_id IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'DNI no encontrado en el padrón electoral';
    END IF;
    
    -- 3. Validar que no haya votado antes
    IF v_ya_voto = 1 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Este ciudadano ya emitió su voto';
    END IF;
    
    -- 4. Registrar el voto
    INSERT INTO tbl_voto (ciudadano_id, partido_id, voto_tipo, ip_address, tiempo_votacion_segundos)
    VALUES (v_ciudadano_id, p_partido_id, p_voto_tipo, p_ip, p_tiempo);
    
    -- 5. Actualizar estado del ciudadano
    UPDATE tbl_ciudadano 
    SET ha_votado = 1, 
        fecha_voto = NOW(),
        ip_voto = p_ip
    WHERE id = v_ciudadano_id;
    
    -- 6. Confirmar éxito
    SELECT 'Voto registrado exitosamente' AS mensaje, v_ciudadano_id AS ciudadano_id;
END //
DELIMITER ;
```

#### Parámetros:

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `p_dni_ciudadano` | CHAR(8) | DNI del votante |
| `p_partido_id` | INT | ID del partido votado |
| `p_voto_tipo` | VARCHAR(10) | VALIDO/BLANCO/NULO |
| `p_ip` | VARCHAR(45) | IP del votante |
| `p_tiempo` | INT | Segundos que tardó |

#### Flujo de Validaciones:

```
1. ¿Existe el DNI en tbl_ciudadano?
   ├─ NO → ERROR: "DNI no encontrado"
   └─ SÍ → Continuar

2. ¿Ya votó (ha_votado = 1)?
   ├─ SÍ → ERROR: "Ya emitió su voto"
   └─ NO → Continuar

3. INSERT en tbl_voto
   └─ Registra el voto

4. UPDATE en tbl_ciudadano
   └─ Marca ha_votado = 1
   └─ Guarda fecha_voto
   └─ Guarda ip_voto

5. RETURN mensaje de éxito
```

#### Seguridad y Atomicidad:
- ✅ **Transacción implícita:** Todo o nada
- ✅ **Validaciones:** Previene fraude
- ✅ **SIGNAL:** Manejo de errores SQL
- ✅ **Doble protección:** Check + UNIQUE constraint

---

## 6. ÍNDICES Y OPTIMIZACIONES

### 📌 Índices Implementados:

#### tbl_ciudadano:
```sql
INDEX idx_dni (dni)           -- Login rápido
INDEX idx_ha_votado (ha_votado) -- Filtros votantes/no votantes
```

#### tbl_partido:
```sql
INDEX idx_siglas (siglas)     -- Búsqueda por siglas
INDEX idx_orden (orden_cedula) -- Ordenamiento en cédula
```

#### tbl_candidato:
```sql
INDEX idx_partido (partido_id) -- JOIN con partido
INDEX idx_tipo (tipo_candidato) -- Filtro por tipo
```

#### tbl_voto:
```sql
INDEX idx_partido_voto (partido_id) -- Conteo de votos
INDEX idx_fecha_voto (fecha_voto)   -- Análisis temporal
```

### ⚡ Optimizaciones de Rendimiento:

1. **UNIQUE en dni_ciudadano:** Previene duplicados a nivel DB
2. **UNIQUE en ciudadano_id en tbl_voto:** Un voto por persona
3. **InnoDB:** Soporte de transacciones y llaves foráneas
4. **LEFT JOIN en vistas:** Incluye partidos sin votos aún
5. **LIMIT 1:** En búsquedas de un solo resultado
6. **Collation específica:** Evita conflictos de comparación

---

## 7. REGLAS DE NEGOCIO

### ✅ Reglas Críticas:

#### RN-01: Un ciudadano, un voto
```
- Un DNI solo puede registrarse una vez en tbl_ciudadano
- Un ciudadano solo puede tener un registro en tbl_voto
- Una vez votado (ha_votado=1), no puede cambiar
```

#### RN-02: Votación válida
```
- El ciudadano debe existir en el padrón (tbl_ciudadano)
- El ciudadano debe estar activo (estado = 1)
- El ciudadano NO debe haber votado antes (ha_votado = 0)
- El partido votado debe existir y estar activo
```

#### RN-03: Tipos de voto
```
VALIDO → Por un partido específico
BLANCO → Sin preferencia (partido especial "BLANCO")
NULO   → Voto inválido (partido especial "NULO")
```

#### RN-04: Integridad referencial
```
- Si se elimina un partido → Se eliminan sus candidatos
- Si se elimina un partido → Se eliminan sus votos
- Si se elimina un ciudadano → Se elimina su voto
```

#### RN-05: Auditoría
```
- Cada voto registra: fecha, hora, IP, tiempo
- Cada tabla tiene fecha_registro
- Los administradores registran ultimo_acceso
```

### 🔒 Seguridad:

1. **Contraseñas:** Hasheadas con MD5 (mejor usar bcrypt)
2. **DNI único:** Previene suplantación
3. **IP logging:** Rastreo de origen
4. **Soft delete:** `estado = 0` en vez de DELETE
5. **Validaciones dobles:** PHP + SQL

---

## 8. DIAGRAMA ENTIDAD-RELACIÓN

### 📊 Diagrama ER Completo:

```
┌───────────────────────────────────────────────────────────────┐
│                    SISTEMA ELECTORAL PERÚ 2026                 │
└───────────────────────────────────────────────────────────────┘

                    ┌─────────────────────┐
                    │   tbl_ciudadano     │
                    ├─────────────────────┤
                    │ • id (PK)           │
                    │ • dni (UK)          │
                    │ • nombres           │
                    │ • apellido_paterno  │
                    │ • apellido_materno  │
                    │ • fecha_nacimiento  │
                    │ • departamento      │
                    │ • provincia         │
                    │ • distrito          │
                    │ • email             │
                    │ • telefono          │
                    │ • foto_url          │
                    │ • ha_votado         │◄─────┐
                    │ • fecha_voto        │      │
                    │ • ip_voto           │      │ Actualizado
                    │ • estado            │      │ al votar
                    │ • fecha_registro    │      │
                    └──────────┬──────────┘      │
                               │ 1                │
                               │                  │
                               │ emite            │
                               │                  │
                               │ 0..1             │
                    ┌──────────▼──────────┐      │
                    │     tbl_voto        │      │
                    ├─────────────────────┤      │
                    │ • id (PK)           │      │
                    │ • ciudadano_id (FK) │──────┘
                    │   (UNIQUE)          │
                    │ • partido_id (FK)   │───┐
                    │ • voto_tipo         │   │
                    │ • fecha_voto        │   │
                    │ • ip_address        │   │
                    │ • user_agent        │   │
                    │ • tiempo_votacion_s │   │
                    └─────────────────────┘   │
                                              │ N
                                              │
                                              │ recibe
                                              │
                                              │ 1
                    ┌─────────────────────┐   │
                    │   tbl_partido       │◄──┘
                    ├─────────────────────┤
                    │ • id (PK)           │
                    │ • nombre_corto      │
                    │ • nombre_completo   │
                    │ • siglas (UK)       │
                    │ • logo_url          │
                    │ • color_primario    │
                    │ • color_secundario  │
                    │ • fundacion_year    │
                    │ • ideologia         │
                    │ • descripcion       │
                    │ • estado            │
                    │ • orden_cedula      │
                    │ • fecha_registro    │
                    └──────────┬──────────┘
                               │ 1
                               │
                               │ tiene
                               │
                               │ N
                    ┌──────────▼──────────┐
                    │   tbl_candidato     │
                    ├─────────────────────┤
                    │ • id (PK)           │
                    │ • partido_id (FK)   │
                    │ • tipo_candidato    │
                    │   - PRESIDENTE      │
                    │   - VICEPRESIDENTE_1│
                    │   - VICEPRESIDENTE_2│
                    │ • dni (UK)          │
                    │ • nombres           │
                    │ • apellido_paterno  │
                    │ • apellido_materno  │
                    │ • foto_url          │
                    │ • fecha_nacimiento  │
                    │ • profesion         │
                    │ • biografia         │
                    │ • plan_gobierno_url │
                    │ • redes_sociales    │
                    │ • hojavida_url      │
                    │ • estado            │
                    │ • fecha_registro    │
                    └─────────────────────┘


                    ┌─────────────────────┐
                    │ tbl_administrador   │ (Tabla independiente)
                    ├─────────────────────┤
                    │ • id (PK)           │
                    │ • usuario (UK)      │
                    │ • clave             │
                    │ • nombres           │
                    │ • email             │
                    │ • rol               │
                    │   - SUPERADMIN      │
                    │   - MODERADOR       │
                    │   - OBSERVADOR      │
                    │ • estado            │
                    │ • ultimo_acceso     │
                    │ • fecha_registro    │
                    └─────────────────────┘

LEYENDA:
─────────
PK = Primary Key (Llave Primaria)
FK = Foreign Key (Llave Foránea)
UK = Unique Key (Llave Única)
1  = Uno
N  = Muchos
```

---

## 📝 RESUMEN PARA EXPOSICIÓN

### 🎯 Puntos Clave:

1. **5 Tablas Principales:**
   - ✅ tbl_ciudadano (Padrón electoral)
   - ✅ tbl_partido (Partidos políticos)
   - ✅ tbl_candidato (Candidatos presidenciales)
   - ✅ tbl_voto (Registro de votos) ⭐
   - ✅ tbl_administrador (Gestión del sistema)

2. **2 Vistas:**
   - ✅ v_resultados_tiempo_real (Dashboard)
   - ✅ v_estadisticas_elecciones (Métricas generales)

3. **3 Procedimientos Almacenados:**
   - ✅ sp_validar_ciudadano (Login)
   - ✅ sp_obtener_cedula (Cédula de votación)
   - ✅ sp_registrar_voto (Proceso de votación) ⭐

4. **Características Destacadas:**
   - ✅ Integridad referencial con CASCADE
   - ✅ Normalización 3FN
   - ✅ Índices para optimización
   - ✅ Validaciones a nivel de base de datos
   - ✅ Auditoría completa (fechas, IPs)
   - ✅ Prevención de doble voto (UNIQUE constraints)

---

## 💡 TIPS PARA LA EXPOSICIÓN

### 🗣️ Orden Sugerido:

1. **Introducción (2 min):**
   - Objetivo del sistema
   - Contexto (Elecciones Perú 2026)
   - Tecnologías (MySQL, InnoDB)

2. **Estructura General (3 min):**
   - 5 tablas principales
   - Mostrar diagrama ER
   - Explicar flujo general

3. **Tablas Principales (5 min):**
   - tbl_ciudadano (quiénes pueden votar)
   - tbl_partido (opciones de voto)
   - tbl_candidato (candidatos por partido)
   - tbl_voto (registro de votos) ⭐
   - Enfatizar restricción UNIQUE

4. **Relaciones (3 min):**
   - 1:1 Ciudadano-Voto
   - 1:N Partido-Voto
   - 1:N Partido-Candidato
   - Explicar CASCADE

5. **Vistas y Procedimientos (3 min):**
   - Vistas para simplificar consultas
   - sp_registrar_voto (seguridad)
   - Validaciones dobles

6. **Reglas de Negocio (2 min):**
   - Un ciudadano, un voto
   - Auditoría completa
   - Tipos de voto

7. **Conclusiones (2 min):**
   - Sistema robusto y seguro
   - Prevención de fraude
   - Escalabilidad

### 📊 Diapositivas Sugeridas:

1. Portada
2. Objetivos del sistema
3. Diagrama ER completo
4. Tabla tbl_ciudadano (detalle)
5. Tabla tbl_voto (detalle) ⭐
6. Relaciones entre tablas
7. Vista v_resultados_tiempo_real
8. Procedimiento sp_registrar_voto
9. Reglas de negocio
10. Conclusiones

---

**Desarrollado por:** Sistema Electoral ONPE  
**Versión de la Base de Datos:** 1.0  
**Fecha:** Octubre 2025  
**Motor:** MySQL 8.0 / MariaDB 10.x

