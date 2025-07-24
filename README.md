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
- **Botones "Ver Detalles"** con efectos hover mejorados
- **Carga de imágenes** optimizada con validación mejorada

### 👥 Gestión de Usuarios
- **Múltiples roles**: Clientes, Agentes, Administradores
- **Perfiles públicos** para agentes con estadísticas
- **Sistema de autenticación** seguro con verificación de email
- **Recuperación de contraseñas** por email
- **Fotos de perfil** con gestión unificada
- **Gestión de sesiones** mejorada
- **Corrección de rutas** de fotos de perfil
- **Variables de sesión** optimizadas
- **Dashboard personalizado** para clientes con estadísticas
- **Gestión de propiedades solicitadas** con eliminación segura

### 💬 Sistema de Chat Integrado
- **Chat en tiempo real** entre clientes y agentes
- **Conversaciones directas** sin necesidad de solicitudes
- **Notificaciones** de mensajes no leídos
- **Historial de conversaciones** persistente
- **Búsqueda de usuarios** para iniciar chats
- **WebSockets** para comunicación en tiempo real
- **Chat simple** con agente preseleccionado
- **Botones de contacto** unificados en todas las páginas
- **Redirección automática** al chat con agente seleccionado
- **Preselección automática** de conversaciones existentes
- **Creación automática** de nuevas conversaciones
- **Experiencia consistente** en todas las páginas del sistema

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
- **Seguimiento de estado** de solicitudes (nuevo, en revisión, reunión agendada, cerrado)
- **Comunicación integrada** con agentes
- **Historial de solicitudes** por cliente
- **Eliminación de solicitudes** con confirmación y actualización visual
- **Estados permitidos** para eliminación (nuevo, en revisión, cerrado)
- **Paginación incremental** con carga de más propiedades
- **Fotos de propiedades** y agentes en las tarjetas
- **Información completa** de propiedades (precio, ubicación, características)

### 🔍 Búsqueda y Filtros Avanzados
- **Búsqueda por nombre completo** de agentes con espacios
- **Filtros por ciudad, sector y experiencia**
- **Carga infinita** en listados de agentes
- **Búsqueda en tiempo real** con debounce optimizado
- **Selección visual** de agentes con tarjetas informativas
- **Búsqueda combinada** en nombre, apellido, ciudad y sector
- **Resultados paginados** con navegación numerada

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
- **Efectos hover** mejorados en botones
- **Gradientes dinámicos** con efectos visuales
- **Botones unificados** con estilos consistentes

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
- **Corrección de errores 500** en rutas de chat
- **Validación de parámetros** en URLs

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
- **Búsqueda por nombre completo** de agentes con espacios
- **Filtros combinados** por múltiples criterios
- **Carga infinita** para mejor rendimiento
- **Búsqueda en tiempo real** con debounce optimizado
- **Resultados paginados** optimizados
- **Búsqueda SQL mejorada** con concatenaciones
- **Soporte para nombres** con espacios y caracteres especiales

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
- **Notificaciones de chat** mejoradas
- **Sistema de tracking** de contactos

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

## 🆕 Mejoras Recientes (v2.4.0)

### Sistema de Chat Unificado y Mejorado
- **✅ Problema resuelto**: Inconsistencia en botones "Contactar" entre páginas
- **🔗 Enlaces unificados**: Todos los botones usan `/chat/simple?agent={id}`
- **🎯 Preselección automática**: Agente seleccionado automáticamente al cargar
- **🔄 Selección inteligente**: Conversaciones existentes se seleccionan automáticamente
- **➕ Creación automática**: Nuevas conversaciones se crean y seleccionan automáticamente
- **📱 Experiencia consistente**: Misma funcionalidad en todas las páginas
- **🔧 Router mejorado**: Priorización de rutas exactas sobre rutas con parámetros
- **📊 Logs detallados**: Sistema de debugging completo para rastrear el flujo

### Correcciones Técnicas Implementadas
- **🔧 Router optimizado**: Método `findRouteWithParams` mejorado para priorizar rutas exactas
- **📁 Archivos corregidos**: `app/views/agente/listar_agentes.php` con enlaces correctos
- **🎨 JavaScript mejorado**: Sistema de eventos robusto para preselección de agentes
- **⚡ Rendimiento optimizado**: Carga asíncrona de conversaciones con eventos personalizados
- **🛡️ Manejo de errores**: Validación mejorada y logs detallados para debugging

