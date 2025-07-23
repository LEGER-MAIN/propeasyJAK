# PropEasy - Sistema Web de Venta de Bienes Raíces

![PropEasy Logo](https://img.shields.io/badge/PropEasy-Real%20Estate%20Platform-blue)
![PHP Version](https://img.shields.io/badge/PHP-8.0+-green)
![MySQL Version](https://img.shields.io/badge/MySQL-8.0+-orange)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Descripción

PropEasy es una plataforma web completa para la gestión y venta de bienes raíces. Permite a agentes inmobiliarios publicar propiedades, gestionar clientes, y facilitar la comunicación entre compradores y vendedores a través de un sistema de chat integrado y herramientas avanzadas de gestión.

## ✨ Características Principales

### 🏠 Gestión de Propiedades
- **Publicación de propiedades** con múltiples imágenes y validación
- **Sistema de validación** para agentes y administradores
- **Búsqueda avanzada** por tipo, ciudad, precio y características
- **Galería de imágenes** con miniaturas y carga optimizada
- **Estados de publicación** (activa, en revisión, vendida, rechazada)
- **Paginación inteligente** con 9 propiedades por página
- **Selección manual de agentes** con búsqueda y carga infinita

### 👥 Gestión de Usuarios
- **Múltiples roles**: Clientes, Agentes, Administradores
- **Perfiles públicos** para agentes con estadísticas
- **Sistema de autenticación** seguro con verificación de email
- **Recuperación de contraseñas** por email
- **Fotos de perfil** con gestión unificada
- **Gestión de sesiones** mejorada

### 💬 Sistema de Chat Integrado
- **Chat en tiempo real** entre clientes y agentes
- **Conversaciones directas** sin necesidad de solicitudes
- **Notificaciones** de mensajes no leídos
- **Historial de conversaciones** persistente
- **Búsqueda de usuarios** para iniciar chats
- **WebSockets** para comunicación en tiempo real

### 📅 Sistema de Citas
- **Agendamiento de visitas** a propiedades
- **Calendario integrado** con vista mensual
- **Notificaciones automáticas** por email
- **Estados de cita** (pendiente, aceptada, rechazada, completada)
- **Gestión de horarios** y disponibilidad

### ❤️ Sistema de Favoritos
- **Guardado de propiedades** favoritas
- **Lista personalizada** para cada usuario
- **Acceso rápido** a propiedades de interés
- **Contador de favoritos** en tiempo real

### 📝 Solicitudes de Compra
- **Formularios de solicitud** para propiedades
- **Seguimiento de estado** de solicitudes
- **Comunicación integrada** con agentes
- **Historial de solicitudes** por cliente

### 🔍 Búsqueda y Filtros Avanzados
- **Búsqueda por nombre completo** de agentes
- **Filtros por ciudad y sector**
- **Carga infinita** en listados de agentes
- **Búsqueda en tiempo real** con debounce
- **Selección visual** de agentes con tarjetas informativas

### 📊 Panel de Administración
- **Dashboard completo** con estadísticas en tiempo real
- **Gestión de usuarios** y roles con cambio de estado
- **Aprobación de propiedades** con comentarios
- **Sistema de reportes** y logs de actividad
- **Configuración del sistema** avanzada
- **Backup y restauración** automática
- **Gestión de favoritos** del administrador

### 📱 Interfaz de Usuario
- **Diseño responsive** para todos los dispositivos
- **Paleta de colores** profesional y moderna
- **Componentes reutilizables** con Tailwind CSS
- **Iconografía** con Font Awesome
- **Animaciones suaves** y transiciones
- **Mensajes flash** informativos

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.0+** - Lenguaje principal con características modernas
- **MySQL 8.0+** - Base de datos relacional optimizada
- **Arquitectura MVC** - Patrón de diseño escalable
- **Sistema de rutas** personalizado con parámetros dinámicos
- **WebSockets** - Chat en tiempo real con Ratchet
- **Composer** - Gestión de dependencias PHP

### Frontend
- **HTML5** - Estructura semántica y accesible
- **CSS3** - Estilos modernos y responsive
- **JavaScript (ES6+)** - Interactividad y AJAX
- **Tailwind CSS** - Framework de utilidades CSS
- **Font Awesome** - Iconografía profesional
- **Fetch API** - Comunicación asíncrona

### Herramientas y Servicios
- **Composer** - Gestión de dependencias
- **Git** - Control de versiones
- **Laragon** - Entorno de desarrollo local
- **PHPMailer** - Envío de emails
- **Ratchet** - Servidor WebSocket

## 📦 Instalación

### Requisitos Previos
- PHP 8.0 o superior
- MySQL 8.0 o superior
- Composer
- Servidor web (Apache/Nginx)
- Extensión PHP para WebSockets (opcional)
- Extensión PHP para GD (manipulación de imágenes)

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
   - Importar el archivo `database/scheme.sql`
   - Copiar `config/database.example.php` a `config/database.php`
   - Configurar las credenciales en `config/database.php`

4. **Configurar el servidor web**
   - Apuntar el document root a la carpeta `public/`
   - Configurar las reglas de rewrite en `.htaccess`
   - Asegurar que mod_rewrite esté habilitado

5. **Configurar permisos**
   ```bash
   chmod 755 public/uploads/
   chmod 755 public/uploads/profiles/
   chmod 755 public/uploads/properties/
   chmod 755 public/uploads/reportes/
   chmod 755 logs/
   ```

6. **Configurar variables de entorno**
   - Copiar y configurar `config/config.php`
   - Ajustar URLs y configuraciones según el entorno
   - Configurar zona horaria y configuraciones de email

### Configuración del Chat en Tiempo Real (Opcional)

Para habilitar el chat en tiempo real:

1. **Instalar dependencias de WebSocket**
   ```bash
   composer require cboden/ratchet
   ```

2. **Iniciar el servidor WebSocket**
   ```bash
   php app/websocket_server.php
   ```

3. **Configurar como servicio** (recomendado para producción)

## 🏗️ Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── AdminController.php
│   │   ├── AgenteController.php
│   │   ├── ApiController.php
│   │   ├── AuthController.php
│   │   ├── ChatController.php
│   │   ├── ClienteController.php
│   │   ├── PropertyController.php
│   │   └── ...
│   ├── models/              # Modelos de datos
│   │   ├── User.php
│   │   ├── Property.php
│   │   ├── Chat.php
│   │   ├── Appointment.php
│   │   └── ...
│   ├── views/               # Vistas y templates
│   │   ├── admin/           # Vistas de administración
│   │   ├── agente/          # Vistas de agentes
│   │   ├── cliente/         # Vistas de clientes
│   │   ├── auth/            # Vistas de autenticación
│   │   ├── properties/      # Vistas de propiedades
│   │   ├── chat/            # Vistas de chat
│   │   ├── components/      # Componentes reutilizables
│   │   └── layouts/         # Layouts principales
│   ├── core/                # Núcleo del sistema
│   │   ├── Database.php
│   │   └── Router.php
│   ├── helpers/             # Funciones auxiliares
│   │   ├── EmailHelper.php
│   │   └── PropertyHelper.php
│   └── websocket_server.php # Servidor WebSocket
├── config/                  # Configuraciones
│   ├── config.php
│   ├── database.php
│   └── database.example.php
├── database/                # Esquemas de BD
│   └── scheme.sql
├── logs/                    # Archivos de log
├── public/                  # Documentos públicos
│   ├── css/                 # Estilos
│   ├── js/                  # JavaScript
│   ├── uploads/             # Archivos subidos
│   │   ├── profiles/        # Fotos de perfil
│   │   ├── properties/      # Imágenes de propiedades
│   │   └── reportes/        # Reportes
│   ├── .htaccess            # Reglas de rewrite
│   └── index.php            # Punto de entrada
├── scripts/                 # Scripts de mantenimiento
│   ├── seed_activity_logs.php
│   └── send_appointment_reminders.php
├── vendor/                  # Dependencias de Composer
├── composer.json            # Configuración de Composer
└── README.md               # Este archivo
```

## 🚀 Uso

### Acceso al Sistema

1. **Acceder a la aplicación**: `http://localhost/propeasy`
2. **Registrarse** como cliente o agente
3. **Verificar email** (si está habilitado)
4. **Iniciar sesión** y comenzar a usar

### Roles de Usuario

#### 👤 Cliente
- **Buscar propiedades** con filtros avanzados
- **Guardar favoritos** y acceder rápidamente
- **Contactar agentes** por chat en tiempo real
- **Solicitar citas** para visitar propiedades
- **Enviar solicitudes de compra** con detalles
- **Ver historial** de actividades
- **Gestionar perfil** personal

#### 🏠 Agente
- **Publicar propiedades** con múltiples imágenes
- **Gestionar perfil público** con estadísticas
- **Responder consultas** de clientes por chat
- **Gestionar citas** y horarios
- **Ver estadísticas** de propiedades
- **Validar propiedades** pendientes
- **Gestionar solicitudes** de compra

#### 👨‍💼 Administrador
- **Gestionar usuarios** y roles con cambio de estado
- **Aprobar/rechazar propiedades** con comentarios
- **Ver estadísticas** del sistema en tiempo real
- **Configurar parámetros** del sistema
- **Gestionar reportes** y logs de actividad
- **Realizar backups** y restauraciones
- **Monitorear actividad** del sistema

## 🔧 Configuración

### Archivos de Configuración

- `config/database.php` - Configuración de base de datos
- `config/config.php` - Configuración general del sistema
- `public/.htaccess` - Reglas de rewrite y seguridad
- `composer.json` - Dependencias PHP

### Variables de Entorno

```php
// config/config.php
define('APP_NAME', 'PropEasy');
define('APP_URL', 'http://localhost/propeasy');
define('APP_PATH', __DIR__ . '/../app');
define('PUBLIC_PATH', __DIR__ . '/../public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('UPLOADS_URL', '/uploads');

// Configuraciones de seguridad
define('SESSION_LIFETIME', 3600);
define('TOKEN_EXPIRY', 3600);
define('PASSWORD_RESET_EXPIRY', 1800);
```

## 📊 Base de Datos

### Tablas Principales

- `usuarios` - Información de usuarios y perfiles
- `propiedades` - Catálogo de propiedades inmobiliarias
- `imagenes_propiedades` - Imágenes de propiedades
- `mensajes_chat` - Mensajes del sistema de chat
- `conversaciones_directas` - Conversaciones directas
- `citas` - Sistema de citas y visitas
- `favoritos` - Propiedades favoritas de usuarios
- `solicitudes_compra` - Solicitudes de compra
- `reportes_irregularidades` - Reportes de problemas
- `logs_actividad` - Logs de actividad del sistema

### Relaciones y Constraint

- Claves foráneas para integridad referencial
- Índices optimizados para búsquedas
- Triggers para auditoría automática
- Vistas para consultas complejas

## 🔒 Seguridad

- **Autenticación segura** con hash bcrypt de contraseñas
- **Validación de entrada** en todos los formularios
- **Protección CSRF** en formularios críticos
- **Sanitización de datos** antes de almacenar
- **Control de acceso** basado en roles (RBAC)
- **Logs de actividad** para auditoría completa
- **Validación de archivos** subidos
- **Headers de seguridad** en respuestas HTTP
- **Sesiones seguras** con configuración optimizada

## 🧪 Testing

Para ejecutar las pruebas del sistema:

```bash
# Verificar sintaxis PHP
php -l app/controllers/
php -l app/models/

# Verificar configuración de base de datos
php scripts/test_connection.php

# Verificar estructura de directorios
php scripts/check_structure.php
```

## 📈 Mantenimiento

### Scripts Disponibles

- `scripts/seed_activity_logs.php` - Generar logs de actividad de prueba
- `scripts/send_appointment_reminders.php` - Enviar recordatorios de citas

### Logs del Sistema

- `logs/error.log` - Errores del sistema
- `logs/activity.log` - Actividad de usuarios
- `logs/chat.log` - Actividad del chat
- `logs/upload.log` - Logs de subida de archivos

### Tareas de Mantenimiento

- **Limpieza de logs** antiguos
- **Optimización de base de datos** periódica
- **Backup automático** de datos
- **Monitoreo de rendimiento**

## 🚀 Características Avanzadas

### Sistema de Búsqueda
- **Búsqueda por nombre completo** de agentes
- **Filtros combinados** por múltiples criterios
- **Carga infinita** para mejor rendimiento
- **Búsqueda en tiempo real** con debounce
- **Resultados paginados** optimizados

### Gestión de Imágenes
- **Carga múltiple** de imágenes
- **Validación de tipos** y tamaños
- **Optimización automática** de imágenes
- **Miniaturas generadas** automáticamente
- **Almacenamiento organizado** por tipo

### Sistema de Notificaciones
- **Notificaciones en tiempo real** por WebSocket
- **Emails automáticos** para eventos importantes
- **Recordatorios de citas** programados
- **Alertas de sistema** para administradores

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

### Guías de Contribución

- Seguir las convenciones de código PHP
- Documentar nuevas funcionalidades
- Incluir pruebas para nuevas features
- Mantener compatibilidad con versiones anteriores

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**PropEasy Team**
- Email: contacto@propeasy.com
- Sitio web: https://propeasy.com
- GitHub: https://github.com/propeasy

## 🙏 Agradecimientos

- **Laragon** por el entorno de desarrollo local
- **Tailwind CSS** por el framework de utilidades CSS
- **Font Awesome** por la iconografía profesional
- **Composer** por la gestión de dependencias PHP
- **Ratchet** por el servidor WebSocket
- **PHPMailer** por el envío de emails

## 📞 Soporte

Para soporte técnico o consultas:

- 📧 Email: soporte@propeasy.com
- 📱 WhatsApp: +1 234 567 8900
- 🌐 Sitio web: https://propeasy.com/soporte
- 📖 Documentación: https://docs.propeasy.com
- 🐛 Issues: https://github.com/propeasy/propeasy/issues

## 🔄 Changelog

### v2.0.0 (2024-12-23)
- ✨ Sistema de búsqueda mejorado para agentes
- 🖼️ Gestión unificada de fotos de perfil
- 🔍 Búsqueda por nombre completo con espacios
- 📱 Interfaz mejorada para selección de agentes
- 🐛 Correcciones de bugs en carga de imágenes
- ⚡ Optimizaciones de rendimiento

### v1.0.0 (2024-12-01)
- 🎉 Lanzamiento inicial
- 🏠 Sistema completo de gestión de propiedades
- 💬 Chat en tiempo real
- 👥 Gestión de usuarios y roles
- 📊 Panel de administración

---

**PropEasy** - Simplificando la venta de bienes raíces desde 2024 🏠✨ 