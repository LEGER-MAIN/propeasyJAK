# PropEasy - Sistema Web de Venta de Bienes Raíces

## Descripción del Proyecto

PropEasy es una plataforma web integral diseñada para optimizar la gestión inmobiliaria mediante la digitalización de procesos y centralización de información. El sistema facilita la visualización masiva de propiedades para los clientes, sirve como intermediario entre clientes y agentes inmobiliarios, y permite a la empresa registrar y monitorear en detalle todas las transacciones y actividades comerciales.

## Características Principales

### 🏠 Gestión de Propiedades
- **Publicación estructurada**: Formularios con campos obligatorios para evitar registros incompletos
- **Validación por tokens**: Sistema de tokenización para certificar la veracidad antes de publicación
- **Búsqueda avanzada**: Filtros por precio, ubicación, habitaciones, baños y más
- **Control de estados**: En revisión, activa, vendida, rechazada
- **Gestión de imágenes**: Múltiples imágenes por propiedad con imagen principal

### 👥 Sistema de Roles y Usuarios
- **Tres roles principales**: Cliente, Agente Inmobiliario, Administrador
- **Autenticación segura**: Login/registro con confirmación por email
- **Recuperación de contraseñas**: Sistema seguro de recuperación
- **Control de acceso**: Permisos basados en roles (RBAC)

### 💬 Chat en Tiempo Real
- **Chat interno**: Comunicación directa entre cliente y agente por propiedad
- **Chat directo**: Conversaciones sin necesidad de solicitudes de compra
- **WebSocket**: Conexión en tiempo real con Ratchet PHP
- **Historial persistente**: Todos los mensajes quedan grabados en la base de datos
- **Notificaciones**: Alertas de nuevos mensajes
- **Filtrado por roles**: Clientes solo ven agentes, agentes ven clientes y otros agentes

### 📅 Sistema de Citas y Agenda
- **Propuesta de citas**: Desde chat o panel del agente
- **Estados de citas**: Propuesta, aceptada, rechazada, completada, cancelada
- **Agenda integrada**: Visualización de citas para agentes y clientes
- **Recordatorios automáticos**: Envío de notificaciones por email

### 📋 Solicitudes de Compra
- **Formularios de interés**: Registro automático de solicitudes
- **Seguimiento de estado**: Nuevo, en revisión, reunión agendada, cerrado
- **Notificaciones automáticas**: Alerta al agente responsable

### 📊 Dashboards y Reportes
- **Dashboard Administrativo**: Estadísticas globales del sistema
- **Dashboard de Agente**: Métricas individuales y seguimiento
- **Perfil Público del Agente**: Transparencia y confianza para clientes
- **Reportes detallados**: Ventas, usuarios, propiedades, citas

### 🚨 Sistema de Reportes de Irregularidades
- **Formularios de queja**: Reportes con adjuntos opcionales
- **Seguimiento de estado**: Pendiente, atendido, descartado
- **Gestión administrativa**: Panel para revisión y respuesta

### ⭐ Sistema de Favoritos
- **Guardado de propiedades**: Lista personalizada de favoritos
- **Seguimiento**: Notificaciones de cambios en propiedades favoritas

### 🔍 Búsqueda Avanzada
- **Búsqueda de agentes**: Filtros por ciudad, experiencia, idioma
- **Búsqueda de clientes**: Gestión de base de datos de clientes
- **Filtros múltiples**: Combinación de criterios de búsqueda

## Tecnologías Utilizadas

### Backend
- **PHP 8.2**: Lógica de negocio y procesamiento
- **MySQL 8.0**: Base de datos relacional
- **Apache**: Servidor web
- **PHPMailer**: Envío de emails
- **Ratchet PHP**: WebSocket para chat en tiempo real

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos y diseño responsivo
- **JavaScript**: Interactividad y validaciones
- **Bootstrap 5**: Framework CSS para diseño responsivo
- **Chart.js**: Gráficos y visualizaciones
- **WebSocket API**: Comunicación en tiempo real

### Características Técnicas
- **Arquitectura MVC**: Separación clara de responsabilidades
- **Base de datos normalizada**: Optimizada para consultas eficientes
- **Sistema de rutas**: Enrutamiento personalizado
- **Validación de datos**: Sanitización y validación robusta
- **Manejo de errores**: Sistema completo de logging y errores
- **Seguridad**: Hashing de contraseñas, tokens CSRF, validación de sesiones
- **Chat en tiempo real**: WebSocket con autenticación y filtrado por roles

## Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── AdminController.php
│   │   ├── ApiController.php
│   │   ├── AppointmentController.php
│   │   ├── AuthController.php
│   │   ├── ChatController.php
│   │   ├── ClienteController.php
│   │   ├── DashboardController.php
│   │   ├── FavoriteController.php
│   │   ├── HomeController.php
│   │   ├── PropertyController.php
│   │   ├── ReporteController.php
│   │   ├── SearchController.php
│   │   ├── SolicitudController.php
│   │   └── ...
│   ├── models/              # Modelos de datos
│   │   ├── Appointment.php
│   │   ├── Chat.php
│   │   ├── Favorite.php
│   │   ├── Property.php
│   │   ├── ReporteIrregularidad.php
│   │   ├── SolicitudCompra.php
│   │   ├── User.php
│   │   └── ...
│   ├── views/               # Vistas y templates
│   │   ├── admin/           # Panel administrativo
│   │   ├── agente/          # Panel de agente
│   │   ├── cliente/         # Panel de cliente
│   │   ├── auth/            # Autenticación
│   │   ├── properties/      # Gestión de propiedades
│   │   ├── chat/            # Sistema de chat
│   │   ├── appointments/    # Sistema de citas
│   │   └── ...
│   └── core/                # Núcleo del sistema
│       ├── Database.php     # Conexión a base de datos
│       └── Router.php       # Sistema de rutas
├── config/                  # Configuración
│   └── config.php          # Configuración general
├── database/               # Base de datos
│   └── scheme.sql          # Esquema completo
├── public/                 # Archivos públicos
│   ├── css/               # Estilos
│   ├── js/                # JavaScript
│   ├── uploads/           # Archivos subidos
│   └── index.php          # Punto de entrada
├── logs/                  # Logs del sistema
├── vendor/                # Dependencias (Composer)
└── docs/                  # Documentación
```

## Instalación y Configuración

### Requisitos del Sistema
- **Laragon** (recomendado) o XAMPP/WAMP
- PHP 7.4 o superior
- MySQL 8.0 o superior
- Composer
- Extensión PHP: mysqli, pdo_mysql, gd, mbstring

### Instalación Rápida con Laragon

1. **Clonar el repositorio en Laragon**
   ```bash
   # Navegar a la carpeta www de Laragon
   cd C:\laragon\www
   
   # Clonar el proyecto
   git clone https://github.com/tu-usuario/propeasy.git
   ```

2. **Instalar dependencias**
   ```bash
   cd propeasy
   composer install
   ```

3. **Configurar base de datos**
   - Abrir HeidiSQL desde Laragon
   - Crear nueva base de datos llamada `propeasy_db`
   - Importar el archivo `database/scheme.sql`

4. **Configurar Laragon**
   - En Laragon, ir a Menú → Preferencias → Document Root
   - Cambiar a: `C:\laragon\www\propeasy\public`
   - Reiniciar Laragon

5. **Acceder al proyecto**
   - Abrir navegador y ir a: `http://propeasy.test` o `http://localhost`
   - El proyecto estará listo para usar

### Configuración Manual (Alternativa)

Si prefieres configuración manual:

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/propeasy.git
   cd propeasy
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar base de datos**
   - Crear base de datos MySQL llamada `propeasy_db`
   - Importar el esquema: `database/scheme.sql`

4. **Configurar servidor web**
   - Apuntar document root a la carpeta `public/`
   - Configurar URL rewriting (mod_rewrite)

5. **Permisos de archivos** (solo en Linux/Mac)
   ```bash
   chmod 755 public/uploads/
   chmod 755 logs/
   ```

6. **Iniciar servidor WebSocket (opcional)**
   ```bash
   php app/websocket_server.php
   ```

### Configuración Automática

El proyecto está configurado para funcionar inmediatamente después de la instalación:

- ✅ **Base de datos**: Configurada para Laragon (localhost, root, sin contraseña)
- ✅ **Configuraciones**: Todas las configuraciones están incluidas en el repositorio
- ✅ **Estructura de carpetas**: Las carpetas de uploads están configuradas
- ✅ **Dependencias**: Composer.json incluye todas las dependencias necesarias

**Nota**: No es necesario crear archivos de configuración adicionales. El proyecto está listo para usar desde el primer momento.

## Uso del Sistema

### Roles de Usuario

#### 👤 Cliente
- Registro y autenticación
- Búsqueda de propiedades
- Solicitudes de compra
- Chat con agentes (solo ve agentes)
- Gestión de favoritos
- Reportes de irregularidades
- Perfil personal

#### 👔 Agente Inmobiliario
- Dashboard personal
- Gestión de propiedades asignadas
- Validación de propiedades de clientes
- Chat con clientes (ve clientes y otros agentes)
- Agenda de citas
- Perfil público
- Estadísticas de ventas

#### 👑 Administrador
- Dashboard administrativo
- Gestión de usuarios
- Reportes globales
- Configuración del sistema
- Gestión de reportes de irregularidades
- Estadísticas del negocio

