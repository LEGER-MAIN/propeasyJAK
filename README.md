# PropEasy - Sistema Web de Venta de Bienes Raíces

![PropEasy Logo](https://img.shields.io/badge/PropEasy-Real%20Estate%20Platform-blue)
![PHP Version](https://img.shields.io/badge/PHP-8.0+-green)
![MySQL Version](https://img.shields.io/badge/MySQL-8.0+-orange)
![License](https://img.shields.io/badge/License-MIT-yellow)
![Version](https://img.shields.io/badge/Version-v2.7.0-brightgreen)

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

### 📋 Sistema de Reportes
- **Reportes de irregularidades** con formulario moderno
- **Carga de archivos adjuntos** con validación
- **Proceso de revisión** profesional con seguimiento
- **Estados de reporte** (pendiente, atendido, descartado)
- **Información confidencial** garantizada
- **Diseño mejorado** con colores profesionales

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

## 🚀 Instalación

### Requisitos del Sistema
- **PHP**: 8.0 o superior
- **MySQL**: 8.0 o superior
- **Servidor Web**: Apache/Nginx
- **Extensiones PHP**: PDO, MySQL, GD, JSON, mbstring

### Pasos de Instalación

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
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas y templates
│   ├── core/           # Núcleo del sistema
│   ├── helpers/        # Funciones auxiliares
│   └── websocket_server.php
├── config/             # Archivos de configuración
├── database/           # Esquemas de base de datos
├── logs/               # Archivos de log
├── public/             # Archivos públicos (document root)
│   ├── css/           # Estilos CSS
│   ├── js/            # JavaScript
│   └── uploads/       # Archivos subidos
├── scripts/            # Scripts de mantenimiento
└── vendor/             # Dependencias de Composer
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