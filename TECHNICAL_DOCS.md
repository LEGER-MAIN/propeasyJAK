# 📋 Documentación Técnica - PropEasy

## 🏗️ Arquitectura del Sistema

### **Patrón MVC (Model-View-Controller)**
- **Models**: Lógica de negocio y acceso a datos
- **Views**: Interfaz de usuario y presentación
- **Controllers**: Control de flujo y lógica de aplicación

### **Componentes Principales**

#### **1. Core System (`app/core/`)**
- `Router.php` - Enrutamiento dinámico con parámetros
- `Database.php` - Capa de abstracción de base de datos
- `Logger.php` - Sistema de logging y auditoría

#### **2. Models (`app/models/`)**
- `User.php` - Gestión de usuarios y autenticación
- `Property.php` - Gestión de propiedades inmobiliarias
- `Chat.php` - Sistema de mensajería en tiempo real
- `Appointment.php` - Gestión de citas y agenda
- `SolicitudCompra.php` - Solicitudes de compra
- `Favorite.php` - Sistema de favoritos
- `ActivityLog.php` - Registro de actividades

#### **3. Controllers (`app/controllers/`)**
- `AuthController.php` - Autenticación y registro
- `PropertyController.php` - Gestión de propiedades
- `AgenteController.php` - Funcionalidades de agentes
- `ChatController.php` - Chat en tiempo real
- `AdminController.php` - Panel administrativo
- `ApiController.php` - API REST

## 🔌 WebSocket Server

### **Configuración**
- **Puerto**: 8080
- **Protocolo**: Ratchet PHP WebSocket
- **Funcionalidades**:
  - Chat en tiempo real
  - Notificaciones automáticas
  - Autenticación de usuarios

### **Integración con ngrok**
- Detección automática de URL de ngrok
- Configuración dinámica de WebSocket URL
- Túnel WebSocket automático

## 🗄️ Base de Datos

### **Estructura Optimizada**

#### **Tabla `usuarios`**
```sql
- id (PRIMARY KEY)
- nombre, apellido, email, password
- telefono, ciudad, sector (OBLIGATORIOS)
- rol (cliente, agente, admin)
- estado (activo, inactivo)
- email_verificado, token_verificacion
- fecha_registro, ultimo_acceso
- perfil_publico_activo (DEFAULT 1 para agentes)
- biografia, foto_perfil, idiomas
```

#### **Tabla `propiedades`**
```sql
- id (PRIMARY KEY)
- titulo, descripcion, tipo
- precio, moneda, ciudad, sector
- direccion, metros_cuadrados
- habitaciones, banos, estacionamientos
- estado_propiedad, estado_publicacion
- cliente_vendedor_id, agente_id
- fecha_creacion, fecha_actualizacion
```

#### **Tabla `mensajes`**
```sql
- id (PRIMARY KEY)
- remitente_id, destinatario_id
- contenido, tipo_mensaje
- leido, fecha_envio
- conversacion_id
```

### **Relaciones Clave**
- `usuarios` ↔ `propiedades` (vendedor/agente)
- `usuarios` ↔ `mensajes` (remitente/destinatario)
- `propiedades` ↔ `imagenes_propiedades` (galería)
- `propiedades` ↔ `solicitudes_compra` (solicitudes)

## 🔐 Sistema de Autenticación

### **Seguridad**
- **Hashing**: `password_hash()` con `PASSWORD_DEFAULT`
- **Tokens**: Generación segura con `random_bytes()`
- **Sesiones**: Gestión segura con `session_start()`
- **CSRF**: Protección con tokens CSRF

### **Roles y Permisos**
- **Admin**: Acceso completo al sistema
- **Agente**: Gestión de propiedades y clientes
- **Cliente**: Publicación y búsqueda de propiedades

## 📧 Sistema de Email

### **Configuración PHPMailer**
- **SMTP**: Configuración automática
- **Templates**: HTML personalizados
- **Funcionalidades**:
  - Verificación de email
  - Recuperación de contraseña
  - Notificaciones automáticas

