# 🚀 INICIO RÁPIDO - SISTEMA ELECTORAL PERÚ 2026

## ⚡ EN 5 MINUTOS

### 1️⃣ **Ejecutar Base de Datos** (1 min)
```powershell
# Abrir phpMyAdmin:
http://localhost/phpmyadmin/

# Ir a SQL y pegar el contenido de: database_electoral.sql
# Clic en "Continuar"
```

### 2️⃣ **Iniciar Servicios** (1 min)
```powershell
# Apache
net start Apache2.4

# MySQL
net start MySQL80
```

### 3️⃣ **Abrir Sistema** (10 seg)
```
http://localhost/elecciones_peru_2026/
```

### 4️⃣ **Votar** (2 min)
```
DNI: 12345678
Seleccionar candidato
Confirmar voto
```

### 5️⃣ **Ver Resultados** (1 min)
```
http://localhost/elecciones_peru_2026/resultados_publicos.php
```

---

## 🎯 URLS PRINCIPALES

| Página | URL |
|--------|-----|
| **Login** | http://localhost/elecciones_peru_2026/ |
| **Resultados** | http://localhost/elecciones_peru_2026/resultados_publicos.php |
| **phpMyAdmin** | http://localhost/phpmyadmin/ |

---

## 👤 DNIS DE PRUEBA

```
12345678  -  JUAN CARLOS PEREZ GARCIA
87654321  -  MARIA ELENA RODRIGUEZ LOPEZ
11223344  -  PEDRO JOSE GONZALES MARTINEZ
44332211  -  ANA LUCIA FERNANDEZ TORRES
55667788  -  CARLOS ALBERTO SANCHEZ DIAZ
```

---

## 🔧 COMANDOS ÚTILES

### **Reiniciar Apache:**
```powershell
net stop Apache2.4
net start Apache2.4
```

### **Reiniciar MySQL:**
```powershell
net stop MySQL80
net start MySQL80
```

### **Ver logs de errores:**
```powershell
# Apache
type c:\Apache24\logs\error.log

# MySQL
type "c:\ProgramData\MySQL\MySQL Server 8.0\Data\*.err"
```

---

## ❓ PROBLEMAS COMUNES

### **No carga la página**
✅ Verifica que Apache esté corriendo: `netstat -an | findstr "80"`

### **Error de conexión a BD**
✅ Verifica que MySQL esté corriendo: `netstat -an | findstr "3306"`
✅ Revisa credenciales en `conexion.php`

### **DNI no encontrado**
✅ Verifica que ejecutaste el `database_electoral.sql`
✅ Usa DNI: `12345678`

---

## 📊 CARACTERÍSTICAS PRINCIPALES

✅ Login con DNI (8 dígitos)  
✅ Cédula de votación con fotos  
✅ Un voto por ciudadano  
✅ Dashboard en tiempo real  
✅ Gráficos con Chart.js  
✅ Actualización cada 5 segundos  
✅ Top 3 con medallas  
✅ Diseño responsive  

---

## 📁 ARCHIVOS IMPORTANTES

- `index.php` - Página de login
- `cedula_votacion.php` - Cédula para votar
- `resultados_publicos.php` - Dashboard de resultados
- `database_electoral.sql` - Base de datos
- `conexion.php` - Configuración de BD

---

## 🎓 PARA APRENDER MÁS

- **README.md** - Documentación completa
- **INSTALACION.md** - Guía paso a paso detallada
- **IMAGENES.md** - Cómo agregar imágenes

---

**¡Listo para votar!** 🗳️
