# PropEasy - Sistema Web de Venta de Bienes Raíces

![PropEasy Logo](https://img.shields.io/badge/PropEasy-Real%20Estate%20Platform-blue)
![PHP Version](https://img.shields.io/badge/PHP-8.0+-green)
![MySQL Version](https://img.shields.io/badge/MySQL-8.0+-orange)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Descripción

PropEasy es una plataforma web completa para la gestión y venta de bienes raíces. Permite a agentes inmobiliarios publicar propiedades, gestionar clientes, y facilitar la comunicación entre compradores y vendedores a través de un sistema de chat integrado.

## ✨ Características Principales

### 🏠 Gestión de Propiedades
- **Publicación de propiedades** con múltiples imágenes
- **Sistema de validación** para agentes y administradores
- **Búsqueda avanzada** por tipo, ciudad, precio y características
- **Galería de imágenes** con miniaturas
- **Estados de publicación** (activa, en revisión, vendida, rechazada)

### 👥 Gestión de Usuarios
- **Múltiples roles**: Clientes, Agentes, Administradores
- **Perfiles públicos** para agentes
- **Sistema de autenticación** seguro
- **Verificación de email**
- **Recuperación de contraseñas**

### 💬 Sistema de Chat Integrado
- **Chat en tiempo real** entre clientes y agentes
- **Conversaciones directas** sin necesidad de solicitudes
- **Notificaciones** de mensajes no leídos
- **Historial de conversaciones**
- **Búsqueda de usuarios** para iniciar chats

### 📅 Sistema de Citas
- **Agendamiento de visitas** a propiedades
- **Calendario integrado**
- **Notificaciones automáticas**
- **Estados de cita** (pendiente, aceptada, rechazada, completada)

### ❤️ Sistema de Favoritos
- **Guardado de propiedades** favoritas
- **Lista personalizada** para cada usuario
- **Acceso rápido** a propiedades de interés

### 📝 Solicitudes de Compra
- **Formularios de solicitud** para propiedades
- **Seguimiento de estado** de solicitudes
- **Comunicación integrada** con agentes

### 📊 Panel de Administración
- **Dashboard completo** con estadísticas
- **Gestión de usuarios** y roles
- **Aprobación de propiedades**
- **Sistema de reportes** y logs
- **Configuración del sistema**
- **Backup y restauración**

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.0+** - Lenguaje principal
- **MySQL 8.0+** - Base de datos
- **Arquitectura MVC** - Patrón de diseño
- **Sistema de rutas** personalizado
- **WebSockets** - Chat en tiempo real

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos y diseño responsive
- **JavaScript (ES6+)** - Interactividad
- **Tailwind CSS** - Framework de estilos
- **Font Awesome** - Iconografía

### Herramientas
- **Composer** - Gestión de dependencias
- **Git** - Control de versiones
- **Laragon** - Entorno de desarrollo local

## 📦 Instalación

### Requisitos Previos
- PHP 8.0 o superior
- MySQL 8.0 o superior
- Composer
- Servidor web (Apache/Nginx)
- Extensión PHP para WebSockets (opcional)

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
   - Configurar las credenciales en `config/database.php`

4. **Configurar el servidor web**
   - Apuntar el document root a la carpeta `public/`
   - Configurar las reglas de rewrite en `.htaccess`

5. **Configurar permisos**
   ```bash
   chmod 755 public/uploads/
   chmod 755 logs/
   ```

6. **Configurar variables de entorno**
   - Copiar y configurar `config/config.php`
   - Ajustar URLs y configuraciones según el entorno

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

## 🏗️ Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/          # Controladores MVC
│   ├── models/              # Modelos de datos
│   ├── views/               # Vistas y templates
│   ├── core/                # Núcleo del sistema
│   ├── helpers/             # Funciones auxiliares
│   └── websocket_server.php # Servidor WebSocket
├── config/                  # Configuraciones
├── database/                # Esquemas de BD
├── logs/                    # Archivos de log
├── public/                  # Documentos públicos
│   ├── css/                 # Estilos
│   ├── js/                  # JavaScript
│   ├── uploads/             # Archivos subidos
│   └── index.php            # Punto de entrada
├── scripts/                 # Scripts de mantenimiento
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
- Buscar propiedades
- Guardar favoritos
- Contactar agentes por chat
- Solicitar citas
- Enviar solicitudes de compra

#### 🏠 Agente
- Publicar propiedades
- Gestionar perfil público
- Responder consultas de clientes
- Gestionar citas
- Ver estadísticas de propiedades

#### 👨‍💼 Administrador
- Gestionar usuarios y roles
- Aprobar/rechazar propiedades
- Ver estadísticas del sistema
- Configurar parámetros
- Gestionar reportes

## 🔧 Configuración

### Archivos de Configuración

- `config/database.php` - Configuración de base de datos
- `config/config.php` - Configuración general del sistema
- `public/.htaccess` - Reglas de rewrite
- `composer.json` - Dependencias PHP

### Variables de Entorno

```php
// config/config.php
define('APP_NAME', 'PropEasy');
define('APP_URL', 'http://localhost/propeasy');
define('APP_PATH', __DIR__ . '/../app');
define('UPLOAD_PATH', __DIR__ . '/../public/uploads');
```

## 📊 Base de Datos

### Tablas Principales

- `usuarios` - Información de usuarios
- `propiedades` - Catálogo de propiedades
- `mensajes_chat` - Mensajes del chat
- `conversaciones_directas` - Conversaciones directas
- `citas` - Sistema de citas
- `favoritos` - Propiedades favoritas
- `solicitudes_compra` - Solicitudes de compra

## 🔒 Seguridad

- **Autenticación segura** con hash de contraseñas
- **Validación de entrada** en todos los formularios
- **Protección CSRF** en formularios críticos
- **Sanitización de datos** antes de almacenar
- **Control de acceso** basado en roles
- **Logs de actividad** para auditoría

## 🧪 Testing

Para ejecutar las pruebas del sistema:

```bash
# Verificar sintaxis PHP
php -l app/controllers/
php -l app/models/

# Verificar configuración de base de datos
php scripts/test_connection.php
```

## 📈 Mantenimiento

### Scripts Disponibles

- `scripts/seed_activity_logs.php` - Generar logs de actividad
- `scripts/send_appointment_reminders.php` - Enviar recordatorios de citas

### Logs del Sistema

- `logs/error.log` - Errores del sistema
- `logs/activity.log` - Actividad de usuarios
- `logs/chat.log` - Actividad del chat

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**PropEasy Team**
- Email: contacto@propeasy.com
- Sitio web: https://propeasy.com

## 🙏 Agradecimientos

- **Laragon** por el entorno de desarrollo
- **Tailwind CSS** por el framework de estilos
- **Font Awesome** por los iconos
- **Composer** por la gestión de dependencias

## 📞 Soporte

Para soporte técnico o consultas:
- 📧 Email: soporte@propeasy.com
- 📱 WhatsApp: +1 234 567 8900
- 🌐 Sitio web: https://propeasy.com/soporte

---

**PropEasy** - Simplificando la venta de bienes raíces desde 2024 🏠✨ 