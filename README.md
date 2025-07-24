# PropEasy - Sistema Web de Venta de Bienes Raíces

![PropEasy Logo](https://img.shields.io/badge/PropEasy-Real%20Estate%20Platform-blue)
![PHP Version](https://img.shields.io/badge/PHP-8.0+-green)
![MySQL Version](https://img.shields.io/badge/MySQL-8.0+-orange)
![License](https://img.shields.io/badge/License-MIT-yellow)
![Version](https://img.shields.io/badge/Version-v2.9.0-brightgreen)

## 📋 Descripción

PropEasy es una plataforma web completa para la gestión y venta de bienes raíces. Permite a agentes inmobiliarios publicar propiedades, gestionar clientes, y facilitar la comunicación entre compradores y vendedores a través de un sistema de chat integrado, herramientas avanzadas de gestión y un sistema inteligente de alertas del administrador.

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
- **Edición y eliminación** de propiedades por agentes
- **Cambio de estados** con comentarios y validación
- **Enfoque exclusivo en venta** - Sin referencias a alquiler
- **Datos completos de propiedades** en solicitudes (tipo, habitaciones, baños, área)

### 👥 Gestión de Usuarios
- **Múltiples roles**: Clientes, Agentes, Administradores
- **Perfiles públicos** para agentes con estadísticas
- **Sistema de autenticación** seguro con verificación de email
- **Recuperación de contraseñas** por email
- **Fotos de perfil** con gestión unificada y vista previa
- **Gestión de sesiones** mejorada
- **Corrección de rutas** de fotos de perfil
- **Variables de sesión** optimizadas
- **Dashboard personalizado** para clientes con estadísticas
- **Gestión de propiedades solicitadas** con eliminación segura
- **Información profesional** con especialidades y experiencia
- **Carga de fotos** con validación y vista previa en tiempo real

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
- **Interfaz limpia** sin botones de eliminación de conversaciones

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
- **Acceso desde dropdown** del usuario para mejor estética
- **Navegación móvil** optimizada en sección "Cuenta"
- **Exclusivo para clientes** - Agentes no pueden usar favoritos

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
- **Datos detallados** de propiedades en solicitudes (tipo, habitaciones, baños, área)

### 🏪 Mis Ventas (Propiedades Enviadas)
- **Gestión de propiedades** enviadas por clientes para publicación
- **Estados de publicación** (activa, en revisión, rechazada, vendida)
- **Estadísticas detalladas** (total, activas, en revisión)
- **Información del agente** asignado a cada propiedad
- **Layout unificado** con el resto de la aplicación
- **Vista de tarjetas** con imágenes y detalles completos
- **Acceso desde navbar** principal para clientes
- **Corrección de consultas** SQL para imágenes de propiedades

### 🔔 Sistema de Alertas del Administrador
- **Alertas inteligentes** del sistema en tiempo real
- **Eliminación permanente** de alertas por el administrador
- **Persistencia de alertas eliminadas** en base de datos
- **No reaparición** de alertas eliminadas al recargar la página
- **Restauración automática** después de 24 horas
- **Animaciones suaves** al eliminar alertas
- **Manejo robusto de errores** con fallback graceful
- **Tipos de alertas**: Reportes nuevos, propiedades pendientes, usuarios suspendidos, propiedades rechazadas
- **Priorización de alertas** por importancia
- **Interfaz intuitiva** con botones de cierre
- **Sistema opcional** que no afecta el funcionamiento general

### 🔍 Sistema de Búsqueda Avanzado
- **Búsqueda de usuarios** por nombre, email, username
- **Filtros por rol** (Administradores, Agentes, Clientes)
- **Filtros por estado** (Activos, Suspendidos)
- **Búsqueda en tiempo real** con auto-submit
- **Búsqueda manual** con botón y tecla Enter
- **Filtros combinables** para búsquedas precisas
- **Resaltado de términos** de búsqueda en resultados
- **Indicadores visuales** de filtros activos
- **Contador de resultados** dinámico
- **Exportación inteligente** que respeta filtros aplicados
- **Limpieza de filtros** con un solo clic
- **Interfaz responsive** optimizada para todos los dispositivos

### ℹ️ Página "Acerca de"
- **Información corporativa** completa de PropEasy
- **Sección Hero** con descripción principal
- **Misión y Visión** de la empresa
- **Valores corporativos** destacados
- **Historia de la empresa** con timeline
- **Equipo de trabajo** con perfiles
- **Información de contacto** profesional
- **Diseño responsive** y moderno
- **Integración completa** con el layout principal

### 📋 Sistema de Reportes
- **Reportes de irregularidades** con formulario moderno
- **Carga de archivos adjuntos** con validación
- **Proceso de revisión** profesional con seguimiento

### 🎯 Panel de Administración
- **Dashboard completo** con estadísticas en tiempo real
- **Gestión de usuarios** con búsqueda y filtros avanzados
- **Sistema de búsqueda inteligente** por nombre, email, rol y estado
- **Filtros combinables** para encontrar usuarios específicos rápidamente
- **Búsqueda en tiempo real** con auto-completado
- **Resaltado de términos** de búsqueda en resultados
- **Exportación inteligente** que respeta filtros aplicados
- **Indicadores visuales** de filtros activos
- **Contador de resultados** dinámico
- **Gestión de propiedades** con validación y aprobación
- **Sistema de alertas inteligente** con eliminación permanente
- **Actividades recientes** con paginación y filtros
- **Todas las actividades** integradas en el sidebar del admin
- **Estadísticas detalladas** de usuarios, propiedades y solicitudes
- **Gráficos interactivos** de tendencias del sistema
- **Gestión de reportes** con resolución y seguimiento
- **Logs del sistema** con filtros y búsqueda
- **Backup y restore** de la base de datos
- **Configuración del sistema** centralizada
- **Diseño compacto y profesional** para todas las secciones
- **Navegación intuitiva** con sidebar integrado
- **Filtros avanzados** para búsqueda de actividades
- **Paginación optimizada** para grandes volúmenes de datos
- **Estados de reporte** (pendiente, atendido, descartado)
- **Información confidencial** garantizada
- **Diseño mejorado** con colores profesionales
- **Persistencia de alertas eliminadas** en base de datos
- **Restauración automática** de alertas después de 24 horas
- **Manejo robusto de errores** en sistema de alertas

### 🎨 Interfaz y Diseño
- **Diseño responsive** optimizado para móviles y desktop
- **Paleta de colores** profesional (azul marino, verde esmeralda, dorado)
- **Componentes reutilizables** con estilos consistentes
- **Animaciones suaves** y transiciones elegantes
- **Iconografía Font Awesome** para mejor UX
- **Footer actualizado** con información de contacto profesional
- **Página "Acerca de"** completa con misión, visión y equipo
- **Navegación intuitiva** con breadcrumbs y enlaces claros

### 🔧 Funcionalidades Técnicas
- **Arquitectura MVC** bien estructurada
- **Sistema de rutas** flexible y escalable
- **Base de datos optimizada** con índices apropiados
- **Validación de datos** en frontend y backend
- **Sistema de logs** para debugging y monitoreo
- **Backup automático** de base de datos
- **Configuración centralizada** y fácil de mantener
- **Código limpio** sin archivos de debug o pruebas
- **Sistema de alertas inteligente** con persistencia en base de datos
- **Manejo robusto de errores** con fallback graceful
- **Gestión de estado** de alertas eliminadas
- **Sistema de búsqueda optimizado** con consultas SQL eficientes
- **Búsqueda en tiempo real** con JavaScript y AJAX
- **Filtros dinámicos** con combinación de criterios
- **Exportación de datos** con respeto a filtros aplicados

## 🚀 Instalación

### ⚡ Instalación Automática (Recomendada)

Para una instalación rápida y automática en Laragon:

1. **Copiar el proyecto** a tu directorio de Laragon
2. **Ejecutar el instalador**:
   ```bash
   cd C:\laragon\www\propeasy
   php install_propeasy.php
   ```

El script automáticamente:
- ✅ Verifica requisitos del sistema
- ✅ Crea estructura de directorios
- ✅ Configura la base de datos
- ✅ Instala dependencias
- ✅ Verifica la instalación

**Ver [INSTALACION_RAPIDA.md](INSTALACION_RAPIDA.md) para instrucciones detalladas.**

### 🔧 Instalación Manual

#### Requisitos del Sistema
- **PHP**: 8.0 o superior
- **MySQL**: 8.0 o superior
- **Servidor Web**: Apache/Nginx
- **Extensiones PHP**: PDO, MySQL, GD, JSON, mbstring

#### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/propeasy.git
   cd propeasy
   ```

2. **Configurar la base de datos**
   - Crear una base de datos MySQL
   - Importar el archivo `database/scheme.sql`
   - Copiar `config/database.example.php` a `config/database.php`
   - Configurar las credenciales de la base de datos

3. **Configurar el servidor web**
   - Configurar el document root en la carpeta `public/`
   - Habilitar mod_rewrite para Apache
   - Configurar permisos de escritura en `logs/` y `public/uploads/`

4. **Instalar dependencias**
   ```bash
   composer install
   ```

5. **Configurar variables de entorno**
   - Editar `config/config.php` según tus necesidades
   - Configurar URLs y configuraciones de email

## 📁 Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/         # Modelos de datos (incluye AlertManager)
│   ├── views/          # Vistas y templates
│   ├── core/           # Núcleo del sistema
│   ├── helpers/        # Funciones auxiliares
│   └── websocket_server.php
├── config/             # Archivos de configuración
├── database/           # Esquemas de base de datos (incluye alertas_eliminadas)
├── logs/               # Archivos de log
├── public/             # Archivos públicos (document root)
│   ├── css/           # Estilos CSS
│   ├── js/            # JavaScript
│   └── uploads/       # Archivos subidos
├── scripts/            # Scripts de mantenimiento
├── vendor/             # Dependencias de Composer
├── install_propeasy.php # Instalador automático
├── INSTALACION_RAPIDA.md # Guía de instalación rápida
└── composer.json       # Configuración de dependencias
```

## 🔧 Configuración

### Configuración de Base de Datos
Editar `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### Configuración de Email
Configurar en `config/config.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_contraseña_app');
```

### Configuración de WebSocket
Para el chat en tiempo real:
```bash
php app/websocket_server.php
```

## 👥 Roles de Usuario

### Cliente
- Ver propiedades disponibles
- Enviar solicitudes de compra
- Gestionar favoritos
- Agendar citas
- Comunicarse con agentes via chat
- Enviar propiedades para publicación

### Agente
- Publicar y gestionar propiedades
- Recibir y gestionar solicitudes
- Comunicarse con clientes
- Gestionar citas
- Ver estadísticas de ventas

### Administrador
- Gestión completa del sistema
- Aprobar/rechazar propiedades
- Gestionar usuarios y roles
- Ver reportes y estadísticas
- Configuración del sistema

## 🛠️ Mantenimiento

### Scripts Disponibles
- `install_propeasy.php` - **Instalador automático** (principal)
- `scripts/cleanup_project.php` - Limpieza del proyecto
- `scripts/seed_activity_logs.php` - Generar logs de actividad
- `scripts/send_appointment_reminders.php` - Recordatorios de citas

### Backup de Base de Datos
```bash
mysqldump -u usuario -p propeasy_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 🔒 Seguridad

- **Validación de datos** en frontend y backend
- **Protección CSRF** en formularios
- **Sanitización de inputs** para prevenir XSS
- **Contraseñas hasheadas** con bcrypt
- **Sesiones seguras** con regeneración de ID
- **Control de acceso** basado en roles

## 📞 Contacto

- **Email**: propeasycorp@gmail.com
- **Teléfono**: +1 809 359 5322
- **Dirección**: Santo Domingo, República Dominicana
- **Horarios**: Lun - Vie: 8:00 AM - 6:00 PM

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Changelog

### v2.9.0 (2025-01-24)
- ✅ **Sistema de búsqueda avanzado** para gestión de usuarios
- ✅ **Búsqueda en tiempo real** con auto-submit y filtros combinables
- ✅ **Filtros por rol y estado** para encontrar usuarios específicos
- ✅ **Resaltado de términos** de búsqueda en resultados
- ✅ **Indicadores visuales** de filtros activos y contador de resultados
- ✅ **Exportación inteligente** que respeta filtros aplicados
- ✅ **Interfaz responsive** optimizada para todos los dispositivos
- ✅ **Limpieza de filtros** con un solo clic
- ✅ **Nuevo método** `searchUsersForAdmin()` en modelo User
- ✅ **Controlador actualizado** para manejar parámetros de búsqueda
- ✅ **Vista mejorada** con formulario funcional y JavaScript optimizado
- ✅ Sistema de alertas inteligente con eliminación permanente
- ✅ Persistencia de alertas eliminadas en base de datos
- ✅ No reaparición de alertas eliminadas al recargar la página
- ✅ Restauración automática de alertas después de 24 horas
- ✅ Animaciones suaves al eliminar alertas del dashboard
- ✅ Manejo robusto de errores con fallback graceful
- ✅ Tipos de alertas: Reportes nuevos, propiedades pendientes, usuarios suspendidos, propiedades rechazadas
- ✅ Priorización de alertas por importancia
- ✅ Interfaz intuitiva con botones de cierre
- ✅ Sistema opcional que no afecta el funcionamiento general
- ✅ Corrección del error 500 en dashboard del admin
- ✅ Modelo AlertManager con métodos completos de gestión
- ✅ Tabla alertas_eliminadas en base de datos
- ✅ JavaScript mejorado para manejo de alertas
- ✅ **Script de instalación automática** para Laragon
- ✅ **Guía de instalación rápida** con instrucciones detalladas
- ✅ **Configuración automática** de base de datos y dependencias
- ✅ **Verificación completa** de requisitos del sistema
- ✅ README.md actualizado con nuevas funcionalidades

### v2.8.0 (2025-01-24)
- ✅ Panel de administración completamente renovado
- ✅ Dashboard con estadísticas en tiempo real y alertas dinámicas
- ✅ Sección "Todas las Actividades" integrada en sidebar del admin
- ✅ Diseño compacto y profesional para todas las secciones admin
- ✅ Sistema de alertas funcional con prioridades y ordenamiento
- ✅ Eliminación del índice "Total Ventas" y reemplazo con "Propiedades Pendientes"
- ✅ Filtros avanzados y paginación optimizada para actividades
- ✅ Navegación intuitiva con sidebar integrado
- ✅ Limpieza completa de console.log y archivos de debug
- ✅ Página "Acerca de" con información corporativa completa
- ✅ Footer actualizado con información de contacto profesional
- ✅ README.md actualizado con todas las nuevas características

### v2.7.0 (2025-01-24)
- ✅ Página "Acerca de" creada y enlazada
- ✅ Número de teléfono formateado profesionalmente (+1 809 359 5322)
- ✅ Datos completos de propiedades en solicitudes
- ✅ Eliminación de archivos de debug y console.log
- ✅ Interfaz de chat limpia sin botones de eliminación
- ✅ README.md actualizado con información completa

### v2.6.0 (2025-01-23)
- ✅ Sistema de chat mejorado
- ✅ Gestión de solicitudes optimizada
- ✅ Interfaz responsive mejorada
- ✅ Sistema de reportes implementado

### v2.5.0 (2025-01-22)
- ✅ Sistema de citas implementado
- ✅ Dashboard de agentes mejorado
- ✅ Sistema de favoritos optimizado

---

**PropEasy** - Conectando sueños inmobiliarios con realidades desde República Dominicana 🇩🇴 