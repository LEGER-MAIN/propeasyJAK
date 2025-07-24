# PropEasy - Sistema Web de Venta de Bienes Raíces

## 📋 Descripción General

PropEasy es un sistema web completo para la gestión y venta de bienes raíces, diseñado para conectar propietarios, agentes inmobiliarios y compradores en una plataforma moderna y eficiente.

## ✨ Características Principales

### 🏠 Gestión de Propiedades
- **Registro de Propiedades**: Formularios completos con validación
- **Galería de Imágenes**: Múltiples fotos por propiedad
- **Estados de Propiedades**: En revisión, activa, vendida, rechazada
- **Filtros Avanzados**: Por precio, ubicación, tipo, características
- **Sistema de Favoritos**: Para usuarios registrados
- **Búsqueda Inteligente**: Con autocompletado y sugerencias

### 👥 Gestión de Usuarios
- **Múltiples Roles**: Cliente, Agente, Administrador
- **Perfiles Completos**: Información personal y profesional
- **Sistema de Autenticación**: Seguro con validación
- **Gestión de Permisos**: Control de acceso por rol
- **Panel de Administración**: Gestión completa de usuarios

### 🤝 Sistema de Solicitudes
- **Solicitudes de Compra**: Formularios detallados
- **Estados de Solicitudes**: Nuevo, en revisión, reunión agendada, cerrado
- **Notificaciones**: Email automáticas
- **Seguimiento**: Historial completo de solicitudes
- **Gestión por Agentes**: Asignación y seguimiento

### 📅 Sistema de Citas
- **Agendamiento**: Calendario interactivo
- **Estados de Citas**: Propuesta, aceptada, rechazada, realizada
- **Notificaciones**: Confirmaciones automáticas
- **Gestión de Horarios**: Disponibilidad de agentes
- **Recordatorios**: Email y SMS

### 📊 Panel de Administración
- **Dashboard Completo**: Estadísticas en tiempo real
- **Gestión de Usuarios**: Bloqueo, cambio de roles, exportación
- **Gestión de Propiedades**: Aprobación, rechazo, eliminación
- **Gestión de Reportes**: Procesamiento de irregularidades
- **Sistema de Logs**: Monitoreo completo del sistema
- **Backup & Restore**: Respaldo y restauración
- **Configuración**: Ajustes del sistema

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.0+**: Lenguaje principal
- **MySQL 8.0+**: Base de datos
- **MVC Architecture**: Patrón de diseño
- **Composer**: Gestión de dependencias
- **PHPMailer**: Envío de emails

### Frontend
- **HTML5 & CSS3**: Estructura y estilos
- **JavaScript (ES6+)**: Interactividad
- **Bootstrap 5**: Framework CSS
- **jQuery**: Manipulación DOM
- **DataTables**: Tablas interactivas
- **Chart.js**: Gráficos y estadísticas

### Herramientas de Desarrollo
- **Git**: Control de versiones
- **Composer**: Gestión de dependencias
- **Laragon**: Entorno de desarrollo local

## 📁 Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas y templates
│   ├── core/           # Clases principales
│   └── helpers/        # Funciones auxiliares
├── config/             # Configuración del sistema
├── database/           # Scripts de base de datos
├── logs/               # Archivos de logs
├── public/             # Archivos públicos
├── scripts/            # Scripts de utilidad
├── sessions/           # Archivos de sesión
├── vendor/             # Dependencias de Composer
├── .htaccess           # Configuración Apache
├── composer.json       # Dependencias PHP
└── README.md           # Documentación
```

## 🚀 Instalación

### Requisitos Previos
- PHP 8.0 o superior
- MySQL 8.0 o superior
- Apache/Nginx
- Composer
- Extensión PHP: mysqli, mbstring, json, session

### Instalación Rápida

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
   - Crear base de datos MySQL
   - Importar `database/propeasy.sql`
   - Configurar `config/config.php`

4. **Configurar servidor web**
   - Apuntar document root a `/public`
   - Configurar URL rewriting

5. **Configurar permisos**
   ```bash
   chmod 755 logs/
   chmod 755 sessions/
   chmod 755 public/uploads/
   ```

### Instalación Automática
Ejecutar el script de instalación:
```bash
php install_propeasy.php
```

## ⚙️ Configuración

### Configuración de Base de Datos
Editar `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
```

### Configuración de Email
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_password');
```