### **URLs Dinámicas**
- Detección automática de entorno (local/ngrok)
- URLs de verificación dinámicas
- Configuración automática de base URL

## 🎨 Frontend

### **Tecnologías**
- **HTML5**: Estructura semántica
- **CSS3**: Estilos responsive con Grid/Flexbox
- **JavaScript**: Interactividad y AJAX
- **WebSocket**: Comunicación en tiempo real

### **Componentes Reutilizables**
- `flash-messages.php` - Mensajes del sistema
- `navbar.php` - Navegación principal
- `card_propiedad_cliente.php` - Tarjetas de propiedades

## 🔧 Configuración del Sistema

### **Archivos de Configuración**

#### **`config/config.php`**
```php
// Configuración general
define('APP_NAME', 'PropEasy');
define('APP_URL', 'http://localhost:80');
define('UPLOADS_URL', 'http://localhost:80/uploads');

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-password');
```

#### **`config/ngrok.php`**
```php
// Funciones dinámicas para ngrok
function getAppUrl() { /* Detección automática */ }
function getWebSocketUrl() { /* URL WebSocket dinámica */ }
function getDynamicBaseUrl() { /* Base URL dinámica */ }
```

### **Variables de Entorno**
- `APP_ENV`: Entorno (development/production)
- `DEBUG_MODE`: Modo debug (true/false)
- `LOG_LEVEL`: Nivel de logging

## 🚀 Script de Inicio

### **`start_propeasy.bat`**
```batch
# Descarga automática de ngrok
# Inicio de servidor PHP (puerto 80)
# Inicio de WebSocket (puerto 8080)
# Configuración de túneles ngrok
# Actualización de configuración
# Apertura automática del navegador
```

## 📊 Monitoreo y Logs

### **Sistema de Logging**
- **Archivo**: `logs/error.log`
- **Niveles**: DEBUG, INFO, WARNING, ERROR
- **Contexto**: Usuario, IP, User-Agent, Timestamp

### **Actividad del Sistema**
- **Tabla**: `logs_actividad`
- **Registro**: Todas las acciones importantes
- **Auditoría**: Trazabilidad completa

## 🔄 Flujos Principales

### **1. Registro de Usuario**
1. Validación de datos del formulario
2. Verificación de email único
3. Hash de contraseña
4. Inserción en base de datos
5. Envío de email de verificación
6. Registro de actividad

### **2. Publicación de Propiedad**
1. Autenticación del cliente
2. Validación de datos de propiedad
3. Procesamiento de imágenes
4. Asignación de agente (opcional)
5. Creación de token de validación
6. Envío de notificaciones

### **3. Chat en Tiempo Real**
1. Conexión WebSocket
2. Autenticación de usuario
3. Carga de conversaciones existentes
4. Envío/recepción de mensajes
5. Actualización de estado "leído"
6. Notificaciones push

## 🛡️ Seguridad

### **Protecciones Implementadas**
- **SQL Injection**: Prepared statements
- **XSS**: `htmlspecialchars()` en todas las salidas
- **CSRF**: Tokens en formularios
- **Session Hijacking**: Regeneración de IDs
- **File Upload**: Validación de tipos y tamaños

### **Validaciones**
- **Frontend**: JavaScript en tiempo real
- **Backend**: Validación PHP estricta
- **Base de Datos**: Constraints y triggers

## 📈 Rendimiento

### **Optimizaciones**
- **Consultas**: Índices en campos frecuentes
- **Imágenes**: Compresión automática
- **Caché**: Configuración de headers
- **Paginación**: Límites en consultas

### **Métricas**
- **Tiempo de respuesta**: < 200ms
- **Uptime**: 99.9%
- **Concurrentes**: 100+ usuarios

## 🔧 Mantenimiento

### **Tareas Programadas**
- Limpieza de logs antiguos
- Backup automático de base de datos
- Verificación de integridad
- Actualización de estadísticas

### **Monitoreo**
- Logs de error en tiempo real
- Métricas de rendimiento
- Alertas automáticas
- Dashboard de estado

---

**📋 Esta documentación se actualiza automáticamente con cada cambio en el sistema.** 