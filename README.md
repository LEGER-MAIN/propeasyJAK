# PropEasy - Sistema Web de Venta de Bienes Raíces

## 📋 Descripción

PropEasy es un sistema web completo para la gestión y venta de bienes raíces. Permite a agentes inmobiliarios publicar propiedades, a clientes buscar y solicitar compras, y a administradores gestionar todo el sistema.

## ✨ Características Principales

### 🏠 Gestión de Propiedades
- Publicación de propiedades con múltiples imágenes
- Categorización por tipo (casa, apartamento, terreno, local comercial, oficina)
- Filtros avanzados de búsqueda
- Sistema de favoritos
- Validación de propiedades por administradores

### 👥 Gestión de Usuarios
- **Clientes**: Buscar propiedades, crear solicitudes de compra, gestionar favoritos
- **Agentes**: Publicar propiedades, gestionar solicitudes, chat con clientes
- **Administradores**: Gestión completa del sistema, validación de propiedades

### 💬 Sistema de Comunicación
- Chat interno entre agentes y clientes
- Sistema de solicitudes de compra
- Notificaciones por email (configurable)
- Mensajería directa

### 📊 Dashboard y Reportes
- Dashboard personalizado por rol de usuario
- Estadísticas de propiedades y solicitudes
- Reportes de actividad
- Gestión de citas y reuniones

## 🚀 Instalación

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 8.0 o superior
- Composer
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd propeasy
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar base de datos**
   - Crear base de datos MySQL
   - Importar el archivo `database/schema.sql`
   - Configurar credenciales en `config/database.php`

4. **Configurar la aplicación**
   - Editar `config/config.php` con las configuraciones de tu entorno
   - Configurar SMTP para emails (opcional)

5. **Configurar servidor web**
   - Apuntar el DocumentRoot a la carpeta `public/`
   - Configurar URL rewriting para Apache

### Configuración del Servidor Web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 📁 Estructura del Proyecto

```
propeasy/
├── app/                    # Código de la aplicación
│   ├── controllers/        # Controladores
│   ├── models/            # Modelos de datos
│   ├── views/             # Vistas
│   ├── core/              # Núcleo del sistema
│   └── helpers/           # Helpers y utilidades
├── config/                # Configuraciones
├── database/              # Esquemas de base de datos
├── public/                # Archivos públicos
│   ├── js/               # JavaScript del cliente
│   ├── uploads/          # Archivos subidos
│   └── index.php         # Punto de entrada
├── logs/                  # Logs del sistema
├── uploads/               # Archivos de propiedades
└── vendor/                # Dependencias de Composer
```

## 🔧 Configuración

### Variables de Entorno

Editar `config/config.php`:

```php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');

// Configuración de email (opcional)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_contraseña_app');
```

### Roles de Usuario

- **cliente**: Usuarios que buscan propiedades
- **agente**: Agentes inmobiliarios que publican propiedades
- **admin**: Administradores del sistema

## 🎯 Funcionalidades por Rol

### 👤 Cliente
- Registro e inicio de sesión
- Búsqueda de propiedades con filtros
- Agregar propiedades a favoritos
- Crear solicitudes de compra
- Chat con agentes
- Ver historial de solicitudes

### 🏢 Agente
- Publicar propiedades con imágenes
- Gestionar solicitudes de compra
- Chat con clientes
- Ver estadísticas de propiedades
- Agendar citas con clientes

### 👨‍💼 Administrador
- Gestión de usuarios
- Validación de propiedades
- Reportes del sistema
- Configuración general
- Gestión de irregularidades

## 🔒 Seguridad

- Autenticación con sesiones seguras
- Validación de CSRF tokens
- Sanitización de datos de entrada
- Control de acceso por roles
- Encriptación de contraseñas
- Validación de archivos subidos

## 📧 Sistema de Emails

El sistema incluye notificaciones por email para:
- Verificación de cuentas
- Recuperación de contraseñas
- Notificaciones de solicitudes
- Actualizaciones de estado

**Nota**: La configuración de email es opcional. El sistema funciona sin emails.

## 🐛 Debugging

### Modo Desarrollo
```php
define('APP_ENV', 'development');
```

### Logs
Los logs se guardan en `logs/error.log`

### Errores
En modo desarrollo se muestran errores detallados
En producción se muestran páginas de error genéricas

## 📊 Base de Datos

### Tablas Principales
- `usuarios`: Usuarios del sistema
- `propiedades`: Propiedades inmobiliarias
- `solicitudes_compra`: Solicitudes de compra
- `favoritos_propiedades`: Favoritos de usuarios
- `mensajes_chat`: Mensajes del chat
- `imagenes_propiedades`: Imágenes de propiedades

### Backup
```bash
mysqldump -u usuario -p propeasy_db > backup.sql
```

## 🚀 Despliegue

### Producción
1. Cambiar `APP_ENV` a `production`
2. Configurar HTTPS
3. Optimizar base de datos
4. Configurar backup automático
5. Configurar monitoreo de logs

### Optimizaciones
- Habilitar caché de PHP
- Optimizar imágenes
- Configurar CDN para archivos estáticos
- Optimizar consultas de base de datos

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o preguntas:
- Email: soporte@propeasy.com
- Documentación: [URL_DOCUMENTACION]
- Issues: [URL_REPOSITORIO]/issues

## 🔄 Changelog

### v1.0.0
- Sistema base completo
- Gestión de propiedades
- Sistema de usuarios y roles
- Chat interno
- Solicitudes de compra
- Sistema de favoritos
- Dashboard por roles
- Validación de propiedades

---

**PropEasy** - Simplificando la venta de bienes raíces 🏠 