### Configuración de Roles
- **ROLE_CLIENTE**: Usuarios que buscan propiedades
- **ROLE_AGENTE**: Agentes inmobiliarios
- **ROLE_ADMIN**: Administradores del sistema

## 📊 Funcionalidades del Panel de Administración

### Dashboard
- **Estadísticas en Tiempo Real**: Usuarios, propiedades, solicitudes
- **Gráficos Interactivos**: Ventas, visitas, actividad
- **Alertas del Sistema**: Notificaciones importantes
- **Actividad Reciente**: Últimas acciones del sistema

### Gestión de Usuarios
- **Lista Completa**: Todos los usuarios registrados
- **Búsqueda Avanzada**: Por nombre, email, rol, estado
- **Filtros**: Por rol (cliente, agente, admin) y estado (activo, suspendido)
- **Acciones**: Bloquear/desbloquear, cambiar rol, eliminar
- **Exportación**: CSV con filtros aplicados

### Gestión de Propiedades
- **Lista de Propiedades**: Todas las propiedades del sistema
- **Estados**: En revisión, activa, vendida, rechazada
- **Acciones**: Aprobar, rechazar, eliminar
- **Filtros**: Por tipo, estado, precio, ubicación
- **Exportación**: CSV con datos completos

### Gestión de Reportes
- **Reportes de Irregularidad**: Procesamiento de denuncias
- **Estados**: Pendiente, atendido, descartado
- **Acciones**: Resolver, descartar, eliminar
- **Estadísticas**: Total, pendientes, resueltos, descartados

### Sistema de Logs
- **Logs del Sistema**: Monitoreo completo de eventos
- **Filtros Avanzados**: Por nivel, módulo, fecha
- **Niveles**: INFO, WARNING, ERROR, DEBUG
- **Módulos**: AUTH, PROPERTY, USER, SYSTEM
- **Funciones**: Limpiar logs, exportar, ver detalles
- **Modal de Detalles**: Información completa de cada log

### Backup & Restore
- **Respaldo Automático**: Base de datos y archivos
- **Restauración**: Recuperación de datos
- **Gestión de Backups**: Lista y eliminación
- **Configuración**: Frecuencia y retención

### Configuración del Sistema
- **Ajustes Generales**: Configuración del sistema
- **Email**: Configuración SMTP
- **Seguridad**: Configuración de sesiones
- **Mantenimiento**: Modo mantenimiento

## 🔐 Seguridad

### Autenticación
- **Sesiones Seguras**: Configuración de cookies
- **Validación de Roles**: Control de acceso
- **Protección CSRF**: Tokens de seguridad
- **Sanitización**: Limpieza de datos de entrada

### Base de Datos
- **Prepared Statements**: Prevención de SQL Injection
- **Encriptación**: Contraseñas hasheadas
- **Backup Regular**: Respaldo automático
- **Logs de Acceso**: Registro de actividades

## 📧 Sistema de Notificaciones

### Email Automático
- **Confirmación de Registro**: Nuevos usuarios
- **Solicitudes de Compra**: Notificación a agentes
- **Citas Agendadas**: Confirmación a usuarios
- **Cambios de Estado**: Propiedades y solicitudes
- **Reportes**: Notificación de irregularidades

### Plantillas Personalizables
- **HTML Responsive**: Diseño moderno
- **Variables Dinámicas**: Datos personalizados
- **Múltiples Idiomas**: Soporte multiidioma
- **Configuración SMTP**: Servidores de email

## 📱 Responsive Design

