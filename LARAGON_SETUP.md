# 🚀 Configuración de PropEasy para Laragon

## ⚡ Instalación Rápida

### **Paso 1: Ejecutar Instalador**
```bash
# Abrir navegador y visitar:
http://localhost/propeasy/install-laragon.php
```

### **Paso 2: Configurar Laragon**
1. Abrir Laragon
2. Hacer clic en **"Start All"** (Apache + MySQL)
3. Esperar a que ambos servicios estén en verde

### **Paso 3: Acceder al Sistema**
- **URL**: `http://localhost/propeasy`
- **Email**: `admin@propeasy.com`
- **Contraseña**: `admin123`

## 🔧 Configuración Manual (Si es necesario)

### **1. Verificar Ubicación del Proyecto**
```
C:\laragon\www\propeasy\
```

### **2. Configurar Document Root**
- En Laragon, ir a **Menu** → **www**
- Verificar que el proyecto esté en la carpeta correcta

### **3. Verificar Servicios**
- **Apache**: Puerto 80 (debe estar en verde)
- **MySQL**: Puerto 3306 (debe estar en verde)

### **4. Configurar Base de Datos**
```sql
-- Crear base de datos
CREATE DATABASE propeasy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar esquema
-- Usar el archivo: database/scheme.sql
```

## 🌐 URLs de Acceso

### **Principal:**
- `http://localhost/propeasy`

### **Alternativas:**
- `http://propeasy.test` (si configuras hosts)
- `http://127.0.0.1/propeasy`

## 📁 Estructura del Proyecto

```
C:\laragon\www\propeasy\
├── app/                    # Código de la aplicación
├── config/                 # Configuración
│   ├── config.php         # Configuración principal
│   └── database.php       # Configuración de BD
├── public/                 # Archivos públicos
│   ├── index.php          # Punto de entrada
│   ├── css/               # Estilos
│   ├── js/                # JavaScript
│   └── uploads/           # Archivos subidos
├── database/              # Scripts de BD
│   └── scheme.sql         # Esquema de BD
├── logs/                  # Logs del sistema
├── .htaccess              # Configuración Apache
└── install-laragon.php    # Instalador
```

## 🔍 Solución de Problemas

### **Error 500 - Internal Server Error**

#### **1. Verificar Servicios**
- Asegúrate de que Apache y MySQL estén ejecutándose
- En Laragon, ambos deben estar en verde

#### **2. Verificar Archivos**
```bash
# Verificar que existan estos archivos:
config/config.php
config/database.php
public/index.php
database/scheme.sql
```

#### **3. Verificar Permisos**
```bash
# Los directorios deben ser escribibles:
logs/
public/uploads/
```

#### **4. Verificar Base de Datos**
```sql
-- Conectar a MySQL y verificar:
SHOW DATABASES;
USE propeasy_db;
SHOW TABLES;
```

### **Error de Conexión a Base de Datos**

#### **1. Verificar Configuración**
```php
// En config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Sin contraseña por defecto
```

#### **2. Verificar MySQL**
- En Laragon, hacer clic en "Database" para abrir phpMyAdmin
- Verificar que la base de datos `propeasy_db` existe

### **Error de Archivo No Encontrado**

#### **1. Verificar .htaccess**
```apache
# El archivo .htaccess debe redirigir a public/
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]
```

#### **2. Verificar mod_rewrite**
- En Laragon, verificar que mod_rewrite esté habilitado
- Reiniciar Apache si es necesario

## 🛠️ Comandos Útiles

### **Reiniciar Servicios**
```bash
# En Laragon:
1. Hacer clic en "Stop All"
2. Esperar 5 segundos
3. Hacer clic en "Start All"
```

### **Limpiar Caché**
```bash
# Eliminar archivos temporales
rm -rf logs/*
rm -rf public/uploads/*
```

### **Verificar Logs**
```bash
# Ver errores de PHP
tail -f logs/error.log

# Ver errores de Apache
# En Laragon: Menu → Apache → error.log
```

## 📞 Soporte

### **Si el problema persiste:**

1. **Ejecutar instalador automático:**
   ```
   http://localhost/propeasy/install-laragon.php
   ```

2. **Verificar configuración de Laragon:**
   - Versión de PHP (recomendado 8.1+)
   - Extensiones PHP habilitadas
   - Configuración de Apache

3. **Revisar logs de error:**
   - `logs/error.log` (errores de PHP)
   - Logs de Apache en Laragon

4. **Verificar puertos:**
   - Puerto 80 (Apache)
   - Puerto 3306 (MySQL)

---

**¡PropEasy está optimizado para Laragon! 🎉** 