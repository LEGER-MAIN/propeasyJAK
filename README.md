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
│   │   ├── RealtimeChatController.php
│   │   ├── ReporteController.php
│   │   ├── SearchController.php
│   │   ├── SimpleController.php
│   │   └── SolicitudController.php
│   ├── core/                 # Núcleo del sistema
│   │   ├── Database.php
│   │   └── Router.php
│   ├── helpers/              # Funciones auxiliares
│   │   └── EmailHelper.php
│   ├── models/               # Modelos de datos
│   │   ├── ActivityLog.php
│   │   ├── Appointment.php
│   │   ├── Chat.php
│   │   ├── Favorite.php
│   │   ├── Property.php
│   │   ├── ReporteIrregularidad.php
│   │   ├── SolicitudCompra.php
│   │   └── User.php
│   ├── views/                # Vistas y templates
│   │   ├── admin/            # Vistas administrativas
│   │   ├── agente/           # Vistas de agentes
│   │   ├── auth/             # Vistas de autenticación
│   │   ├── chat/             # Vistas de chat
│   │   ├── cliente/          # Vistas de clientes
│   │   ├── components/       # Componentes reutilizables
│   │   ├── errors/           # Páginas de error
│   │   ├── home/             # Vistas principales
│   │   ├── layouts/          # Layouts principales
│   │   ├── properties/       # Vistas de propiedades
│   │   └── search/           # Vistas de búsqueda
│   └── websocket_server.php  # Servidor WebSocket
├── config/                   # Configuración
│   ├── config.php
│   └── database.php
├── database/                 # Base de datos
│   └── scheme.sql
├── logs/                     # Logs del sistema
├── public/                   # Archivos públicos
│   ├── css/                  # Estilos CSS
│   ├── js/                   # JavaScript
│   ├── uploads/              # Archivos subidos
│   └── index.php             # Punto de entrada
├── scripts/                  # Scripts de utilidad
│   ├── seed_activity_logs.php
│   └── send_appointment_reminders.php
├── vendor/                   # Dependencias Composer
├── .gitignore
├── .htaccess
├── composer.json
├── composer.lock
└── README.md
```

## 🚀 Instalación

### Requisitos Previos
- PHP 8.2 o superior
- MySQL 8.0 o superior
- Apache con mod_rewrite habilitado
- Composer (para dependencias)

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
   - Configurar el DocumentRoot en la carpeta `public/`
   - Asegurar que mod_rewrite esté habilitado

5. **Configurar permisos**
   ```bash
   chmod 755 public/uploads/
   chmod 755 logs/
   ```

6. **Configurar variables de entorno**
   - Editar `config/config.php` con las configuraciones del servidor
   - Configurar el envío de emails en `config/config.php`

### Configuración del WebSocket (Opcional)

Para el chat en tiempo real:

1. **Instalar Ratchet**
   ```bash
   composer require cboden/ratchet
   ```

2. **Ejecutar el servidor WebSocket**
   ```bash
   php app/websocket_server.php
   ```

## 🔧 Configuración

### Archivos de Configuración

- **`config/config.php`** - Configuración general del sistema
- **`config/database.php`** - Configuración de la base de datos
- **`public/.htaccess`** - Configuración de Apache

### Variables Importantes

```php
// config/config.php
define('APP_NAME', 'PropEasy');
define('APP_URL', 'http://localhost');
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_USERNAME', 'tu-email@gmail.com');
define('EMAIL_PASSWORD', 'tu-password');
```

## 📱 Características Responsive

- **Diseño móvil optimizado** con sidebar colapsable
- **Interfaz adaptativa** para tablets y smartphones
- **Navegación táctil** optimizada
- **Modales responsivas** para acciones importantes
- **Tablas adaptativas** con scroll horizontal

## 🔒 Seguridad

- **Hashing de contraseñas** con bcrypt
- **Tokens CSRF** para formularios
- **Validación de sesiones** robusta
- **Sanitización de datos** de entrada
- **Control de acceso** basado en roles
- **Logging de actividades** para auditoría

## 📊 Funcionalidades Administrativas

### Panel de Control Total
- **Dashboard con estadísticas** en tiempo real
- **Gestión de usuarios** completa
- **Gestión de propiedades** con validación
- **Sistema de reportes** de irregularidades
- **Logs del sistema** para monitoreo
- **Backup y restore** de base de datos
- **Configuración del sistema** centralizada

### Gestión de Reportes
- **Modales elegantes** para acciones
- **Filtros avanzados** por estado, prioridad, tipo
- **Búsqueda de texto** en títulos y descripciones
- **Exportación a CSV** de reportes
- **Vista detallada** de cada reporte

## 🧪 Testing

El proyecto incluye scripts de prueba para verificar funcionalidades:

```bash
# Verificar estructura de base de datos
php scripts/check_table_structure.php

# Generar logs de actividad de prueba
php scripts/seed_activity_logs.php

# Enviar recordatorios de citas
php scripts/send_appointment_reminders.php
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o preguntas:
- 📧 Email: soporte@propeasy.com
- 📱 WhatsApp: +1 234 567 8900
- 🌐 Website: https://propeasy.com

## 🗺️ Roadmap

### Próximas Características
- [ ] **API REST** para integración con apps móviles
- [ ] **Sistema de notificaciones push**
- [ ] **Integración con Google Maps**
- [ ] **Sistema de pagos online**
- [ ] **App móvil nativa**
- [ ] **Analytics avanzados**
- [ ] **Sistema de reseñas**
- [ ] **Integración con redes sociales**

---

**Desarrollado con ❤️ por el equipo de PropEasy**

*Sistema Web de Venta de Bienes Raíces - Versión 2.0* 