### Características del Sistema de Chat
- **💬 Chat Simple**: Interfaz moderna con preselección automática
- **🎯 Preselección de Agente**: Detección automática del agente desde URL
- **🔄 Carga Inteligente**: Espera a que las conversaciones se carguen antes de preseleccionar
- **📱 Responsive**: Funciona perfectamente en dispositivos móviles
- **🔍 Búsqueda Integrada**: Búsqueda de usuarios para nuevas conversaciones
- **📊 Estadísticas**: Contador de mensajes no leídos y estado online

## 🐛 Problemas Conocidos y Soluciones

### ✅ Chat Unificado Resuelto
**Problema**: Inconsistencia en botones "Contactar" y "Chat" entre páginas
**Solución**: Todos los botones ahora usan `/chat/simple?agent={id}` con preselección automática
**Estado**: ✅ COMPLETAMENTE RESUELTO

### Caché del Navegador
**Problema**: Cambios no se reflejan inmediatamente
**Solución**: Usar Ctrl+F5 para limpiar caché o agregar parámetro `&v={timestamp}`
**Estado**: ✅ SOLUCIONADO

### Fotos de Perfil No Se Muestran
**Problema**: Rutas inconsistentes en diferentes controladores
**Solución**: Todas las fotos ahora usan `/uploads/profiles/` unificadamente
**Estado**: ✅ SOLUCIONADO

### Búsqueda de Agentes con Espacios
**Problema**: Búsqueda no funciona con nombres como "Angel Leger"
**Solución**: Búsqueda SQL mejorada con concatenaciones y división de palabras
**Estado**: ✅ SOLUCIONADO

### Redirección Incorrecta al Chat
**Problema**: Botones "Contactar" redirigían a `/chat/{id}` en lugar de `/chat/simple?agent={id}`
**Solución**: Corregidos todos los enlaces para usar la ruta correcta del chat simple
**Estado**: ✅ COMPLETAMENTE RESUELTO

## 📞 Soporte

Para soporte técnico o consultas:

- 📧 Email: soporte@propeasy.com
- 📱 WhatsApp: +1 234 567 8900
- 🌐 Sitio web: https://propeasy.com/soporte
- 📖 Documentación: https://docs.propeasy.com
- 🐛 Issues: https://github.com/propeasy/propeasy/issues

## 🔄 Changelog

### v2.4.0 (2024-12-23)
- 🎯 **Sistema de chat completamente unificado**
- 🔗 **Corrección de enlaces en todas las páginas de agentes**
- 🔧 **Router optimizado con priorización de rutas exactas**
- 📊 **Sistema de logs detallados para debugging**
- ⚡ **Carga asíncrona mejorada con eventos personalizados**
- 🛡️ **Manejo robusto de errores y validaciones**
- 📱 **Experiencia consistente en todas las páginas**
- 🔄 **Preselección automática de conversaciones existentes**
- ➕ **Creación automática de nuevas conversaciones**

### v2.3.0 (2024-12-23)
- 💬 Sistema de chat unificado en todas las páginas
- 🔗 Botones "Contactar" y "Chat" con lógica consistente
- 🎯 Preselección automática de agentes en chat
- 🔄 Selección automática de conversaciones existentes
- 🗑️ Lógica unificada de eliminación de solicitudes
- 🔒 Estados permitidos para eliminación (nuevo, en revisión, cerrado)
- 📱 Experiencia consistente en todas las páginas
- ⚡ Optimizaciones de rendimiento en chat

### v2.2.0 (2024-12-23)
- 🗑️ Sistema de eliminación de solicitudes de compra
- 📊 Dashboard del cliente mejorado con estadísticas
- 🔄 Paginación incremental en propiedades solicitadas
- 🖼️ Fotos de propiedades y agentes en tarjetas
- 📋 Estados de solicitudes (nuevo, en revisión, reunión agendada, cerrado)
- ✅ Confirmación de eliminación con actualización visual
- 🔒 Eliminación física segura de solicitudes
- 🎯 Lógica de estados permitidos para eliminación

### v2.1.0 (2024-12-23)
- 🔧 Corrección de errores 500 en rutas de chat
- 🎨 Efectos hover mejorados en botones
- 🔗 Unificación de botones "Contactar" en todas las páginas
- 🖼️ Corrección de rutas de fotos de perfil
- 🔍 Búsqueda SQL mejorada para nombres con espacios
- ⚡ Optimizaciones de rendimiento y caché

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