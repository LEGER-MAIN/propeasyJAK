# PropEasy - Sistema de Gestión Inmobiliaria

## Descripción

PropEasy es una plataforma web completa para la gestión inmobiliaria que permite a agentes inmobiliarios gestionar propiedades, citas, clientes y comunicaciones de manera eficiente. El sistema incluye funcionalidades para clientes, agentes y administradores.

## Características Principales

### Para Agentes Inmobiliarios
- Dashboard personalizado con estadísticas
- Gestión de propiedades (crear, editar, publicar)
- Sistema de citas y agenda
- Chat interno con clientes
- Perfil público personalizable
- Gestión de solicitudes de compra

### Para Clientes
- Búsqueda avanzada de propiedades
- Sistema de favoritos
- Solicitudes de compra
- Chat con agentes
- Historial de actividades
- Reportes de irregularidades

### Para Administradores
- Panel de administración completo
- Gestión de usuarios y roles
- Validación de propiedades
- Reportes y estadísticas
- Configuración del sistema

## Tecnologías Utilizadas

- **Backend**: PHP 8.0+
- **Base de Datos**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Tailwind CSS
- **Iconos**: Font Awesome
- **WebSockets**: Para chat en tiempo real
- **Email**: PHPMailer para notificaciones

## Requisitos del Sistema

- PHP 8.0 o superior
- MySQL 5.7 o MariaDB 10.2 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP: mysqli, json, session, mbstring

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd propeasy
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar la base de datos**
   - Crear una base de datos MySQL
   - Importar el esquema de la base de datos
   - Configurar las credenciales en `config/config.php`

4. **Configurar el servidor web**
   - Configurar el document root en la carpeta `public/`
   - Asegurar que las carpetas `uploads/` y `logs/` tengan permisos de escritura

5. **Configurar variables de entorno**
   - Editar `config/config.php` con los datos de tu entorno
   - Configurar las credenciales de email para notificaciones

## Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/     # Controladores de la aplicación
│   ├── core/           # Núcleo del framework
│   ├── helpers/        # Funciones auxiliares
│   ├── models/         # Modelos de datos
│   └── views/          # Vistas de la aplicación
├── config/             # Configuración del sistema
├── database/           # Archivos de base de datos
├── logs/               # Archivos de log
├── public/             # Archivos públicos (document root)
├── scripts/            # Scripts de mantenimiento
└── vendor/             # Dependencias de Composer
```

## Configuración

### Base de Datos
Editar `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### Email
Configurar las credenciales SMTP en `config/config.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_contraseña');
```

## Uso

### Acceso al Sistema
- **URL principal**: `http://localhost/propeasy`
- **Login**: `http://localhost/propeasy/login`
- **Registro**: `http://localhost/propeasy/register`

### Roles de Usuario
1. **Cliente**: Puede buscar propiedades, crear favoritos, solicitar citas
2. **Agente**: Puede gestionar propiedades, citas y comunicarse con clientes
3. **Administrador**: Acceso completo al sistema y gestión de usuarios

## Funcionalidades Principales

### Gestión de Propiedades
- Crear y editar propiedades
- Subir imágenes
- Configurar precios y características
- Estados de validación

### Sistema de Citas
- Calendario interactivo
- Solicitudes de cita
- Confirmaciones automáticas
- Recordatorios por email

### Chat en Tiempo Real
- Comunicación directa entre agentes y clientes
- Historial de conversaciones
- Notificaciones instantáneas

### Sistema de Reportes
- Reportes de irregularidades
- Estadísticas de uso
- Análisis de rendimiento

## Mantenimiento

### Logs
Los logs del sistema se almacenan en `logs/` y incluyen:
- Errores de aplicación
- Actividad de usuarios
- Transacciones de base de datos

### Backups
Se recomienda realizar backups regulares de:
- Base de datos
- Archivos de configuración
- Imágenes de propiedades

### Actualizaciones
Para actualizar el sistema:
1. Hacer backup de la base de datos
2. Actualizar archivos del código
3. Ejecutar migraciones si las hay
4. Verificar la funcionalidad

## Soporte

Para soporte técnico o reportar problemas:
- Revisar los logs en `logs/`
- Verificar la configuración en `config/config.php`
- Comprobar permisos de archivos y carpetas

## Licencia

Este proyecto está bajo licencia [especificar licencia].

## Contribución

Para contribuir al proyecto:
1. Fork del repositorio
2. Crear una rama para tu feature
3. Realizar commits descriptivos
4. Crear un Pull Request

---

**PropEasy** - Simplificando la gestión inmobiliaria 