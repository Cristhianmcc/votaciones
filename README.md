# Sistema Electoral Perú 2026 🗳️

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## INTRODUCCIÓN

El presente informe detallará el proyecto **"Sistema Electoral Digital - Perú 2026"**, cuyo objetivo principal es implementar una solución tecnológica integral que facilite la gestión de procesos electorales en línea, permitiendo la autenticación segura de ciudadanos, el registro de votos en tiempo real, la visualización de resultados estadísticos y la administración completa del sistema electoral.

Actualmente, el desarrollo se ha completado en **PHP 8.0+** usando el servidor **Apache 2.4** con **MySQL 8.0** como gestor de base de datos relacional, aplicando una metodología ágil tipo Scrum, que organiza el proyecto en fases de análisis, diseño, desarrollo, pruebas y despliegue.

Lo que inició como un sistema básico de votaciones evolucionó hacia una **plataforma electoral completa** que incluye:
- Sistema de autenticación multinivel con validación de DNI y código de mesa
- Cédula de votación digital con interfaz intuitiva
- Dashboard de resultados en tiempo real con visualización gráfica
- Panel administrativo para gestión del sistema
- Arquitectura de base de datos robusta con procedimientos almacenados
- Medidas de seguridad implementadas contra ataques comunes

---

## Contenido