### Diseño Adaptativo
- **Mobile First**: Optimizado para móviles
- **Tablet Friendly**: Interfaz para tablets
- **Desktop Optimized**: Experiencia completa
- **Touch Friendly**: Interacciones táctiles

### Componentes Responsive
- **Navegación**: Menú hamburguesa en móvil
- **Tablas**: Scroll horizontal en móvil
- **Formularios**: Campos optimizados
- **Modales**: Ventanas adaptativas

## 🚀 Optimización

### Rendimiento
- **Caché de Consultas**: Optimización de base de datos
- **Compresión de Imágenes**: Reducción de tamaño
- **Minificación CSS/JS**: Archivos optimizados
- **CDN**: Librerías externas

### SEO
- **URLs Amigables**: Estructura limpia
- **Meta Tags**: Información para buscadores
- **Sitemap**: Mapa del sitio
- **Open Graph**: Compartir en redes sociales

## 🐛 Debugging y Logs

### Sistema de Logs
- **Clase Logger**: Sistema profesional de logging
- **Niveles de Log**: INFO, WARNING, ERROR, DEBUG
- **Módulos**: AUTH, PROPERTY, USER, SYSTEM
- **Formato Estándar**: `[timestamp] LEVEL: message | module | user | ip`
- **Gestión**: Limpiar, exportar, filtrar

### Debugging
- **Modo Desarrollo**: Errores detallados
- **Logs de Error**: Registro de problemas
- **Validación**: Verificación de datos
- **Testing**: Scripts de prueba

## 📈 Estadísticas y Reportes

### Dashboard Analytics
- **Usuarios Activos**: Estadísticas en tiempo real
- **Propiedades**: Total, activas, vendidas
- **Solicitudes**: Nuevas, en proceso, completadas
- **Citas**: Programadas, realizadas, canceladas

### Reportes Exportables
- **CSV**: Datos tabulares
- **Filtros Aplicados**: Exportación personalizada
- **Múltiples Formatos**: Diferentes tipos de reporte
- **Programación**: Reportes automáticos

## 🔄 Mantenimiento

### Tareas Automáticas
- **Limpieza de Logs**: Eliminación de logs antiguos
- **Backup Automático**: Respaldo programado
- **Optimización DB**: Mantenimiento de base de datos
- **Cache**: Limpieza de caché

### Monitoreo
- **Logs del Sistema**: Monitoreo continuo
- **Alertas**: Notificaciones de problemas
- **Métricas**: Rendimiento del sistema
- **Uptime**: Disponibilidad del servicio

## 🤝 Contribución

### Guías de Contribución
1. Fork el proyecto
2. Crear rama feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

### Estándares de Código
- **PSR-12**: Estándares PHP
- **Comentarios**: Documentación clara
- **Nombres**: Variables y funciones descriptivas
- **Estructura**: Organización lógica

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

### Contacto
- **Email**: soporte@propeasy.com
- **Teléfono**: +1 809 359 5322
- **Documentación**: [docs.propeasy.com](https://docs.propeasy.com)

### Recursos
- **Documentación**: Guías completas
- **API**: Documentación de API
- **FAQ**: Preguntas frecuentes
- **Tutoriales**: Videos y guías

## 🎯 Roadmap

### Próximas Funcionalidades
- [ ] **App Móvil**: Aplicación nativa
- [ ] **Chat en Tiempo Real**: Comunicación instantánea
- [ ] **Pagos Online**: Integración de pagos
- [ ] **IA para Recomendaciones**: Machine Learning
- [ ] **API REST**: Servicios web
- [ ] **Multiidioma**: Soporte completo
- [ ] **Analytics Avanzado**: Métricas detalladas
- [ ] **Integración CRM**: Gestión de clientes

### Mejoras Técnicas
- [ ] **Microservicios**: Arquitectura escalable
- [ ] **Docker**: Contenedores
- [ ] **CI/CD**: Integración continua
- [ ] **Testing**: Cobertura completa
- [ ] **Performance**: Optimización avanzada

---

**PropEasy** - Transformando la forma de vender bienes raíces 🏠✨ 