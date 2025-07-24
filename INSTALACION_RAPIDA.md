# 🚀 Instalación Rápida - PropEasy

## 📋 Requisitos Previos

- **Laragon** instalado y ejecutándose
- **MySQL** y **Apache** activos en Laragon
- **PHP 8.0+** configurado

## ⚡ Instalación Automática (Recomendada)

### 1. Copiar el Proyecto
```bash
# Copia la carpeta propeasy a tu directorio de Laragon
# Normalmente: C:\laragon\www\propeasy
```

### 2. Ejecutar el Instalador
```bash
# Abre la terminal de Laragon (o PowerShell)
cd C:\laragon\www\propeasy

# Ejecuta el script de instalación
php install_propeasy.php
```

### 3. ¡Listo!
El script automáticamente:
- ✅ Verifica requisitos del sistema
- ✅ Crea estructura de directorios
- ✅ Configura la base de datos
- ✅ Instala dependencias
- ✅ Verifica la instalación

## 🌐 Acceso al Sistema

Una vez completada la instalación:

- **URL Principal**: http://localhost/propeasy
- **Panel Admin**: http://localhost/propeasy/admin/dashboard
- **Base de Datos**: propeasy_db (creada automáticamente)

## 👤 Primer Usuario

1. Accede a http://localhost/propeasy
2. Haz clic en "Registrarse"
3. Crea una cuenta con rol "Administrador"
4. ¡Ya puedes usar el sistema!

## 🔧 Configuración Adicional (Opcional)

### Cambiar Credenciales de BD
Edita `config/database.php`:
```php
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### Configurar Email
Edita `config/config.php`:
```php
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_contraseña_app');
```

## 🆘 Solución de Problemas

### Error: "MySQL no conecta"
- Verifica que MySQL esté ejecutándose en Laragon
- Asegúrate de que el usuario 'root' no tenga contraseña

### Error: "mod_rewrite no funciona"
- En Laragon, ve a Apache > mod_rewrite > Enable

### Error: "Permisos de directorio"
- Verifica que los directorios `logs/` y `public/uploads/` sean escribibles

## 📞 Soporte

- **Email**: propeasycorp@gmail.com
- **Teléfono**: +1 809 359 5322
- **Documentación**: README.md

---

**PropEasy v2.9.0** - Sistema Web de Venta de Bienes Raíces 🇩🇴 