# 🔬 EJEMPLOS PRÁCTICOS Y CONSULTAS SQL - Sistema Electoral 2026

## 📚 ÍNDICE
1. [Consultas Básicas](#1-consultas-básicas)
2. [Consultas de Análisis](#2-consultas-de-análisis)
3. [Ejemplos de Uso de Procedimientos](#3-ejemplos-de-uso-de-procedimientos)
4. [Consultas para la Exposición](#4-consultas-para-la-exposición)
5. [Casos de Prueba](#5-casos-de-prueba)

---

## 1. CONSULTAS BÁSICAS

### 📋 Ver todos los ciudadanos registrados:
```sql
SELECT 
    dni,
    CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo,
    departamento,
    ha_votado,
    fecha_voto
FROM tbl_ciudadano
WHERE estado = 1
ORDER BY apellido_paterno, apellido_materno;
```

**Resultado esperado:**
```
+----------+---------------------------+--------------+-----------+---------------------+
| dni      | nombre_completo           | departamento | ha_votado | fecha_voto          |
+----------+---------------------------+--------------+-----------+---------------------+
| 12345678 | JUAN CARLOS PEREZ GARCIA  | LIMA         |         0 | NULL                |
| 87654321 | MARIA ELENA RODRIGUEZ...  | AREQUIPA     |         1 | 2025-10-20 14:30:25 |
+----------+---------------------------+--------------+-----------+---------------------+
```

---

### 🎭 Ver todos los partidos con sus candidatos presidenciales:
```sql
SELECT 
    p.siglas,
    p.nombre_corto,
    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS presidente
FROM tbl_partido p
LEFT JOIN tbl_candidato c ON p.id = c.partido_id 
WHERE c.tipo_candidato = 'PRESIDENTE' 
  AND p.estado = 1
  AND p.siglas NOT IN ('BLANCO', 'NULO')
ORDER BY p.orden_cedula;
```

**Resultado esperado:**
```
+--------+------------------------+-----------------------------+
| siglas | nombre_corto           | presidente                  |
+--------+------------------------+-----------------------------+
| FP     | Fuerza Popular         | KEIKO FUJIMORI HIGUCHI      |
| PL     | Perú Libre             | PEDRO CASTILLO TERRONES     |
| RP     | Renovación Popular     | RAFAEL LOPEZ ALIAGA         |
| APP    | Alianza para el Prog.. | CESAR ACUÑA PERALTA         |
+--------+------------------------+-----------------------------+
```

---

### 🗳️ Contar votos totales:
```sql
SELECT 
    COUNT(*) AS total_votos,
    SUM(CASE WHEN voto_tipo = 'VALIDO' THEN 1 ELSE 0 END) AS votos_validos,
    SUM(CASE WHEN voto_tipo = 'BLANCO' THEN 1 ELSE 0 END) AS votos_blancos,
    SUM(CASE WHEN voto_tipo = 'NULO' THEN 1 ELSE 0 END) AS votos_nulos
FROM tbl_voto;
```

**Resultado esperado:**
```
+-------------+---------------+---------------+-------------+
| total_votos | votos_validos | votos_blancos | votos_nulos |
+-------------+---------------+---------------+-------------+
|         150 |           142 |             5 |           3 |
+-------------+---------------+---------------+-------------+
```

---

## 2. CONSULTAS DE ANÁLISIS

### 📊 Ranking de partidos por votos:
```sql
SELECT 
    p.siglas,
    p.nombre_corto,
    COUNT(v.id) AS total_votos,
    ROUND((COUNT(v.id) * 100.0 / 
        (SELECT COUNT(*) FROM tbl_voto WHERE voto_tipo = 'VALIDO')), 2) AS porcentaje
FROM tbl_partido p
LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.voto_tipo = 'VALIDO'
WHERE p.estado = 1 
  AND p.siglas NOT IN ('BLANCO', 'NULO')
GROUP BY p.id, p.siglas, p.nombre_corto
ORDER BY total_votos DESC;
```

**Resultado esperado:**
```
+--------+------------------------+-------------+------------+
| siglas | nombre_corto           | total_votos | porcentaje |
+--------+------------------------+-------------+------------+
| FP     | Fuerza Popular         |          45 |      31.69 |
| PL     | Perú Libre             |          38 |      26.76 |
| RP     | Renovación Popular     |          25 |      17.61 |
| APP    | Alianza para el Prog.. |          20 |      14.08 |
| PM     | Partido Morado         |          14 |       9.86 |
+--------+------------------------+-------------+------------+
```

---

### 📈 Estadísticas de participación por departamento:
```sql
SELECT 
    departamento,
    COUNT(*) AS total_ciudadanos,
    SUM(ha_votado) AS total_votantes,
    ROUND((SUM(ha_votado) * 100.0 / COUNT(*)), 2) AS porcentaje_participacion
FROM tbl_ciudadano
WHERE estado = 1
GROUP BY departamento
ORDER BY porcentaje_participacion DESC;
```

**Resultado esperado:**
```
+--------------+-------------------+----------------+-------------------------+
| departamento | total_ciudadanos  | total_votantes | porcentaje_participacion|
+--------------+-------------------+----------------+-------------------------+
| AREQUIPA     |               120 |            108 |                   90.00 |
| LIMA         |               350 |            298 |                   85.14 |
| CUSCO        |                80 |             65 |                   81.25 |
| PIURA        |               100 |             75 |                   75.00 |
+--------------+-------------------+----------------+-------------------------+
```

---

### ⏰ Análisis de horas de votación:
```sql
SELECT 
    HOUR(fecha_voto) AS hora,
    COUNT(*) AS cantidad_votos
FROM tbl_voto
WHERE fecha_voto IS NOT NULL
GROUP BY HOUR(fecha_voto)
ORDER BY hora;
```

**Resultado esperado:**
```
+------+----------------+
| hora | cantidad_votos |
+------+----------------+
|    8 |             15 |
|    9 |             32 |
|   10 |             45 |
|   11 |             28 |
|   12 |             20 |
|   13 |             10 |
+------+----------------+
```

---

### 🎯 Ciudadanos que NO han votado:
```sql
SELECT 
    dni,
    CONCAT(nombres, ' ', apellido_paterno) AS nombre,
    departamento,
    provincia
FROM tbl_ciudadano
WHERE ha_votado = 0 
  AND estado = 1
ORDER BY departamento, apellido_paterno;
```

**Resultado esperado:**
```
+----------+----------------------+--------------+-----------+
| dni      | nombre               | departamento | provincia |
+----------+----------------------+--------------+-----------+
| 12345678 | JUAN CARLOS PEREZ    | LIMA         | LIMA      |
| 55667788 | CARLOS ALBERTO...    | LIMA         | LIMA      |
| 11223344 | PEDRO JOSE GONZALES  | CUSCO        | CUSCO     |
+----------+----------------------+--------------+-----------+
```

---

### 🕐 Tiempo promedio de votación:
```sql
SELECT 
    AVG(tiempo_votacion_segundos) AS tiempo_promedio,
    MIN(tiempo_votacion_segundos) AS tiempo_minimo,
    MAX(tiempo_votacion_segundos) AS tiempo_maximo,
    ROUND(AVG(tiempo_votacion_segundos) / 60, 2) AS promedio_minutos
FROM tbl_voto
WHERE tiempo_votacion_segundos > 0;
```

**Resultado esperado:**
```
+-----------------+---------------+---------------+------------------+
| tiempo_promedio | tiempo_minimo | tiempo_maximo | promedio_minutos |
+-----------------+---------------+---------------+------------------+
|          125.50 |            45 |           320 |             2.09 |
+-----------------+---------------+---------------+------------------+
```

---

## 3. EJEMPLOS DE USO DE PROCEDIMIENTOS

### ✅ Procedimiento: sp_validar_ciudadano

#### Ejemplo 1: Ciudadano válido que no ha votado
```sql
CALL sp_validar_ciudadano('12345678');
```

**Resultado:**
```
+----+----------+---------------------------+--------------+-----------+
| id | dni      | nombre_completo           | ha_votado    | estado    |
+----+----------+---------------------------+--------------+-----------+
|  1 | 12345678 | JUAN CARLOS PEREZ GARCIA  |          0   |        1  |
+----+----------+---------------------------+--------------+-----------+
```

#### Ejemplo 2: DNI que no existe
```sql
CALL sp_validar_ciudadano('99999999');
```

**Resultado:**
```
Empty set (0.00 sec)
-- No retorna filas
```

#### Ejemplo 3: Ciudadano que ya votó
```sql
CALL sp_validar_ciudadano('87654321');
```

**Resultado:**
```
+----+----------+-----------------------------+-----------+--------+
| id | dni      | nombre_completo             | ha_votado | estado |
+----+----------+-----------------------------+-----------+--------+
|  2 | 87654321 | MARIA ELENA RODRIGUEZ LOPEZ |         1 |      1 |
+----+----------+-----------------------------+-----------+--------+
```

---

### 📋 Procedimiento: sp_obtener_cedula

```sql
CALL sp_obtener_cedula();
```

**Resultado:**
```
+------------+--------------+--------+-----------------------------+--------------+
| partido_id | nombre_corto | siglas | presidente                  | vice1        |
+------------+--------------+--------+-----------------------------+--------------+
|          1 | Fuerza Pop.. | FP     | KEIKO FUJIMORI HIGUCHI      | LUIS GALA... |
|          2 | Perú Libre   | PL     | PEDRO CASTILLO TERRONES     | DINA BOLU... |
|          3 | Renovación.. | RP     | RAFAEL LOPEZ ALIAGA         | ADRIANA T... |
+------------+--------------+--------+-----------------------------+--------------+
8 rows in set (0.01 sec)
```

---

### 🗳️ Procedimiento: sp_registrar_voto

#### Ejemplo 1: Voto exitoso
```sql
CALL sp_registrar_voto('12345678', 1, 'VALIDO', '192.168.1.10', 125);
```

**Resultado:**
```
+--------------------------------+--------------+
| mensaje                        | ciudadano_id |
+--------------------------------+--------------+
| Voto registrado exitosamente   |            1 |
+--------------------------------+--------------+
```

**Verificación:**
```sql
-- Ver que se registró el voto
SELECT * FROM tbl_voto WHERE ciudadano_id = 1;

-- Ver que se actualizó el ciudadano
SELECT dni, ha_votado, fecha_voto, ip_voto 
FROM tbl_ciudadano 
WHERE id = 1;
```

#### Ejemplo 2: Intento de doble voto (ERROR)
```sql
CALL sp_registrar_voto('12345678', 2, 'VALIDO', '192.168.1.10', 80);
```

**Resultado:**
```
ERROR 1644 (45000): Este ciudadano ya emitió su voto
```

#### Ejemplo 3: DNI inexistente (ERROR)
```sql
CALL sp_registrar_voto('99999999', 1, 'VALIDO', '192.168.1.10', 60);
```

**Resultado:**
```
ERROR 1644 (45000): DNI no encontrado en el padrón electoral
```

---

## 4. CONSULTAS PARA LA EXPOSICIÓN

### 🎤 Demostración 1: Proceso Completo de Votación

```sql
-- PASO 1: Verificar estado inicial del ciudadano
SELECT dni, CONCAT(nombres, ' ', apellido_paterno) AS nombre, ha_votado
FROM tbl_ciudadano
WHERE dni = '12345678';

-- RESULTADO ESPERADO: ha_votado = 0

-- PASO 2: Validar que puede votar
CALL sp_validar_ciudadano('12345678');

-- RESULTADO ESPERADO: Retorna datos del ciudadano

-- PASO 3: Obtener opciones de votación
CALL sp_obtener_cedula();

-- RESULTADO ESPERADO: Lista de partidos y candidatos

-- PASO 4: Registrar el voto (supongamos que vota por partido 8 - JPP)
CALL sp_registrar_voto('12345678', 8, 'VALIDO', '127.0.0.1', 95);

-- RESULTADO ESPERADO: "Voto registrado exitosamente"

-- PASO 5: Verificar que se registró el voto
SELECT * FROM tbl_voto WHERE ciudadano_id = (
    SELECT id FROM tbl_ciudadano WHERE dni = '12345678'
);

-- PASO 6: Verificar que se actualizó el ciudadano
SELECT dni, ha_votado, fecha_voto, ip_voto
FROM tbl_ciudadano
WHERE dni = '12345678';

-- RESULTADO ESPERADO: ha_votado = 1, fecha_voto = NOW(), ip_voto = '127.0.0.1'
```

---

### 🎤 Demostración 2: Consultar Resultados en Tiempo Real

```sql
-- Usar la vista de resultados
SELECT 
    siglas,
    candidato_nombre,
    total_votos,
    porcentaje
FROM v_resultados_tiempo_real
ORDER BY total_votos DESC
LIMIT 5;
```

**Mostrar en pantalla:**
```
+--------+-----------------------------+-------------+------------+
| siglas | candidato_nombre            | total_votos | porcentaje |
+--------+-----------------------------+-------------+------------+
| FP     | KEIKO FUJIMORI HIGUCHI      |          45 |      31.69 |
| PL     | PEDRO CASTILLO TERRONES     |          38 |      26.76 |
| RP     | RAFAEL LOPEZ ALIAGA         |          25 |      17.61 |
| APP    | CESAR ACUÑA PERALTA         |          20 |      14.08 |
| PM     | JULIO GUZMAN CACERES        |          14 |       9.86 |
+--------+-----------------------------+-------------+------------+
```

---

### 🎤 Demostración 3: Estadísticas Generales

```sql
-- Usar la vista de estadísticas
SELECT * FROM v_estadisticas_elecciones;
```

**Mostrar en pantalla:**
```
+------------------+----------------+---------------+---------------+-------------+---------------+-------------------------+
| total_ciudadanos | total_votantes | votos_validos | votos_blancos | votos_nulos | total_partidos| porcentaje_participacion|
+------------------+----------------+---------------+---------------+-------------+---------------+-------------------------+
|              500 |            350 |           340 |             7 |           3 |             8 |                   70.00 |
+------------------+----------------+---------------+---------------+-------------+---------------+-------------------------+
```

---

### 🎤 Demostración 4: Seguridad - Prevención de Doble Voto

```sql
-- Intentar que un ciudadano vote dos veces
SELECT dni, ha_votado FROM tbl_ciudadano WHERE dni = '87654321';
-- Resultado: ha_votado = 1

-- Intentar registrar otro voto
CALL sp_registrar_voto('87654321', 3, 'VALIDO', '192.168.1.20', 60);

-- RESULTADO: ERROR 1644 (45000): Este ciudadano ya emitió su voto
```

---

## 5. CASOS DE PRUEBA

### ✅ Caso de Prueba 1: Votación Normal

**Precondición:** Ciudadano DNI 12345678 no ha votado

```sql
-- 1. Validar ciudadano
CALL sp_validar_ciudadano('12345678');
-- ✅ Esperado: Retorna datos, ha_votado = 0

-- 2. Registrar voto
CALL sp_registrar_voto('12345678', 1, 'VALIDO', '192.168.1.100', 120);
-- ✅ Esperado: "Voto registrado exitosamente"

-- 3. Verificar actualización
SELECT ha_votado FROM tbl_ciudadano WHERE dni = '12345678';
-- ✅ Esperado: ha_votado = 1
```

---

### ❌ Caso de Prueba 2: DNI Inexistente

```sql
CALL sp_validar_ciudadano('00000000');
-- ❌ Esperado: Empty set (no retorna filas)

CALL sp_registrar_voto('00000000', 1, 'VALIDO', '192.168.1.100', 60);
-- ❌ Esperado: ERROR 1644 - DNI no encontrado
```

---

### ❌ Caso de Prueba 3: Intento de Doble Voto

```sql
-- Primera votación
CALL sp_registrar_voto('11223344', 5, 'VALIDO', '192.168.1.50', 90);
-- ✅ Esperado: Éxito

-- Segunda votación (fraude)
CALL sp_registrar_voto('11223344', 3, 'VALIDO', '192.168.1.51', 45);
-- ❌ Esperado: ERROR 1644 - Este ciudadano ya emitió su voto
```

---

### ✅ Caso de Prueba 4: Voto en Blanco

```sql
-- Registrar voto en blanco
-- Partido con siglas "BLANCO" tiene ID 9
CALL sp_registrar_voto('44332211', 9, 'BLANCO', '192.168.1.75', 30);
-- ✅ Esperado: "Voto registrado exitosamente"

-- Verificar en resultados
SELECT voto_tipo, COUNT(*) 
FROM tbl_voto 
GROUP BY voto_tipo;
-- ✅ Esperado: Muestra votos VALIDOS, BLANCOS, NULOS
```

---

### ✅ Caso de Prueba 5: Consulta de Relaciones

```sql
-- Ver un partido completo con sus candidatos y votos
SELECT 
    p.nombre_corto AS partido,
    c.nombres AS candidato,
    c.tipo_candidato AS tipo,
    COUNT(DISTINCT v.id) AS votos_recibidos
FROM tbl_partido p
LEFT JOIN tbl_candidato c ON p.id = c.partido_id
LEFT JOIN tbl_voto v ON p.id = v.partido_id
WHERE p.id = 1  -- Fuerza Popular
GROUP BY p.id, c.id, c.nombres, c.tipo_candidato
ORDER BY c.tipo_candidato;
```

**Resultado esperado:**
```
+----------------+-----------+------------------+------------------+
| partido        | candidato | tipo             | votos_recibidos  |
+----------------+-----------+------------------+------------------+
| Fuerza Popular | KEIKO     | PRESIDENTE       |               45 |
| Fuerza Popular | LUIS      | VICEPRESIDENTE_1 |               45 |
| Fuerza Popular | MARTHA    | VICEPRESIDENTE_2 |               45 |
+----------------+-----------+------------------+------------------+
```

---

## 📊 CONSULTAS ADICIONALES ÚTILES

### 🔍 Ver detalle completo de un voto:
```sql
SELECT 
    c.dni,
    CONCAT(c.nombres, ' ', c.apellido_paterno) AS votante,
    p.siglas AS partido_votado,
    v.voto_tipo,
    v.fecha_voto,
    v.ip_address,
    v.tiempo_votacion_segundos
FROM tbl_voto v
INNER JOIN tbl_ciudadano c ON v.ciudadano_id = c.id
INNER JOIN tbl_partido p ON v.partido_id = p.id
WHERE c.dni = '87654321';
```

---

### 📈 Gráfico de barras (datos para Chart.js):
```sql
SELECT 
    p.siglas AS label,
    COUNT(v.id) AS value,
    p.color_primario AS backgroundColor
FROM tbl_partido p
LEFT JOIN tbl_voto v ON p.id = v.partido_id AND v.voto_tipo = 'VALIDO'
WHERE p.estado = 1 AND p.siglas NOT IN ('BLANCO', 'NULO')
GROUP BY p.id, p.siglas, p.color_primario
ORDER BY value DESC;
```

---

### 🗺️ Mapa de participación:
```sql
SELECT 
    departamento,
    provincia,
    COUNT(*) AS total_habilitados,
    SUM(ha_votado) AS total_votaron,
    COUNT(*) - SUM(ha_votado) AS falta_votar,
    ROUND(SUM(ha_votado) * 100.0 / COUNT(*), 2) AS participacion
FROM tbl_ciudadano
WHERE estado = 1
GROUP BY departamento, provincia
HAVING COUNT(*) > 0
ORDER BY participacion DESC;
```

---

## 💡 TIPS PARA DEMOSTRAR EN VIVO

### ✅ Preparación previa:

1. **Tener datos de prueba listos:**
```sql
-- Ciudadano que SÍ puede votar
DNI: 12345678 (JUAN CARLOS PEREZ)

-- Ciudadano que YA votó
DNI: 87654321 (MARIA ELENA RODRIGUEZ)

-- DNI que NO existe
DNI: 00000000
```

2. **Limpiar datos si es necesario:**
```sql
-- Resetear un voto para demostración
DELETE FROM tbl_voto WHERE ciudadano_id = 1;
UPDATE tbl_ciudadano SET ha_votado = 0, fecha_voto = NULL WHERE id = 1;
```

3. **Abrir múltiples ventanas de terminal:**
   - Ventana 1: Para ejecutar procedimientos
   - Ventana 2: Para consultas SELECT
   - Ventana 3: Para mostrar resultados en tiempo real

---

## 🎯 SCRIPT DE DEMOSTRACIÓN COMPLETO

```sql
-- =====================================================
-- DEMOSTRACIÓN COMPLETA - SISTEMA ELECTORAL 2026
-- =====================================================

-- 1. MOSTRAR ESTADO INICIAL
SELECT '=== ESTADO INICIAL DEL SISTEMA ===' AS titulo;
SELECT * FROM v_estadisticas_elecciones;

-- 2. VALIDAR UN CIUDADANO
SELECT '=== VALIDANDO CIUDADANO DNI: 12345678 ===' AS titulo;
CALL sp_validar_ciudadano('12345678');

-- 3. MOSTRAR OPCIONES DE VOTACIÓN
SELECT '=== CÉDULA DE VOTACIÓN ===' AS titulo;
CALL sp_obtener_cedula();

-- 4. REGISTRAR UN VOTO
SELECT '=== REGISTRANDO VOTO ===' AS titulo;
CALL sp_registrar_voto('12345678', 8, 'VALIDO', '192.168.1.100', 95);

-- 5. VERIFICAR QUE SE REGISTRÓ
SELECT '=== VERIFICANDO REGISTRO DE VOTO ===' AS titulo;
SELECT * FROM tbl_voto WHERE ciudadano_id = 1;
SELECT dni, ha_votado, fecha_voto FROM tbl_ciudadano WHERE dni = '12345678';

-- 6. VER RESULTADOS ACTUALIZADOS
SELECT '=== RESULTADOS ACTUALIZADOS ===' AS titulo;
SELECT siglas, candidato_nombre, total_votos, porcentaje 
FROM v_resultados_tiempo_real 
ORDER BY total_votos DESC;

-- 7. INTENTAR DOBLE VOTO (DEMOSTRAR SEGURIDAD)
SELECT '=== INTENTANDO DOBLE VOTO (DEBE FALLAR) ===' AS titulo;
CALL sp_registrar_voto('12345678', 3, 'VALIDO', '192.168.1.101', 60);
-- Esperado: ERROR

-- 8. ESTADÍSTICAS FINALES
SELECT '=== ESTADÍSTICAS FINALES ===' AS titulo;
SELECT * FROM v_estadisticas_elecciones;
```

---

**Preparado para:** Exposición del Sistema Electoral Perú 2026  
**Incluye:** 40+ consultas SQL de ejemplo  
**Nivel:** Intermedio-Avanzado  
**Tiempo estimado de demostración:** 15-20 minutos

