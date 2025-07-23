# 🏠 PropEasy - Sistema Web de Venta de Bienes Raíces

[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-green.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

## 📋 Descripción

PropEasy es una plataforma web integral diseñada para optimizar la gestión inmobiliaria mediante la digitalización de procesos y centralización de información. El sistema facilita la visualización masiva de propiedades para los clientes, sirve como intermediario entre clientes y agentes inmobiliarios, y permite a la empresa registrar y monitorear en detalle todas las transacciones y actividades comerciales.

## ✨ Características Principales

### 🏠 Gestión de Propiedades
- **Publicación estructurada** con formularios validados
- **Sistema de validación** por tokens para certificar veracidad
- **Búsqueda avanzada** con múltiples filtros
- **Control de estados** (En revisión, Activa, Vendida, Rechazada)
- **Gestión de imágenes** múltiples por propiedad

### 👥 Sistema de Usuarios y Roles
- **Tres roles principales**: Cliente, Agente Inmobiliario, Administrador
- **Autenticación segura** con confirmación por email
- **Recuperación de contraseñas** mediante email
- **Control de acceso** basado en roles (RBAC)
- **Perfil unificado** con edición completa de información
- **Fotos de perfil** con subida y gestión de archivos
- **Campos específicos por rol** (experiencia, especialidades para agentes)

### 💬 Chat en Tiempo Real
- **Chat interno** entre cliente y agente por propiedad
- **Chat directo** sin necesidad de solicitudes
- **WebSocket** con Ratchet PHP para tiempo real
- **Historial persistente** de todos los mensajes
- **Notificaciones** de nuevos mensajes
- **Filtrado por roles** para privacidad

### 📅 Sistema de Citas
- **Propuesta de citas** desde chat o panel del agente
- **Estados de citas** (Propuesta, Aceptada, Rechazada, Completada, Cancelada)
- **Agenda integrada** para agentes y clientes
- **Recordatorios automáticos** por email

### 📋 Solicitudes de Compra
- **Formularios de interés** con registro automático
- **Seguimiento de estado** completo
- **Notificaciones automáticas** al agente

### 📊 Dashboards y Reportes
- **Dashboard Administrativo** con estadísticas globales
- **Dashboard de Agente** con métricas individuales
- **Perfil Público del Agente** para transparencia
- **Reportes detallados** de ventas, usuarios, propiedades

### 🚨 Sistema de Reportes
- **Formularios de queja** con adjuntos opcionales
- **Seguimiento de estado** (Pendiente, Atendido, Descartado)
- **Gestión administrativa** con panel de revisión

### ⭐ Sistema de Favoritos
- **Guardado de propiedades** en lista personalizada
- **Seguimiento** con notificaciones de cambios

### 🔍 Búsqueda Avanzada
- **Búsqueda de agentes** con filtros múltiples
- **Búsqueda de clientes** para gestión de base de datos
- **Filtros combinados** para resultados precisos

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.2+** - Lógica de negocio y procesamiento
- **MySQL 8.0+** - Base de datos relacional
- **Apache** - Servidor web
- **PHPMailer** - Envío de emails
- **Ratchet PHP** - WebSocket para chat en tiempo real

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos y diseño responsivo
- **JavaScript** - Interactividad y validaciones
- **Bootstrap 5.3** - Framework CSS responsivo
- **Chart.js** - Gráficos y visualizaciones
- **Font Awesome** - Iconografía

### Características Técnicas
- **Arquitectura MVC** - Separación clara de responsabilidades
- **Base de datos normalizada** - Optimizada para consultas eficientes
- **Sistema de rutas personalizado** - Enrutamiento flexible
- **Validación robusta** - Sanitización y validación de datos
- **Manejo de errores completo** - Logging y gestión de errores
- **Seguridad avanzada** - Hashing, tokens CSRF, validación de sesiones
- **Responsive design** - Optimizado para móviles y tablets

## 📁 Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── AdminController.php
│   │   ├── AgenteController.php
│   │   ├── ApiController.php
│   │   ├── AppointmentController.php
│   │   ├── AuthController.php
│   │   ├── ChatController.php
│   │   ├── ClienteController.php
│   │   ├── DashboardController.php
│   │   ├── FavoriteController.php
│   │   ├── HomeController.php
│   │   ├── PropertyController.php
│   │   ├── ProfileController.php      # ✨ Perfil unificado
│   │   ├── RealtimeChatController.php
│   │   ├── ReporteController.php
│   │   ├── SearchController.php
│   │   ├── SimpleController.php
│   │   └── SolicitudController.php
│   ├── core/                 # Núcleo del sistema
│   │   ├── Database.php      # Clase de conexión a BD
│   │   └── Router.php        # Sistema de rutas
│   ├── helpers/              # Funciones auxiliares
│   │   ├── EmailHelper.php
│   │   └── PropertyHelper.php
│   ├── models/               # Modelos de datos
│   │   ├── ActivityLog.php
│   │   ├── Appointment.php
│   │   ├── Chat.php
│   │   ├── Favorite.php
│   │   ├── Property.php
│   │   ├── ReporteIrregularidad.php
│   │   ├── SolicitudCompra.php
│   │   └── User.php
│   ├── views/                # Vistas del sistema
│   │   ├── admin/            # Panel administrativo
│   │   ├── agente/           # Panel de agentes
│   │   ├── auth/             # Autenticación
│   │   ├── chat/             # Chat en tiempo real
│   │   ├── cliente/          # Panel de clientes
│   │   ├── components/       # Componentes reutilizables
│   │   ├── errors/           # Páginas de error
│   │   ├── home/             # Página principal
│   │   ├── layouts/          # Layouts principales
│   │   ├── profile/          # ✨ Perfil unificado
│   │   ├── properties/       # Gestión de propiedades
│   │   ├── reportes/         # Sistema de reportes
│   │   └── search/           # Búsquedas
│   └── websocket_server.php  # Servidor WebSocket
├── config/                   # Configuración
│   ├── config.php            # Configuración general
│   └── database.php          # Configuración de BD
├── database/                 # Base de datos
│   └── scheme.sql            # Esquema de BD
├── logs/                     # Archivos de log
├── public/                   # Directorio público
│   ├── css/                  # Estilos CSS
│   ├── js/                   # JavaScript
│   ├── uploads/              # Archivos subidos
│   │   ├── profiles/         # ✨ Fotos de perfil
│   │   ├── properties/       # Imágenes de propiedades
│   │   └── reportes/         # Archivos de reportes
│   └── index.php             # Punto de entrada
├── scripts/                  # Scripts de mantenimiento
├── vendor/                   # Dependencias de Composer
├── .gitignore               # Archivos ignorados por Git
├── .htaccess                # Configuración de Apache
├── composer.json            # Dependencias de PHP
└── README.md                # Este archivo
```

## 🚀 Instalación

### Requisitos Previos
- **PHP 8.2 o superior**
- **MySQL 8.0 o superior**
- **Apache/Nginx** con mod_rewrite habilitado
- **Composer** para gestión de dependencias
- **Extensiones PHP**: PDO, PDO_MySQL, OpenSSL, mbstring, fileinfo

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/propeasy.git
   cd propeasy
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar la base de datos**
   - Crear una base de datos MySQL
   - Importar el esquema: `database/scheme.sql`
   - Copiar `config/database.example.php` a `config/database.php`
   - Configurar las credenciales en `config/database.php`

4. **Configurar el servidor web**
   - Apuntar el document root a la carpeta `public/`
   - Habilitar mod_rewrite para Apache
   - Configurar permisos de escritura en `public/uploads/`

5. **Configurar email**
   - Actualizar configuración SMTP en `config/config.php`
   - Configurar credenciales de Gmail o servidor SMTP

6. **Iniciar el servidor WebSocket** (opcional, para chat en tiempo real)
   ```bash
   php app/websocket_server.php
   ```

## ⚙️ Configuración

### Variables de Entorno
Las principales configuraciones se encuentran en `config/config.php`:

```php
// Configuración de la aplicación
define('APP_NAME', 'PropEasy');
define('APP_URL', 'http://localhost');
define('APP_ENV', 'production');

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-contraseña-de-aplicación');
```

### Configuración de Base de Datos
En `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy');
define('DB_USER', 'tu-usuario');
define('DB_PASS', 'tu-contraseña');
```

## 🔧 Uso del Sistema

### Roles de Usuario

#### 👤 Cliente
- **Registro y autenticación**
- **Búsqueda de propiedades** con filtros avanzados
- **Sistema de favoritos**
- **Solicitudes de compra**
- **Chat con agentes**
- **Gestión de citas**
- **Perfil personal** con foto

#### 🏢 Agente Inmobiliario
- **Dashboard personal** con estadísticas
- **Gestión de propiedades** (crear, editar, validar)
- **Perfil público** con información profesional
- **Chat con clientes**
- **Gestión de citas**
- **Seguimiento de solicitudes**
- **Perfil profesional** con experiencia y especialidades

#### 👨‍💼 Administrador
- **Panel de control completo**
- **Gestión de usuarios** y roles
- **Estadísticas globales**
- **Validación de propiedades**
- **Gestión de reportes**
- **Configuración del sistema**
- **Backups y mantenimiento**

### Funcionalidades Principales

#### 🏠 Gestión de Propiedades
1. **Crear propiedad**: Formulario completo con validación
2. **Editar propiedad**: Modificar información existente
3. **Validar propiedad**: Sistema de tokens para verificación
4. **Buscar propiedades**: Filtros por tipo, precio, ubicación
5. **Gestionar imágenes**: Subida múltiple con vista previa

#### 💬 Chat en Tiempo Real
1. **Chat por propiedad**: Conversación específica sobre una propiedad
2. **Chat directo**: Comunicación general entre cliente y agente
3. **Notificaciones**: Alertas de nuevos mensajes
4. **Historial**: Persistencia de todas las conversaciones

#### 📅 Sistema de Citas
1. **Proponer cita**: Desde chat o panel del agente
2. **Aceptar/Rechazar**: Gestión de respuestas
3. **Calendario**: Vista organizada de citas
4. **Recordatorios**: Notificaciones automáticas

#### 👤 Perfil Unificado
1. **Información personal**: Nombre, teléfono, ubicación
2. **Foto de perfil**: Subida con validación
3. **Campos específicos**: Experiencia, especialidades (agentes)
4. **Cambio de contraseña**: Seguro con validación

## 🔒 Seguridad

### Medidas Implementadas
- **Hashing de contraseñas** con bcrypt
- **Tokens CSRF** en formularios
- **Validación de sesiones** en cada página
- **Sanitización de datos** de entrada
- **Control de acceso** basado en roles
- **Protección contra SQL Injection**
- **Validación de archivos** subidos

### Configuración de Seguridad
```php
// Configuración de seguridad en config/config.php
define('PASSWORD_COST', 12);           // Costo de hashing
define('SESSION_TIMEOUT', 3600);       // Timeout de sesión
define('MAX_LOGIN_ATTEMPTS', 5);       // Intentos de login
```

## 📊 Mantenimiento

### Scripts de Mantenimiento
- `scripts/send_appointment_reminders.php` - Recordatorios automáticos
- `scripts/seed_activity_logs.php` - Generación de logs de actividad

### Logs del Sistema
- **Error logs**: `logs/error.log`
- **Activity logs**: Base de datos
- **Access logs**: Servidor web

### Backups
- **Base de datos**: Exportación automática
- **Archivos**: Copia de seguridad de uploads
- **Configuración**: Backup de archivos de configuración

## 🐛 Solución de Problemas

### Problemas Comunes

#### Error de Conexión a Base de Datos
```bash
# Verificar configuración en config/database.php
# Comprobar que MySQL esté ejecutándose
# Verificar credenciales de acceso
```

#### Error de Permisos en Uploads
```bash
# Asignar permisos de escritura
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/
```

#### Chat No Funciona
```bash
# Verificar que el servidor WebSocket esté ejecutándose
php app/websocket_server.php
# Comprobar configuración de puertos
```

#### Emails No Se Envían
```bash
# Verificar configuración SMTP en config/config.php
# Comprobar credenciales de Gmail
# Verificar que la extensión OpenSSL esté habilitada
```

## 📈 Versiones

### v1.1.0 (2025-01-23) - Perfil Unificado
- ✨ **Perfil unificado** para todos los usuarios
- 📸 **Sistema de fotos de perfil** con validación
- 🔧 **Mejoras técnicas** en rutas y configuración
- 🐛 **Correcciones** de bugs y limpieza de código

### v1.0.0 (2025-01-20) - Lanzamiento Inicial
- 🎉 Sistema completo de gestión inmobiliaria
- 💬 Chat en tiempo real
- 📅 Sistema de citas
- 📊 Dashboard administrativo
- 🏠 Gestión de propiedades y usuarios

## 🤝 Contribución

1. **Fork** el proyecto
2. **Crear** una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. **Abrir** un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

- **Email**: propeasy.soporte@gmail.com
- **Teléfono**: 809 359 5322
- **Documentación**: Este README
- **Issues**: GitHub Issues

## 🙏 Agradecimientos

- **Bootstrap** por el framework CSS
- **Font Awesome** por los iconos
- **Chart.js** por las visualizaciones
- **Ratchet PHP** por el WebSocket
- **PHPMailer** por el envío de emails

---

**PropEasy** - Simplificando la gestión inmobiliaria 🏠✨ 