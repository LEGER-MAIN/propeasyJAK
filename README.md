# PropEasy - Plataforma de Bienes Raíces

PropEasy es una plataforma web completa para la venta y gestión de propiedades inmobiliarias, desarrollada en PHP con arquitectura MVC.

## 🏗️ Características Principales

### 👥 Gestión de Usuarios
- **Registro y autenticación** de usuarios
- **Perfiles diferenciados**: Clientes, Agentes Inmobiliarios y Administradores
- **Sistema de roles** con permisos específicos
- **Recuperación de contraseñas** por email

### 🏠 Gestión de Propiedades
- **Publicación de propiedades** con imágenes múltiples
- **Búsqueda avanzada** por ubicación, precio, características
- **Sistema de favoritos** para usuarios
- **Validación de propiedades** por administradores
- **Estados de publicación** (pendiente, aprobada, rechazada)

### 💼 Panel de Agentes
- **Dashboard personalizado** con estadísticas
- **Gestión de propiedades** propias
- **Perfil público** personalizable
- **Sistema de citas** y agenda
- **Chat interno** con clientes

### 🛒 Sistema de Solicitudes
- **Solicitudes de compra** para propiedades
- **Seguimiento de estado** en tiempo real
- **Notificaciones automáticas**
- **Historial de transacciones**

### 📊 Reportes y Moderación
- **Sistema de reportes** de irregularidades
- **Panel de administración** completo
- **Notificaciones por email** automáticas
- **Gestión de contenido** inapropiado

### 💬 Chat en Tiempo Real
- **WebSocket** para comunicación instantánea
- **Chat privado** entre clientes y agentes
- **Notificaciones** en tiempo real
- **Historial de conversaciones**

## 🚀 Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone [url-del-repositorio]
cd propeasy
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar base de datos**
```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE propeasy_db;
USE propeasy_db;

# Importar esquema
mysql -u root -p propeasy_db < database/scheme.sql
```

4. **Configurar archivo de configuración**
```bash
# Editar config/config.php
# Configurar credenciales de base de datos y configuración de email
```

5. **Configurar servidor web**
- Apuntar el document root a la carpeta `public/`
- Configurar URL rewriting para Apache/Nginx

6. **Configurar permisos**
```bash
chmod 755 public/uploads/
chmod 755 logs/
```

## 📁 Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas y templates
│   ├── core/           # Núcleo del framework
│   └── helpers/        # Utilidades y helpers
├── config/             # Configuración
├── database/           # Esquemas de BD
├── docs/              # Documentación
├── logs/              # Logs del sistema
├── public/            # Archivos públicos
│   ├── css/           # Estilos
│   ├── js/            # JavaScript
│   └── uploads/       # Archivos subidos
└── vendor/            # Dependencias Composer
```

## 🔧 Configuración

### Base de Datos
Editar `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
```

### Email
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_password');
define('SMTP_FROM', 'noreply@propeasy.com');
```

### URLs
```php
define('BASE_URL', 'http://localhost/propeasy');
define('UPLOADS_URL', BASE_URL . '/uploads');
```

## 🎨 Tecnologías Utilizadas

### Backend
- **PHP 7.4+** - Lenguaje principal
- **MySQL** - Base de datos
- **Composer** - Gestión de dependencias

### Frontend
- **HTML5/CSS3** - Estructura y estilos
- **JavaScript (ES6+)** - Interactividad
- **Tailwind CSS** - Framework de estilos
- **Alpine.js** - Reactividad del frontend

### Comunicación
- **WebSocket** - Chat en tiempo real
- **PHPMailer** - Envío de emails
- **AJAX** - Peticiones asíncronas

## 🔐 Seguridad

- **Validación de entrada** en todos los formularios
- **Sanitización de datos** antes de almacenar
- **Protección CSRF** en formularios
- **Autenticación segura** con hash de contraseñas
- **Control de acceso** basado en roles
- **Validación de archivos** subidos

## 📧 Notificaciones

El sistema envía emails automáticos para:
- **Verificación de cuenta**
- **Recuperación de contraseña**
- **Nuevas solicitudes de compra**
- **Reportes de irregularidades**
- **Respuestas a reportes**
- **Notificaciones de chat**

## 🚀 Despliegue

### Producción
1. Configurar servidor web (Apache/Nginx)
2. Configurar SSL/HTTPS
3. Optimizar configuración PHP
4. Configurar backup automático
5. Monitoreo de logs

### Backup
```bash
# Backup de base de datos
mysqldump -u usuario -p propeasy_db > backup.sql

# Backup de archivos
tar -czf propeasy_backup.tar.gz propeasy/
```

## 🐛 Solución de Problemas

### Logs
- **Error logs**: `logs/error.log`
- **WebSocket logs**: Consola del servidor

### Problemas Comunes
1. **Permisos de archivos**: Verificar permisos en `public/uploads/`
2. **Configuración de email**: Verificar credenciales SMTP
3. **WebSocket**: Verificar puerto y firewall
4. **Base de datos**: Verificar conexión y credenciales

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo LICENSE para más detalles.

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama para feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## 📞 Soporte

Para soporte técnico, contactar a: `propeasy.soporte@gmail.com`

---

**PropEasy** - Simplificando la venta de propiedades inmobiliarias 🏠✨ 