### Funcionalidades Principales

#### Gestión de Propiedades
1. **Publicación por Cliente**: Formulario con validación por token
2. **Validación por Agente**: Revisión y aprobación de propiedades
3. **Búsqueda Avanzada**: Filtros múltiples y paginación
4. **Gestión de Estados**: Control completo del ciclo de vida

#### Sistema de Chat en Tiempo Real
1. **Chat Integrado**: Comunicación directa cliente-agente
2. **WebSocket**: Conexión en tiempo real
3. **Filtrado por Roles**: Clientes solo ven agentes, agentes ven clientes y otros agentes
4. **Historial Completo**: Persistencia de conversaciones
5. **Notificaciones**: Alertas en tiempo real

#### Sistema de Citas
1. **Propuesta de Citas**: Desde chat o panel
2. **Gestión de Estados**: Seguimiento completo
3. **Recordatorios**: Notificaciones automáticas

## API REST

El sistema incluye una API REST completa para integraciones futuras:

### Endpoints Principales
- `GET /api/properties` - Lista de propiedades
- `GET /api/properties/{id}` - Detalle de propiedad
- `POST /api/requests` - Crear solicitud de compra
- `GET /api/stats` - Estadísticas del sistema
- `GET /api/agents` - Lista de agentes
- `GET /api/agents/{id}/profile` - Perfil de agente

### Autenticación API
- Headers CORS configurados
- Validación de tokens (futura implementación)
- Respuestas JSON estandarizadas

## Base de Datos

### Tablas Principales
- `usuarios` - Gestión de usuarios y roles
- `propiedades` - Catálogo de propiedades
- `solicitudes_compra` - Solicitudes de compra
- `citas` - Agenda de citas
- `mensajes_chat` - Sistema de chat
- `conversaciones_directas` - Chat directo
- `reportes_irregularidades` - Reportes de usuarios
- `favoritos_propiedades` - Sistema de favoritos
- `calificaciones_agentes` - Sistema de calificaciones

### Características de la BD
- **Normalización**: Optimizada para consultas eficientes
- **Índices**: Optimización para búsquedas frecuentes
- **Relaciones**: Claves foráneas bien definidas
- **Triggers**: Automatización de procesos
- **Vistas**: Consultas complejas optimizadas

## Seguridad

### Medidas Implementadas
- **Hashing de contraseñas**: password_hash() con bcrypt
- **Validación de entrada**: Sanitización de datos
- **Tokens CSRF**: Protección contra ataques CSRF
- **Control de sesiones**: Gestión segura de sesiones
- **Validación de roles**: Acceso basado en permisos
- **Prepared Statements**: Prevención de SQL Injection
- **Validación de archivos**: Control de uploads
- **Filtrado por roles**: Clientes solo ven agentes en chat

### Configuraciones de Seguridad
- Headers de seguridad configurados
- Configuración de cookies seguras
- Manejo de errores sin exposición de información sensible
- Logging de actividades del sistema

## Mantenimiento y Soporte

### Logs del Sistema
- **Error logs**: Errores de aplicación
- **Activity logs**: Actividades de usuarios
- **Access logs**: Accesos al sistema
- **WebSocket logs**: Conexiones y mensajes del chat

### Backup y Recuperación
- **Backup automático**: Scripts de respaldo
- **Recuperación**: Procedimientos de restauración
- **Monitoreo**: Alertas de estado del sistema

### Actualizaciones
- **Control de versiones**: Git para seguimiento de cambios
- **Migraciones**: Scripts de actualización de BD
- **Rollback**: Procedimientos de reversión

## Contribución

### Guías de Desarrollo
1. **Estándares de código**: PSR-12 para PHP
2. **Documentación**: Comentarios en código
3. **Testing**: Pruebas unitarias (futura implementación)
4. **Code Review**: Revisión de código antes de merge

### Estructura de Commits
- `feat:` Nuevas funcionalidades
- `fix:` Correcciones de bugs
- `docs:` Documentación
- `style:` Cambios de estilo
- `refactor:` Refactorización de código

## Licencia

Este proyecto es desarrollado como proyecto final para el título de Técnico Superior en Desarrollo de Software del Instituto Técnico Superior Comunitario.

## Autores

- **Jefferson Miguel Angel Leger Lora** (2023-0218)
- **Kelvin Antonio Dominguez Cabrera** (2021-0357)
- **Alejandro Santos Estrella** (2023-0242)

### Asesor
- **Prof. Arismendy Polanco**

## Contacto

Para soporte técnico o consultas sobre el proyecto:
- Email: propeasycorp@gmail.com
- Documentación: Ver carpeta `docs/`

---

**PropEasy** - Transformando la gestión inmobiliaria a través de la tecnología. 