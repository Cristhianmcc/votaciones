# 📋 Manual del Panel de Administración - Sistema Electoral Perú 2026

## 🔐 Acceso al Panel

**URL:** `http://localhost/elecciones_peru_2026/admin/`

**Credenciales predeterminadas:**
- Usuario: `admin`
- Contraseña: `admin123`

---

## 🎯 Módulos del Sistema

### 1️⃣ **Gestión de Partidos Políticos**

**Ruta:** `/admin/gestionar_partidos.php`

#### Funcionalidades:
✅ **Crear Nuevo Partido**
- Siglas (ej: AP, FP, APP)
- Nombre completo del partido
- Color primario (selector de color)
- Color secundario (opcional)
- Logo automático en: `assets/img/partidos/[siglas].png`

✅ **Editar Partido**
- Modificar datos del partido
- Cambiar colores

✅ **Eliminar Partido**
- ⚠️ **CUIDADO:** Al eliminar un partido, se eliminan sus candidatos asociados

#### Ejemplo de uso:
1. Clic en "Nuevo Partido"
2. Ingresar: `AP` | `Acción Popular` | Color: `#0033A0`
3. Guardar
4. Subir logo a: `assets/img/partidos/ap.png`

---

### 2️⃣ **Gestión de Candidatos**

**Ruta:** `/admin/gestionar_candidatos.php`

#### Funcionalidades:
✅ **Crear Candidato**
- Seleccionar partido político
- Cargo: Presidente | Vicepresidente 1ro | Vicepresidente 2do
- Nombres y apellidos
- Profesión
- Foto automática en: `assets/img/candidatos/[nombre].jpg`

✅ **Editar Candidato**
- Cambiar datos, partido o cargo

✅ **Eliminar Candidato**

#### Estructura de candidatos:
Cada partido debe tener:
- 1 Presidente
- 1 Vicepresidente 1ro
- 1 Vicepresidente 2do

#### Ejemplo de uso:
1. Crear partido "AP" primero
2. Ir a Candidatos → "Nuevo Candidato"
3. Partido: `AP`
4. Cargo: `Presidente`
5. Nombres: `Yonhy`
6. Apellidos: `Lescano Ancieta`
7. Profesión: `Abogado y Congresista`
8. Guardar
9. Subir foto a: `assets/img/candidatos/yonhy.jpg`

---

### 3️⃣ **Gestión de Padrón Electoral**

**Ruta:** `/admin/gestionar_padron.php`

#### Funcionalidades:
✅ **Agregar Ciudadano Individual**
- DNI (8 dígitos)
- Nombres y apellidos
- Departamento
- Estado: Habilitado/Deshabilitado

✅ **Importar desde CSV**
- Formato requerido:
```csv
DNI,NOMBRES,APELLIDO_PATERNO,APELLIDO_MATERNO,DEPARTAMENTO
12345678,JUAN CARLOS,PEREZ,GARCIA,LIMA
87654321,MARIA ELENA,RODRIGUEZ,LOPEZ,AREQUIPA
```

✅ **Buscar Ciudadanos**
- Por DNI o nombre
- Filtrado rápido

✅ **Editar/Eliminar Ciudadanos**

✅ **Ver Estado de Votación**
- Icono verde: Ya votó
- Icono gris: No ha votado

#### Proceso de importación masiva:
1. Preparar archivo CSV con el formato correcto
2. Ver ejemplo en: `/admin/ejemplo_padron.csv`
3. Clic en "Importar CSV"
4. Seleccionar archivo
5. El sistema importará todos los registros válidos
6. Se mostrarán estadísticas: importados vs errores

---

### 4️⃣ **Votos Registrados**

**Ruta:** `/admin/gestionar_votos.php`

#### Funcionalidades:
✅ **Visualización de Todos los Votos**
- Información completa del votante
- Tipo de voto: Válido | Blanco | Nulo
- Fecha y hora exacta
- Partido y candidato (si aplica)

✅ **Estadísticas en Tiempo Real**
- Total votos válidos
- Total votos en blanco
- Total votos nulos
- Total general

✅ **Filtros Avanzados**
- Por tipo de voto
- Por DNI o nombre del ciudadano
- Combinación de filtros

✅ **Paginación**
- 50 registros por página
- Navegación entre páginas

✅ **Exportar a Excel**
- Descarga completa en formato CSV
- Compatible con Excel, Google Sheets
- Incluye todos los filtros aplicados

#### Información visible por voto:
- **ID del voto**
- **Fecha y hora** (dd/mm/aaaa hh:mm)
- **DNI del votante**
- **Nombre completo**
- **Departamento**
- **Tipo de voto** (badge con color)
- **Partido/Candidato:**
  - Verde: Voto válido con partido y candidato
  - Gris: Voto en blanco
  - Rojo: Voto nulo