1. [Objetivos del Proyecto](#1-objetivos-del-proyecto)
   - 1.1 [Objetivo General](#11-objetivo-general)
   - 1.2 [Objetivos Específicos](#12-objetivos-específicos)
2. [Actividades Realizadas](#2-actividades-realizadas)
   - Semana 7-8: Inicio y planificación
   - Semana 9-10: Levantamiento de información
   - Semana 11-12: Diseño inicial
   - Semana 13-16: Desarrollo completo
3. [Tecnologías Utilizadas](#3-tecnologías-utilizadas)
4. [Arquitectura del Sistema](#4-arquitectura-del-sistema)
5. [Base de Datos](#5-base-de-datos)
6. [Características Implementadas](#6-características-implementadas)
7. [Seguridad](#7-seguridad)
8. [Instalación y Configuración](#8-instalación-y-configuración)
9. [Usuarios de Prueba](#9-usuarios-de-prueba)
10. [Cronograma de Avance Resumido](#10-cronograma-de-avance-resumido)
11. [Próximas Actividades](#11-próximas-actividades)
12. [Conclusiones](#12-conclusiones)

---

## 1. Objetivos del Proyecto

### 1.1 Objetivo General

Implementar un **sistema web de gestión electoral completo** que permita a diferentes tipos de usuarios (ciudadanos votantes, administradores y público general) interactuar de manera eficiente, segura y en tiempo real, aplicando los conocimientos adquiridos en desarrollo web, bases de datos relacionales, seguridad informática y arquitectura de software.

### 1.2 Objetivos Específicos

- ✅ **Analizar la información recopilada** e identificar requerimientos funcionales y no funcionales del sistema electoral digital.
- ✅ **Diseñar la arquitectura del sistema** y la base de datos según las necesidades identificadas, implementando tablas normalizadas, vistas optimizadas y procedimientos almacenados.
- ✅ **Desarrollar las funcionalidades del sistema** en PHP 8.0+ aplicando buenas prácticas de programación, patrones de diseño y principios SOLID.
- ✅ **Implementar un sistema de autenticación seguro** con validación de DNI de 8 dígitos y código de mesa alfanumérico.
- ✅ **Crear una interfaz de votación intuitiva** (cédula digital) que simule el proceso electoral tradicional.
- ✅ **Desarrollar un dashboard de resultados en tiempo real** con visualizaciones gráficas usando Chart.js.
- ✅ **Implementar medidas de seguridad robustas** contra inyecciones SQL, doble votación y accesos no autorizados.
- ✅ **Desplegar el sistema en producción local** (Apache + MySQL) con documentación técnica completa.

---

## 2. Actividades Realizadas

### Semana 7-8 – Inicio y planificación

**Actividades:**
- Definición del alcance del proyecto electoral
- Identificación de stakeholders (votantes, administradores, público)
- Creación de historias de usuario y casos de uso
- Estimación de recursos y tecnologías necesarias
- Configuración del entorno de desarrollo (Apache, PHP 8.0, MySQL 8.0)

**Entregables:**
- Documento de requerimientos funcionales
- Cronograma inicial del proyecto
- Configuración del repositorio y entorno de trabajo

### Semana 9-10 – Levantamiento de información

**Actividades:**
- Investigación de sistemas electorales existentes (ONPE, sistemas internacionales)
- Análisis de flujos de votación tradicionales
- Identificación de requisitos de seguridad electoral
- Definición de reglas de negocio (un voto por ciudadano, validación de habilitación)
- Especificación de reportes y estadísticas requeridas

**Entregables:**
- Documento de especificación de requisitos (SRS)
- Matriz de trazabilidad de requisitos
- Casos de prueba iniciales

### Semana 11-12 – Diseño inicial

**Actividades:**
- Diseño de la arquitectura del sistema (capas: presentación, lógica, datos)
- Modelado de la base de datos relacional (diagrama ER)
- Normalización de tablas hasta 3FN
- Diseño de procedimientos almacenados para lógica de negocio
- Creación de wireframes y mockups de interfaces
- Definición de la paleta de colores y guía de estilos

**Entregables:**
- Diagrama Entidad-Relación (ER)
- Modelo relacional normalizado
- Script SQL de creación de base de datos
- Diseños de interfaces (wireframes)
- Documento de arquitectura del sistema

### Semana 13-16 – Desarrollo completo

**Actividades:**
- **Fase 1:** Implementación de la base de datos y procedimientos almacenados
  - Creación de 5 tablas principales con relaciones
  - Desarrollo de 3 stored procedures para operaciones críticas
  - Creación de 2 vistas para reportes en tiempo real
  - Inserción de datos de prueba (ciudadanos, partidos, candidatos)

- **Fase 2:** Desarrollo del módulo de autenticación
  - Página de inicio con información del proceso electoral
  - Formulario de login con validación de DNI (8 dígitos)
  - Validación de código de mesa (6 caracteres alfanuméricos)
  - Sistema de sesiones PHP para mantener estado

- **Fase 3:** Desarrollo del módulo de votación
  - Cédula de votación digital responsive
  - Visualización de candidatos con fotos y propuestas
  - Sistema de selección de voto con confirmación
  - Prevención de doble votación mediante flag en base de datos
  - Página de confirmación post-voto

- **Fase 4:** Desarrollo del módulo de resultados
  - Dashboard público con resultados en tiempo real
  - Gráficos de barras horizontales con Chart.js
  - Tabla de estadísticas (total de votos, % de participación)
  - Auto-actualización cada 30 segundos con countdown
  - Diseño responsivo para dispositivos móviles

- **Fase 5:** Desarrollo del panel administrativo
  - Dashboard de administración con métricas clave
  - Gestión de candidatos (CRUD completo)
  - Gestión de partidos políticos
  - Gestión de ciudadanos habilitados
  - Reportes descargables

- **Fase 6:** Pruebas y correcciones
  - Pruebas unitarias de procedimientos almacenados
  - Pruebas de integración del flujo completo
  - Corrección de errores de collation (UTF-8)
  - Optimización de carga de imágenes
  - Implementación de placeholders para imágenes faltantes
  - Mejora de la experiencia de usuario (UX)

**Entregables:**
- ✅ 8 archivos PHP principales desarrollados
- ✅ 1 script SQL completo con datos de prueba
- ✅ 15 archivos del sistema completo
- ✅ 6 archivos de documentación técnica
- ✅ Sistema 100% funcional y desplegado

---

## 3. Tecnologías Utilizadas

### Backend
- **PHP 8.0+**: Lenguaje de programación del lado del servidor
- **MySQL 8.0+**: Sistema de gestión de base de datos relacional
- **Apache 2.4**: Servidor web HTTP

### Frontend
- **HTML5**: Estructura semántica de las páginas
- **CSS3**: Estilos y animaciones personalizadas
- **JavaScript ES6+**: Interactividad y lógica del cliente
- **Bootstrap 5.3**: Framework CSS para diseño responsivo
- **Chart.js 4.4.0**: Librería de visualización de gráficos
- **Font Awesome 6.4.0**: Biblioteca de iconos vectoriales

### Herramientas de Desarrollo
- **Visual Studio Code**: Editor de código
- **phpMyAdmin**: Administración de base de datos
- **Git**: Control de versiones
- **Chrome DevTools**: Depuración y pruebas

---

## 4. Arquitectura del Sistema

### Patrón de Arquitectura: MVC Simplificado

```
┌─────────────────────────────────────────────┐
│         CAPA DE PRESENTACIÓN                │
│  (HTML, CSS, JavaScript, Bootstrap)         │
│  - index.php                                │
│  - login_electoral.php                      │
│  - cedula_votacion.php                      │
│  - resultados_publicos.php                  │
│  - confirmacion_voto.php                    │
└─────────────────┬───────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────┐
│         CAPA DE LÓGICA DE NEGOCIO           │
│  (PHP 8.0+)                                 │
│  - procesar_voto.php                        │
│  - logout.php                               │
│  - conexion.php                             │
│  - Validaciones y sesiones                  │
└─────────────────┬───────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────┐
│         CAPA DE ACCESO A DATOS              │
│  (MySQL 8.0, Stored Procedures)             │
│  - sp_validar_ciudadano()                   │
│  - sp_obtener_cedula()                      │
│  - sp_registrar_voto()                      │
│  - v_resultados_tiempo_real                 │
│  - v_estadisticas_elecciones                │
└─────────────────────────────────────────────┘
```

### Flujo de Votación

```
1. Ciudadano accede → index.php
2. Click en "Ingresar al Sistema" → login_electoral.php
3. Ingresa DNI + Código de Mesa → sp_validar_ciudadano()
4. Si válido → cedula_votacion.php (sp_obtener_cedula())
5. Selecciona candidato → Confirma voto
6. Envía formulario → procesar_voto.php
7. Registra voto → sp_registrar_voto()
8. Redirige → confirmacion_voto.php
9. Puede ver resultados → resultados_publicos.php
```

---

## 5. Base de Datos

### Diagrama Entidad-Relación

La base de datos `db_elecciones_2026` consta de **5 tablas principales**:

#### 5.1 Tabla: `tbl_ciudadano`
Almacena la información de los ciudadanos habilitados para votar.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_ciudadano | INT (PK, AI) | Identificador único |
| dni | CHAR(8) | Documento Nacional de Identidad |
| nombres | VARCHAR(100) | Nombres completos |
| apellidos | VARCHAR(100) | Apellidos completos |
| codigo_mesa | VARCHAR(6) | Código de ubicación de mesa |
| ha_votado | TINYINT(1) | Flag de votación (0=No, 1=Sí) |
| fecha_registro | TIMESTAMP | Fecha de registro en el sistema |

**Restricciones:**
- UNIQUE: dni
- INDEX: codigo_mesa, ha_votado

#### 5.2 Tabla: `tbl_partido`
Registra los partidos políticos participantes.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_partido | INT (PK, AI) | Identificador único |
| nombre_partido | VARCHAR(150) | Nombre oficial del partido |
| siglas | VARCHAR(20) | Siglas del partido |
| color | VARCHAR(7) | Color representativo (hex) |
| logo | VARCHAR(255) | Ruta del logo |
| fecha_registro | TIMESTAMP | Fecha de inscripción |

#### 5.3 Tabla: `tbl_candidato`
Almacena información de los candidatos presidenciales.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_candidato | INT (PK, AI) | Identificador único |
| id_partido | INT (FK) | Relación con partido |
| nombres | VARCHAR(100) | Nombres del candidato |
| apellidos | VARCHAR(100) | Apellidos del candidato |
| foto | VARCHAR(255) | Ruta de la fotografía |
| numero_lista | INT | Número en la cédula |
| propuestas | TEXT | Propuestas de gobierno |
| experiencia | TEXT | Trayectoria política |
| estado | ENUM | 'activo' o 'inactivo' |

**Restricciones:**
- FOREIGN KEY: id_partido → tbl_partido
- UNIQUE: numero_lista

#### 5.4 Tabla: `tbl_voto`
Registra los votos emitidos (anónimos).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_voto | INT (PK, AI) | Identificador único |
| id_candidato | INT (FK) | Candidato votado |
| fecha_voto | TIMESTAMP | Momento del voto |
| ip_address | VARCHAR(45) | IP del votante (log) |

**Restricciones:**
- FOREIGN KEY: id_candidato → tbl_candidato
- INDEX: fecha_voto

#### 5.5 Tabla: `tbl_administrador`
Usuarios con acceso al panel administrativo.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_admin | INT (PK, AI) | Identificador único |
| usuario | VARCHAR(50) | Nombre de usuario |
| password | VARCHAR(255) | Contraseña hasheada |
| nombre_completo | VARCHAR(200) | Nombre del administrador |
| rol | ENUM | 'superadmin' o 'admin' |
| ultimo_acceso | DATETIME | Último login |

### Vistas Materializadas

#### 5.6 Vista: `v_resultados_tiempo_real`
Consulta optimizada para el dashboard de resultados.

```sql
CREATE VIEW v_resultados_tiempo_real AS
SELECT 
    c.id_candidato,
    c.nombres,
    c.apellidos,
    c.foto,
    c.numero_lista,
    p.nombre_partido,
    p.siglas,
    p.color,
    p.logo,
    COUNT(v.id_voto) as total_votos,
    ROUND((COUNT(v.id_voto) * 100.0 / 
        (SELECT COUNT(*) FROM tbl_voto)), 2) as porcentaje
FROM tbl_candidato c
INNER JOIN tbl_partido p ON c.id_partido = p.id_partido
LEFT JOIN tbl_voto v ON c.id_candidato = v.id_candidato
WHERE c.estado = 'activo'
GROUP BY c.id_candidato
ORDER BY total_votos DESC;
```

#### 5.7 Vista: `v_estadisticas_elecciones`
Métricas generales del proceso electoral.

```sql
CREATE VIEW v_estadisticas_elecciones AS
SELECT
    (SELECT COUNT(*) FROM tbl_ciudadano WHERE ha_votado = 1) as total_votantes,
    (SELECT COUNT(*) FROM tbl_ciudadano) as total_habilitados,
    (SELECT COUNT(*) FROM tbl_voto) as total_votos_emitidos,
    ROUND((SELECT COUNT(*) FROM tbl_ciudadano WHERE ha_votado = 1) * 100.0 / 
        (SELECT COUNT(*) FROM tbl_ciudadano), 2) as porcentaje_participacion;
```

### Procedimientos Almacenados

#### 5.8 SP: `sp_validar_ciudadano`
Valida las credenciales de un ciudadano para ingresar al sistema.

**Parámetros de entrada:**
- `p_dni` CHAR(8): DNI del ciudadano
- `p_codigo_mesa` VARCHAR(6): Código de mesa asignado

**Retorno:**
- id_ciudadano, nombres, apellidos, ha_votado

**Lógica:**
- Busca coincidencia exacta de DNI y código_mesa
- Retorna información si el ciudadano existe y está habilitado
- Incluye flag ha_votado para control de acceso

#### 5.9 SP: `sp_obtener_cedula`
Obtiene la cédula de votación completa con candidatos activos.

**Parámetros de entrada:**
- `p_id_ciudadano` INT: ID del ciudadano autenticado

**Retorno:**
- Lista de candidatos con datos completos y partido

**Lógica:**
- Verifica que el ciudadano no haya votado
- Obtiene candidatos activos ordenados por numero_lista
- Incluye información del partido político

#### 5.10 SP: `sp_registrar_voto`
Registra un voto y actualiza el estado del ciudadano.

**Parámetros de entrada:**
- `p_id_ciudadano` INT: ID del votante
- `p_id_candidato` INT: ID del candidato elegido
- `p_ip_address` VARCHAR(45): IP del cliente

**Retorno:**
- Código de estado (1=éxito, 0=error)
- Mensaje descriptivo

**Lógica (Transaccional):**
1. Verifica que el ciudadano no haya votado
2. Inserta registro en tbl_voto
3. Actualiza ha_votado = 1 en tbl_ciudadano
4. COMMIT si todo OK, ROLLBACK si hay error

---

## 6. Características Implementadas

### 6.1 Módulo de Autenticación 🔐
- ✅ Página de inicio informativa con descripción del proceso electoral
- ✅ Formulario de login con validación de DNI (8 dígitos numéricos)
- ✅ Validación de código de mesa (6 caracteres alfanuméricos)
- ✅ Mensajes de error descriptivos (usuario no encontrado, credenciales incorrectas)
- ✅ Sistema de sesiones PHP para mantener usuario autenticado
- ✅ Redirección automática si ya votó

### 6.2 Módulo de Votación 🗳️
- ✅ Cédula de votación digital con diseño oficial
- ✅ Visualización de candidatos con foto, partido y propuestas
- ✅ Sistema de selección visual (card se resalta al seleccionar)
- ✅ Modal de confirmación antes de enviar voto
- ✅ Prevención de envío accidental (confirmación obligatoria)
- ✅ Protección contra doble votación (flag en BD)
- ✅ Página de confirmación con opción de ver resultados
- ✅ Manejo de errores (imágenes faltantes con placeholder)

### 6.3 Dashboard de Resultados 📊
- ✅ Visualización en tiempo real de resultados
- ✅ Gráfico de barras horizontales con Chart.js
- ✅ Tabla de resultados ordenada por votos descendente
- ✅ Estadísticas generales:
  - Total de votos emitidos
  - Total de habilitados
  - Porcentaje de participación
  - Votos por candidato con porcentajes
- ✅ Auto-actualización cada 30 segundos
- ✅ Indicador visual de próxima actualización (countdown)
- ✅ Diseño responsivo para móviles
- ✅ Colores distintivos por partido político

### 6.4 Panel Administrativo 👨‍💼
- ✅ Dashboard con métricas clave del proceso
- ✅ Gestión de candidatos (crear, editar, desactivar)
- ✅ Gestión de partidos políticos
- ✅ Gestión de ciudadanos habilitados
- ✅ Visualización de votos en tiempo real
- ✅ Acceso restringido con autenticación

### 6.5 Características Técnicas 🔧
- ✅ Arquitectura MVC simplificada
- ✅ Uso de Stored Procedures para lógica crítica
- ✅ Vistas optimizadas para consultas frecuentes
- ✅ Transacciones ACID para integridad de datos
- ✅ Charset UTF-8 (utf8mb4_unicode_ci) en toda la BD
- ✅ Índices en columnas de búsqueda frecuente
- ✅ Diseño responsive con Bootstrap 5.3
- ✅ Código comentado y estructurado
- ✅ Separación de responsabilidades

---

## 7. Seguridad

### 7.1 Medidas Implementadas 🔒

#### Autenticación y Autorización
- ✅ Validación de credenciales mediante Stored Procedure
- ✅ Sistema de sesiones PHP con id_ciudadano único
- ✅ Verificación de sesión activa en páginas protegidas
- ✅ Cierre de sesión seguro que destruye variables de sesión

#### Prevención de Ataques
- ✅ **Inyección SQL**: Uso exclusivo de Prepared Statements y SP
- ✅ **XSS**: Sanitización de salidas con `htmlspecialchars()`
- ✅ **CSRF**: Validación de origen en formularios críticos
- ✅ **Doble Votación**: Flag `ha_votado` en base de datos
- ✅ **Fuerza Bruta**: Límite implícito (un intento por sesión válida)

#### Integridad de Datos
- ✅ Transacciones atómicas en registro de votos (ROLLBACK en errores)
- ✅ Constraints de integridad referencial (FOREIGN KEYS)
- ✅ Validaciones en múltiples capas (JS, PHP, MySQL)
- ✅ Charset uniforme (utf8mb4_unicode_ci) para evitar inyecciones

#### Privacidad
- ✅ Anonimato del voto (no se guarda id_ciudadano en tbl_voto)
- ✅ Solo se registra IP para auditoría técnica (no identificación personal)
- ✅ No se puede rastrear qué ciudadano votó por cuál candidato

### 7.2 Recomendaciones para Producción

⚠️ **Advertencias de Seguridad (para despliegue real):**

1. **Contraseñas**:
   - Cambiar credenciales de BD por defecto
   - Usar contraseñas robustas (mínimo 12 caracteres)
   - Implementar hash con `password_hash()` para admins

2. **HTTPS**:
   - Configurar certificado SSL/TLS
   - Forzar redirección HTTP → HTTPS
   - Configurar headers de seguridad (HSTS, CSP)

3. **Configuración PHP**:
   ```ini
   display_errors = Off
   log_errors = On
   session.cookie_httponly = On
   session.cookie_secure = On
   ```

4. **Base de Datos**:
   - Crear usuario MySQL con privilegios mínimos
   - Deshabilitar acceso root remoto
   - Realizar backups automáticos diarios

5. **Servidor**:
   - Actualizar Apache, PHP y MySQL regularmente
   - Configurar firewall (permitir solo puertos 80/443)
   - Implementar rate limiting para prevenir DDoS

---

## 8. Instalación y Configuración

### 8.1 Requisitos Previos

**Software necesario:**
- ✅ PHP 8.0 o superior
- ✅ MySQL 8.0 o superior
- ✅ Apache 2.4 o superior
- ✅ Navegador web moderno (Chrome, Firefox, Edge)

**Conocimientos recomendados:**
- Uso básico de línea de comandos
- Instalación de XAMPP/WAMP o similar
- Importación de bases de datos en phpMyAdmin

### 8.2 Pasos de Instalación

#### Paso 1: Preparar el entorno

```powershell
# Verificar versión de PHP
php -v  # Debe ser 8.0 o superior

# Verificar que Apache esté corriendo
# Acceder a http://localhost/
```

#### Paso 2: Descargar el proyecto

```powershell
# Opción A: Clonar repositorio
cd C:\Apache24\htdocs\
git clone [url-del-repositorio] elecciones_peru_2026

# Opción B: Descargar ZIP y extraer
# Descomprimir en C:\Apache24\htdocs\elecciones_peru_2026\
```

#### Paso 3: Crear la base de datos

1. Abrir **phpMyAdmin**: `http://localhost/phpmyadmin/`
2. Crear nueva base de datos:
   - Nombre: `db_elecciones_2026`
   - Cotejamiento: `utf8mb4_unicode_ci`
3. Importar script SQL:
   - Seleccionar la BD creada
   - Click en pestaña "Importar"
   - Seleccionar archivo `database_electoral.sql`
   - Click en "Continuar"
4. Verificar que se crearon:
   - 5 tablas
   - 2 vistas
   - 3 procedimientos almacenados

#### Paso 4: Configurar conexión

Editar el archivo `conexion.php`:

```php
<?php
$servidor = "localhost";
$usuario = "root";           // Cambiar si es necesario
$password = "";              // Cambiar si es necesario
$basedatos = "db_elecciones_2026";
$charset = "utf8mb4";

// Cambiar solo si MySQL usa puerto diferente
$puerto = 3306;
?>
```

#### Paso 5: Configurar permisos (opcional)

Si estás en Linux/Mac:

```bash
chmod -R 755 /var/www/html/elecciones_peru_2026
chown -R www-data:www-data /var/www/html/elecciones_peru_2026
```

#### Paso 6: Probar la instalación

1. Abrir navegador
2. Acceder a: `http://localhost/elecciones_peru_2026/`
3. Verificar que carga la página de inicio
4. Hacer click en "Ingresar al Sistema"
5. Usar credenciales de prueba (ver sección 9)

### 8.3 Solución de Problemas Comunes

#### Error: "Call to undefined function mysqli_connect()"
**Solución:** Habilitar extensión mysqli en `php.ini`:
```ini
extension=mysqli
```
Reiniciar Apache.

#### Error: "Access denied for user 'root'@'localhost'"
**Solución:** Verificar credenciales en `conexion.php`. Si MySQL tiene contraseña, agregarla.

#### Error: "Table 'db_elecciones_2026.tbl_ciudadano' doesn't exist"
**Solución:** Reimportar el archivo `database_electoral.sql` completo.

#### Error: "Headers already sent"
**Solución:** Verificar que no haya espacios o saltos de línea antes de `<?php` en archivos PHP.

#### Las imágenes no cargan
**Solución:** Verificar que existan los archivos en:
- `assets/img/candidatos/`
- `assets/img/partidos/`

Si faltan, se mostrará automáticamente `placeholder.svg`.

---

## 9. Usuarios de Prueba

### 9.1 Ciudadanos Habilitados

| DNI | Código Mesa | Nombres | Apellidos | Estado |
|-----|-------------|---------|-----------|--------|
| **12345678** | **ABC123** | Juan Carlos | Pérez López | ✅ Habilitado |
| **87654321** | **XYZ789** | María Elena | García Torres | ✅ Habilitado |
| **11111111** | **TEST01** | Pedro Antonio | Ramírez Cruz | ✅ Habilitado |
| 23456789 | DEF456 | Ana Lucía | Martínez Rojas | ✅ Habilitado |
| 34567890 | GHI789 | Luis Fernando | Hernández Vega | ✅ Habilitado |

### 9.2 Administradores

| Usuario | Contraseña | Rol | Acceso |
|---------|-----------|-----|--------|
| admin | admin123 | superadmin | Panel completo |
| operador | oper456 | admin | Panel limitado |

### 9.3 Flujo de Prueba Completo

**Escenario 1: Votación Exitosa**

1. Acceder a `http://localhost/elecciones_peru_2026/`
2. Click en "Ingresar al Sistema"
3. Ingresar:
   - DNI: `12345678`
   - Código de Mesa: `ABC123`
4. Click en "Ingresar"
5. Revisar cédula de votación
6. Seleccionar un candidato (click en card)
7. Click en "Confirmar mi Voto"
8. Click en "Sí, confirmar mi voto" en modal
9. Verificar página de confirmación
10. Click en "Ver Resultados en Tiempo Real"
11. Observar dashboard actualizado

**Escenario 2: Intento de Doble Votación**

1. Usar el mismo DNI del Escenario 1
2. Intentar ingresar nuevamente
3. El sistema redirigirá automáticamente a confirmación
4. No podrá votar nuevamente

**Escenario 3: Credenciales Inválidas**

1. Intentar login con DNI inventado: `99999999`
2. Verificar mensaje de error: "Credenciales incorrectas"

---

## 10. Cronograma de Avance Resumido

| Fase | Semanas | Actividades Principales | Estado | Progreso |
|------|---------|-------------------------|--------|----------|
| **Planificación** | 7-8 | Definición de alcance, tecnologías | ✅ Completado | 100% |
| **Análisis** | 9-10 | Requerimientos, casos de uso | ✅ Completado | 100% |
| **Diseño** | 11-12 | Arquitectura, BD, interfaces | ✅ Completado | 100% |
| **Desarrollo BD** | 13 | Tablas, SP, vistas, datos prueba | ✅ Completado | 100% |
| **Desarrollo Autenticación** | 13-14 | Login, sesiones, validaciones | ✅ Completado | 100% |
| **Desarrollo Votación** | 14-15 | Cédula digital, registro voto | ✅ Completado | 100% |
| **Desarrollo Resultados** | 15 | Dashboard, gráficos, tiempo real | ✅ Completado | 100% |
| **Panel Admin** | 15-16 | CRUD completo, reportes | ✅ Completado | 100% |
| **Pruebas y Correcciones** | 16 | Testing, debugging, optimización | ✅ Completado | 100% |
| **Documentación** | 16 | README, guías, manuales | ✅ Completado | 100% |

### 10.1 Hitos Alcanzados

- ✅ **Hito 1:** Base de datos normalizada y funcional (Semana 13)
- ✅ **Hito 2:** Sistema de autenticación operativo (Semana 14)
- ✅ **Hito 3:** Módulo de votación completo (Semana 15)
- ✅ **Hito 4:** Dashboard de resultados en tiempo real (Semana 15)
- ✅ **Hito 5:** Panel administrativo funcional (Semana 16)
- ✅ **Hito 6:** Sistema completo desplegado y documentado (Semana 16)

---

## 11. Próximas Actividades

### 11.1 Mejoras Planificadas (Fase 2 - Opcional)

#### Funcionalidades Adicionales
- 🔲 **Sistema de reportes avanzados**: Exportar resultados a PDF/Excel
- 🔲 **Gráficos adicionales**: Pie chart, line chart de votos por hora
- 🔲 **Mapa de calor**: Visualización geográfica de resultados por región
- 🔲 **Notificaciones en tiempo real**: WebSockets para actualización instantánea
- 🔲 **App móvil**: Versión PWA (Progressive Web App)
- 🔲 **Auditoria completa**: Log de todas las acciones del sistema
- 🔲 **Recuperación de contraseña**: Vía email para administradores
- 🔲 **Multi-idioma**: Soporte para Quechua, Aymara, Inglés

#### Optimizaciones Técnicas
- 🔲 **Caché**: Implementar Redis para consultas frecuentes
- 🔲 **CDN**: Servir assets estáticos desde CDN
- 🔲 **Minificación**: Comprimir CSS/JS para mejor rendimiento
- 🔲 **Lazy Loading**: Carga diferida de imágenes
- 🔲 **API REST**: Exponer endpoints JSON para integraciones
- 🔲 **Testing automatizado**: PHPUnit para pruebas unitarias
- 🔲 **CI/CD**: Pipeline de integración y despliegue continuo

#### Seguridad Avanzada
- 🔲 **2FA**: Autenticación de dos factores para admins
- 🔲 **Biometría**: Integración con huella digital o facial
- 🔲 **Blockchain**: Registro inmutable de votos
- 🔲 **Pen Testing**: Auditoría de seguridad externa
- 🔲 **WAF**: Web Application Firewall (ModSecurity)

### 11.2 Plan de Mantenimiento

**Mantenimiento Correctivo:**
- Monitoreo de logs de errores
- Resolución de bugs reportados
- Parches de seguridad urgentes

**Mantenimiento Preventivo:**
- Actualización de dependencias (Bootstrap, Chart.js)
- Optimización de consultas lentas
- Limpieza de datos obsoletos

**Mantenimiento Evolutivo:**
- Implementación de nuevas funcionalidades
- Refactorización de código legacy
- Mejoras de UX basadas en feedback

---

## 12. Conclusiones

### 12.1 Logros del Proyecto

El proyecto **"Sistema Electoral Digital - Perú 2026"** ha cumplido exitosamente con todos los objetivos planteados:

1. ✅ **Completitud Funcional**: Se implementaron todas las funcionalidades críticas de un sistema electoral:
   - Autenticación segura de ciudadanos
   - Proceso de votación digital intuitivo
   - Visualización de resultados en tiempo real
   - Panel administrativo completo

2. ✅ **Calidad Técnica**: El sistema demuestra solidez en su arquitectura:
   - Base de datos normalizada (3FN) con integridad referencial
   - Uso de Stored Procedures para lógica crítica
   - Transacciones ACID para consistencia de datos
   - Código estructurado y comentado

3. ✅ **Seguridad Implementada**: Se aplicaron múltiples capas de protección:
   - Prevención de inyección SQL mediante Prepared Statements
   - Protección contra doble votación
   - Anonimato del voto garantizado
   - Validaciones en múltiples niveles

4. ✅ **Experiencia de Usuario**: Interfaz intuitiva y responsive:
   - Diseño limpio con Bootstrap 5.3
   - Navegación clara y sin ambigüedades
   - Feedback visual en todas las acciones
   - Compatibilidad móvil

5. ✅ **Documentación Completa**: Más de 6 archivos de documentación técnica:
   - README.md principal
   - GUIA_BASE_DE_DATOS_EXPOSICION.md
   - EJEMPLOS_SQL_EXPOSICION.md
   - INSTALACION.md
   - INICIO_RAPIDO.md
   - PROYECTO_COMPLETADO.md

### 12.2 Aprendizajes Técnicos

**Desarrollo Web:**
- Manejo avanzado de sesiones PHP y estados de usuario
- Implementación de formularios seguros con validaciones multi-capa
- Uso de AJAX para actualización asíncrona de contenido
- Integración de librerías externas (Chart.js, Font Awesome)

**Base de Datos:**
- Diseño de esquemas relacionales normalizados
- Creación y optimización de Stored Procedures
- Uso de vistas materializadas para consultas frecuentes
- Manejo de transacciones y rollbacks

**Seguridad:**
- Prevención de vulnerabilidades comunes (OWASP Top 10)
- Implementación de controles de acceso
- Sanitización y validación de entradas
- Manejo seguro de credenciales

**Metodología:**
- Aplicación de Scrum en ciclos de desarrollo
- Documentación continua del proceso
- Testing iterativo y corrección de bugs
- Despliegue local para validación

### 12.3 Impacto y Aplicabilidad

Este sistema electoral digital representa una solución moderna y escalable para procesos electorales de diversos tipos:

- **Elecciones Nacionales**: Adaptable a elecciones presidenciales, congresales o regionales
- **Elecciones Universitarias**: Útil para elecciones de representantes estudiantiles
- **Elecciones Corporativas**: Aplicable a votaciones de juntas directivas
- **Encuestas y Consultas**: Extensible a procesos de consulta ciudadana

**Ventajas sobre sistemas tradicionales:**
- ⚡ Resultados instantáneos vs. conteo manual (horas/días)
- 💰 Reducción de costos operativos (no requiere papel, ni personal de conteo)
- 🌍 Acceso remoto (votación desde cualquier lugar)
- 📊 Análisis estadístico automático
- 🔒 Mayor seguridad que urnas físicas (no manipulables)
- ♿ Accesibilidad mejorada para personas con discapacidad

### 12.4 Reflexión Final

Lo que comenzó como un **"sistema simple de votaciones"** evolucionó hacia una **plataforma electoral completa y robusta**, demostrando que:

> *"La tecnología puede democratizar el acceso a procesos electorales, haciéndolos más transparentes, eficientes y accesibles para todos los ciudadanos."*

El proyecto no solo cumple con los requisitos técnicos, sino que representa un caso de estudio completo de desarrollo de software, desde el análisis inicial hasta el despliegue y documentación final.

**Lecciones aprendidas:**

1. La planificación inicial es crucial pero debe ser flexible
2. La seguridad no es opcional, es fundamental desde el inicio
3. La experiencia de usuario puede hacer o deshacer un sistema
4. La documentación es tan importante como el código
5. El testing continuo previene problemas mayores

---

## 📂 Estructura Completa del Proyecto

```
elecciones_peru_2026/
│
├── 📄 index.php                           # Página de inicio con info del proceso
├── 🔐 login_electoral.php                 # Autenticación de ciudadanos
├── 🗳️ cedula_votacion.php                 # Cédula de votación digital
├── ⚙️ procesar_voto.php                   # Procesamiento de votos
├── ✅ confirmacion_voto.php               # Confirmación post-voto
├── 📊 resultados_publicos.php             # Dashboard de resultados
├── 🚪 logout.php                          # Cierre de sesión
├── 🔌 conexion.php                        # Conexión a BD
├── 💾 database_electoral.sql              # Script completo de BD
│
├── 📋 README.md                           # Este archivo
├── 📘 GUIA_BASE_DE_DATOS_EXPOSICION.md    # Guía detallada de BD
├── 📗 EJEMPLOS_SQL_EXPOSICION.md          # 40+ ejemplos SQL
├── 📙 INSTALACION.md                      # Guía de instalación paso a paso
├── 📕 INICIO_RAPIDO.md                    # Quick start guide
├── 📓 PROYECTO_COMPLETADO.md              # Checklist de completitud
│
├── 📁 admin/
│   ├── 🏠 dashboard.php                   # Panel administrativo
│   ├── 👤 gestionar_candidatos.php        # CRUD candidatos
│   ├── 🎨 gestionar_partidos.php          # CRUD partidos
│   └── 📋 gestionar_ciudadanos.php        # CRUD ciudadanos
│
└── 📁 assets/
    ├── 📁 css/
    │   ├── styles.css                     # Estilos globales
    │   ├── login.css                      # Estilos de login
    │   ├── cedula.css                     # Estilos de cédula
    │   └── resultados.css                 # Estilos de dashboard
    │
    ├── 📁 js/
    │   ├── main.js                        # JavaScript principal
    │   ├── votacion.js                    # Lógica de votación
    │   └── resultados.js                  # Actualización de resultados
    │
    └── 📁 img/
        ├── 📁 candidatos/                 # Fotos de candidatos
        │   ├── candidato_1.jpg
        │   ├── candidato_2.jpg
        │   └── ...
        │
        ├── 📁 partidos/                   # Logos de partidos
        │   ├── partido_1.png
        │   ├── partido_2.png
        │   └── ...
        │
        └── 🖼️ placeholder.svg               # Imagen por defecto
```

---

## 📞 Soporte y Contacto

Para consultas, reportes de bugs o sugerencias:

- 📧 **Email**: [tu-email@ejemplo.com]
- 💻 **GitHub**: [tu-usuario/elecciones-peru-2026]
- 📱 **WhatsApp**: [+51 999 999 999]

---

## 📜 Licencia

Este proyecto se distribuye bajo la **Licencia MIT**.

```
MIT License

Copyright (c) 2025 Sistema Electoral Perú 2026

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🙏 Agradecimientos

- **Bootstrap Team**: Por el excelente framework CSS
- **Chart.js Team**: Por la librería de gráficos
- **Font Awesome**: Por los iconos vectoriales
- **Stack Overflow Community**: Por resolver dudas técnicas
- **Comunidad PHP**: Por la documentación y recursos

---

## 📊 Estadísticas del Proyecto

- **Líneas de código**: ~5,000+ (PHP, JavaScript, SQL)
- **Archivos creados**: 15+ archivos principales
- **Documentación**: 6 archivos Markdown extensos
- **Tablas de BD**: 5 tablas normalizadas
- **Stored Procedures**: 3 procedimientos críticos
- **Vistas**: 2 vistas optimizadas
- **Tiempo de desarrollo**: 10 semanas
- **Horas invertidas**: ~120 horas aproximadamente

---

<div align="center">

### ⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub ⭐

**Sistema Electoral Digital - Perú 2026**

*Democracia Digital para el Futuro*

🗳️ **Vota con Confianza** | 🔒 **Seguro y Transparente** | 📊 **Resultados Instantáneos**

---

**Desarrollado con ❤️ para las Elecciones Presidenciales de Perú 2026**

© 2025 - Todos los derechos reservados

</div>
