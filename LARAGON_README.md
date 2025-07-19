# 🎉 PropEasy - Configuración Completada para Laragon

## ✅ Instalación Exitosa

Tu proyecto PropEasy ha sido configurado correctamente para Laragon. Aquí tienes toda la información necesaria:

## 🌐 Acceso al Sistema

### **URL Principal:**
```
http://localhost/propeasy
```

### **Credenciales de Administrador:**
- **Email**: `admin@propeasy.com`
- **Contraseña**: `admin123`

## 🚀 Cómo Iniciar

### **Paso 1: Iniciar Laragon**
1. Abrir Laragon
2. Hacer clic en **"Start All"**
3. Esperar a que Apache y MySQL estén en verde

### **Paso 2: Acceder al Sistema**
1. Abrir tu navegador
2. Ir a: `http://localhost/propeasy`
3. Iniciar sesión con las credenciales de administrador

## 📁 Estructura del Proyecto

```
C:\laragon\www\propeasy\
├── app/                    # Código de la aplicación
│   ├── controllers/        # Controladores
│   ├── models/            # Modelos
│   ├── views/             # Vistas
│   └── core/              # Núcleo del sistema
├── config/                 # Configuración
│   ├── config.php         # Configuración principal
│   └── database.php       # Configuración de BD
├── public/                 # Archivos públicos
│   ├── index.php          # Punto de entrada
│   ├── css/               # Estilos
│   ├── js/                # JavaScript
│   └── uploads/           # Archivos subidos
├── database/              # Scripts de BD
│   ├── scheme.sql         # Esquema completo
│   └── install.sql        # Esquema simplificado
├── logs/                  # Logs del sistema
├── .htaccess              # Configuración Apache
├── install-laragon.php    # Instalador
└── LARAGON_README.md      # Este archivo
```

## 🗄️ Base de Datos

### **Información de Conexión:**
- **Host**: `localhost`
- **Base de datos**: `propeasy_db`
- **Usuario**: `root`
- **Contraseña**: (sin contraseña)

### **Acceso a phpMyAdmin:**
1. En Laragon, hacer clic en **"Database"**
2. O ir directamente a: `http://localhost/phpmyadmin`

## 🔧 Funcionalidades Disponibles

### **Para Administradores:**
- ✅ Gestión de usuarios
- ✅ Aprobación de propiedades
- ✅ Reportes de irregularidades
- ✅ Estadísticas del sistema

### **Para Agentes:**
- ✅ Publicar propiedades
- ✅ Gestionar solicitudes
- ✅ Agenda de citas
- ✅ Chat con clientes

### **Para Clientes:**
- ✅ Buscar propiedades
- ✅ Solicitar información
- ✅ Agendar citas
- ✅ Sistema de favoritos

## 🛠️ Solución de Problemas

### **Si no puedes acceder:**

#### **1. Verificar Servicios**
- Asegúrate de que Apache y MySQL estén ejecutándose (verde en Laragon)

#### **2. Verificar URL**
- Usa exactamente: `http://localhost/propeasy`
- No uses `http://propeasy.test` a menos que lo hayas configurado

#### **3. Verificar Puertos**
- Puerto 80 (Apache) - debe estar libre
- Puerto 3306 (MySQL) - debe estar libre

#### **4. Reiniciar Servicios**
1. En Laragon, hacer clic en **"Stop All"**
2. Esperar 5 segundos
3. Hacer clic en **"Start All"**

### **Si hay errores de base de datos:**

#### **1. Verificar MySQL**
```sql
-- En phpMyAdmin o MySQL Workbench:
SHOW DATABASES;
USE propeasy_db;
SHOW TABLES;
```

#### **2. Reinstalar Base de Datos**
```bash
# Ejecutar el instalador nuevamente:
http://localhost/propeasy/install-laragon.php
```

### **Si hay errores de permisos:**

#### **1. Verificar Directorios**
```bash
# Los siguientes directorios deben ser escribibles:
logs/
public/uploads/
public/uploads/properties/
public/uploads/reportes/
```

#### **2. Configurar Permisos (Windows)**
- Click derecho en la carpeta del proyecto
- Propiedades → Seguridad → Editar
- Agregar permisos de escritura para el usuario del servidor web

## 📞 Soporte

### **Logs de Error:**
- **PHP**: `logs/error.log`
- **Apache**: En Laragon → Menu → Apache → error.log

### **Reinstalación Completa:**
Si necesitas reinstalar todo:
1. Eliminar la base de datos `propeasy_db`
2. Ejecutar: `http://localhost/propeasy/install-laragon.php`

## 🎯 Próximos Pasos

### **1. Personalizar el Sistema:**
- Modificar `config/config.php` para tu entorno
- Actualizar credenciales de email
- Personalizar colores y estilos

### **2. Crear Usuarios de Prueba:**
- Crear agentes inmobiliarios
- Crear clientes de prueba
- Agregar propiedades de ejemplo

### **3. Configurar Email:**
- Actualizar SMTP en `config/config.php`
- Probar envío de emails

---

## 🎉 ¡PropEasy está listo para usar!

**URL de acceso**: `http://localhost/propeasy`

**Email**: `admin@propeasy.com`  
**Contraseña**: `admin123`

---

*Configurado el: <?php echo date('Y-m-d H:i:s'); ?>* 