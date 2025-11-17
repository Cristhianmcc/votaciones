<!-- 
    ARCHIVO DE REFERENCIA PARA IMÁGENES
    Este archivo lista todas las imágenes necesarias para el sistema
-->

# 📸 IMÁGENES NECESARIAS PARA EL SISTEMA

## 🎨 Carpeta: assets/img/partidos/

### Logos de Partidos Políticos (PNG o JPG, 300x300px recomendado)

1. **fuerza_popular.png** - Logo de Fuerza Popular (naranja)
2. **peru_libre.png** - Logo de Perú Libre (rojo)
3. **renovacion_popular.png** - Logo de Renovación Popular (azul)
4. **app.png** - Logo de Alianza para el Progreso
5. **accion_popular.png** - Logo de Acción Popular
6. **partido_morado.png** - Logo del Partido Morado
7. **avanza_pais.png** - Logo de Avanza País
8. **juntos_peru.png** - Logo de Juntos por el Perú
9. **voto_blanco.png** - Imagen para voto en blanco (fondo blanco)
10. **voto_nulo.png** - Imagen para voto nulo (X tachado)
11. **default.png** - Imagen por defecto si falta logo

---

## 👤 Carpeta: assets/img/candidatos/

### Fotos de Candidatos Presidenciales (JPG, 400x500px recomendado)

**Fuerza Popular:**
1. **keiko.jpg** - Keiko Fujimori
2. **galarreta.jpg** - Luis Galarreta
3. **chavez.jpg** - Martha Chávez

**Perú Libre:**
4. **castillo.jpg** - Pedro Castillo
5. **boluarte.jpg** - Dina Boluarte

**Renovación Popular:**
6. **lopez_aliaga.jpg** - Rafael López Aliaga
7. **tudela.jpg** - Adriana Tudela

**Alianza para el Progreso:**
8. **acuna.jpg** - César Acuña
9. **camones.jpg** - Lady Camones

**Acción Popular:**
10. **lescano.jpg** - Yonhy Lescano
11. **leon.jpg** - María Isabel León

**Partido Morado:**
12. **guzman.jpg** - Julio Guzmán
13. **pablo.jpg** - Flor Pablo

**Avanza País:**
14. **desoto.jpg** - Hernando de Soto
15. **chirinos.jpg** - Patricia Chirinos

**Juntos por el Perú:**
16. **mendoza.jpg** - Verónika Mendoza
17. **silva.jpg** - Rocío Silva Santisteban

**Default:**
18. **default.jpg** - Silueta de persona genérica

---

## 📥 CÓMO OBTENER LAS IMÁGENES

### Opción 1: Crear Placeholders Automáticamente

Puedes usar servicios de placeholder de imágenes temporalmente:

**Logos de partidos:**
```
https://via.placeholder.com/300x300/FF6600/FFFFFF?text=FP
https://via.placeholder.com/300x300/CC0000/FFFFFF?text=PL
https://via.placeholder.com/300x300/00BFFF/FFFFFF?text=RP
https://via.placeholder.com/300x300/0066CC/FFFFFF?text=APP
https://via.placeholder.com/300x300/DC143C/FFFFFF?text=AP
https://via.placeholder.com/300x300/8B008B/FFFFFF?text=PM
https://via.placeholder.com/300x300/FF1493/FFFFFF?text=APPIS
https://via.placeholder.com/300x300/FF4500/FFFFFF?text=JPP
```

**Fotos de candidatos:**
```
https://via.placeholder.com/400x500/333333/FFFFFF?text=Candidato
```

### Opción 2: Descargar Imágenes Reales

1. Busca en Google Images: "logo [nombre del partido] perú"
2. Busca en Google Images: "foto [nombre del candidato] perú"
3. Descarga las imágenes en buena resolución
4. Renómbralas según la lista de arriba
5. Colócalas en las carpetas correspondientes

### Opción 3: Usar Imágenes de Ejemplo

El sistema incluye validación de errores:
- Si una imagen no existe, muestra una imagen por defecto
- El atributo `onerror` en las imágenes maneja los errores

---

## 🎨 ESPECIFICACIONES TÉCNICAS

### **Logos de Partidos:**
- **Formato:** PNG (con fondo transparente) o JPG
- **Tamaño recomendado:** 300x300px o 500x500px
- **Aspecto:** Cuadrado (1:1)
- **Peso:** Menos de 200KB

### **Fotos de Candidatos:**
- **Formato:** JPG o PNG
- **Tamaño recomendado:** 400x500px o 800x1000px
- **Aspecto:** Vertical (4:5 o similar)
- **Peso:** Menos de 300KB
- **Estilo:** Foto tipo carnet o retrato profesional

### **Imagen Default:**
- **Formato:** PNG con transparencia
- **Contenido:** Silueta de persona genérica
- **Colores:** Grises neutros

---

## 🔧 ACTUALIZAR RUTAS EN LA BASE DE DATOS

Si quieres usar URLs externas o cambiar rutas:

```sql
-- Actualizar logo de un partido
UPDATE tbl_partido 
SET logo_url = 'https://ejemplo.com/logo.png' 
WHERE id = 1;

-- Actualizar foto de un candidato
UPDATE tbl_candidato 
SET foto_url = 'https://ejemplo.com/foto.jpg' 
WHERE id = 1;
```

---

## ✅ VERIFICAR IMÁGENES

Para verificar que las imágenes están cargando correctamente:

1. Abre el navegador en modo desarrollador (F12)
2. Ve a la pestaña "Network" o "Red"
3. Recarga la página
4. Busca las imágenes en la lista de recursos
5. Verifica que no hayan errores 404 (no encontrado)

---

## 🎨 CREAR TUS PROPIAS IMÁGENES

### Herramientas Recomendadas:
- **Canva** (online, gratis) - Para logos y diseños
- **GIMP** (desktop, gratis) - Editor de imágenes
- **Photopea** (online, gratis) - Similar a Photoshop
- **Remove.bg** - Para quitar fondos de fotos

### Dimensiones Exactas:
```
Logos: 500px × 500px
Fotos: 800px × 1000px
```

---

**NOTA:** El sistema funcionará correctamente incluso sin imágenes reales, mostrando placeholders en su lugar.