---

## 🔄 Flujo de Trabajo Recomendado

### Setup Inicial del Sistema:

#### **Paso 1: Configurar Partidos**
```
1. Ingresar a "Partidos Políticos"
2. Crear todos los partidos participantes
3. Subir logos a assets/img/partidos/
```

#### **Paso 2: Registrar Candidatos**
```
1. Ingresar a "Candidatos"
2. Para cada partido:
   - Crear Presidente
   - Crear VP 1
   - Crear VP 2
3. Subir fotos a assets/img/candidatos/
```

#### **Paso 3: Importar Padrón Electoral**
```
Opción A - Individual:
1. Ir a "Padrón Electoral"
2. Usar "Nuevo Ciudadano" uno por uno

Opción B - Masiva (RECOMENDADO):
1. Preparar CSV con el formato correcto
2. Importar desde "Importar CSV"
3. Verificar estadísticas de importación
```

#### **Paso 4: Monitorear Votación**
```
1. "Votos Registrados" - Ver en tiempo real
2. "Resultados en Tiempo Real" - Gráficos y estadísticas
3. Exportar datos según necesites
```

---

## 📊 Exportación de Datos

### Exportar Votos a Excel:
1. Ir a "Votos Registrados"
2. Aplicar filtros si deseas (opcional)
3. Clic en "Exportar a Excel"
4. Se descarga archivo CSV con:
   - ID, Fecha/Hora, DNI
   - Datos del votante
   - Tipo de voto
   - Partido y candidato

### Formato del archivo exportado:
```
ID,Fecha y Hora,DNI,Nombres,Apellido Paterno,Apellido Materno,Departamento,Tipo de Voto,Partido (Siglas),Partido (Nombre Completo),Candidato Presidente
1,20/11/2025 14:30:25,12345678,JUAN CARLOS,PEREZ,GARCIA,LIMA,VALIDO,AP,Acción Popular,Yonhy Lescano Ancieta
```

---

## 🔒 Seguridad

### Niveles de Acceso:
- **SUPERADMIN:** Acceso completo a todos los módulos
- **ADMIN:** Acceso limitado (sin gestión de administradores)

### Validaciones:
✅ DNI único (no duplicados)
✅ Formato de DNI: exactamente 8 dígitos
✅ Un ciudadano solo puede votar una vez
✅ Verificación de sesión en cada página

---

## ⚠️ Notas Importantes

1. **No eliminar partidos con votos registrados** - Causará inconsistencias
2. **Hacer backup de la base de datos** antes de importaciones masivas
3. **Subir imágenes con nombres en minúsculas** y sin espacios
4. **El padrón debe estar completo** antes de iniciar votaciones
5. **CSV debe usar codificación UTF-8** para caracteres especiales

---

## 🖼️ Formatos de Imágenes

### Logos de Partidos:
- **Ubicación:** `assets/img/partidos/`
- **Nombre:** `[siglas_en_minusculas].png`
- **Tamaño recomendado:** 200x200 px
- **Formato:** PNG con fondo transparente

**Ejemplo:**
- Partido: "AP" → `assets/img/partidos/ap.png`
- Partido: "FP" → `assets/img/partidos/fp.png`

### Fotos de Candidatos:
- **Ubicación:** `assets/img/candidatos/`
- **Nombre:** `[nombre_en_minusculas].jpg`
- **Tamaño recomendado:** 300x400 px
- **Formato:** JPG

**Ejemplo:**
- Candidato: "Yonhy Lescano" → `assets/img/candidatos/yonhy.jpg`
- Candidato: "Keiko Fujimori" → `assets/img/candidatos/keiko.jpg`

---

## 🆘 Solución de Problemas

### "DNI ya existe en el padrón"
➡️ El DNI ya está registrado. Usar "Editar" en lugar de crear nuevo.

### "Error al importar CSV"
➡️ Verificar formato: `DNI,NOMBRES,APELLIDO_PATERNO,APELLIDO_MATERNO,DEPARTAMENTO`
➡️ Asegurarse que el archivo tenga codificación UTF-8
➡️ DNI debe tener exactamente 8 dígitos

### Imágenes no se ven
➡️ Verificar que el nombre del archivo coincida exactamente
➡️ Usar solo minúsculas y guiones bajos
➡️ Verificar permisos de carpeta

### No aparecen candidatos en la cédula
➡️ Verificar que cada partido tenga 3 candidatos (P, VP1, VP2)
➡️ Revisar en "Gestionar Candidatos"

---

## 📞 Soporte

Para problemas técnicos o dudas:
- Revisar logs de errores en Apache
- Verificar conexión a base de datos
- Comprobar permisos de archivos

---

**Sistema Electoral Perú 2026 - ONPE**
*Versión 1.0 - Panel de Administración*
