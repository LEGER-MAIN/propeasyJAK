# PropEasy - Sistema de Gestión Inmobiliaria

![PropEasy Logo](https://img.shields.io/badge/PropEasy-Sistema%20Inmobiliario-blue)
![PHP Version](https://img.shields.io/badge/PHP-8.0+-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Descripción

PropEasy es un sistema web completo de gestión inmobiliaria desarrollado en PHP que permite a agentes inmobiliarios gestionar propiedades, clientes, citas y reportes de manera eficiente. El sistema incluye un panel de administración robusto, gestión de usuarios, exportación de reportes en PDF y funcionalidades avanzadas de búsqueda.

## ✨ Características Principales

### 🏠 Gestión de Propiedades
- **CRUD completo** de propiedades inmobiliarias
- **Múltiples tipos** de propiedades (casa, apartamento, terreno, comercial, etc.)
- **Gestión de imágenes** con múltiples fotos por propiedad
- **Estados de propiedad** (activa, en revisión, vendida, rechazada)
- **Búsqueda avanzada** por ciudad, tipo, precio, características
- **Filtros dinámicos** con búsqueda por texto libre

### 👥 Gestión de Usuarios
- **Sistema de roles** (admin, agente, cliente)
- **Perfiles completos** con información personal y profesional
- **Gestión de permisos** por rol
- **Bloqueo/desbloqueo** de usuarios
- **Cambio de roles** dinámico

### 📅 Sistema de Citas
- **Agenda de citas** entre agentes y clientes
- **Notificaciones** automáticas por email
- **Estado de citas** (pendiente, confirmada, completada, cancelada)
- **Calendario visual** para gestión

### 📊 Panel de Administración
- **Dashboard completo** con estadísticas en tiempo real
- **Gráficos visuales** de propiedades y usuarios
- **Exportación PDF** con gráficos incluidos
- **Filtros por períodos** (mes, trimestre, año)
- **Actividades del sistema** con logs detallados

### 🔍 Búsqueda y Filtros
- **Búsqueda por ciudad** con input de texto libre
- **Filtros múltiples** (estado, tipo, precio)
- **Búsqueda semántica** en títulos y descripciones
- **Filtros activos** con indicadores visuales

### 📈 Reportes y Exportación
- **Exportación PDF** profesional con DOMPDF
- **Gráficos incluidos** en reportes
- **Estadísticas detalladas** por períodos
- **Reportes personalizables** para administradores

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 8.0+
- **Base de Datos:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **PDF:** DOMPDF para generación de reportes
- **Email:** PHPMailer para notificaciones
- **WebSockets:** Ratchet para comunicación en tiempo real
- **Dependencias:** Composer para gestión de paquetes

## 📁 Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/          # Controladores MVC
│   ├── models/              # Modelos de datos
│   ├── views/               # Vistas y templates
│   ├── helpers/             # Clases auxiliares
│   └── core/                # Núcleo del sistema
├── config/                  # Configuración del sistema
├── database/                # Esquemas y migraciones
├── logs/                    # Logs del sistema
├── public/                  # Archivos públicos
│   ├── css/                 # Estilos CSS
│   ├── js/                  # JavaScript
│   └── uploads/             # Archivos subidos
├── vendor/                  # Dependencias de Composer
└── README.md               # Este archivo
```

## 🚀 Instalación

### Requisitos Previos
- PHP 8.0 o superior
- MySQL 5.7+ o MariaDB 10.2+
- Composer
- Servidor web (Apache/Nginx)

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
   - Importar el esquema desde `database/scheme.sql`
   - Configurar las credenciales en `config/database.php`

4. **Configurar el servidor web**
   - Apuntar el document root a la carpeta `public/`
   - Configurar las reglas de rewrite en `.htaccess`

5. **Configurar permisos**
   ```bash
   chmod 755 public/uploads/
   chmod 755 logs/
   ```

6. **Iniciar el sistema**
   ```bash
   # Para desarrollo local
   php -S localhost:8000 -t public
   
   # O usar los scripts incluidos
   ./start_local.bat
   ```

## ⚙️ Configuración

### Archivos de Configuración

- **`config/config.php`** - Configuración general del sistema
- **`config/database.php`** - Configuración de la base de datos
- **`config/ngrok.php`** - Configuración para desarrollo con ngrok

### Variables de Entorno

```php
// Configuración de la aplicación
define('APP_NAME', 'PropEasy');
define('APP_URL', 'http://localhost:8000');
define('APP_EMAIL', 'admin@propeasy.com');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'propeasy');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 👤 Roles de Usuario

### 🔧 Administrador
- Gestión completa del sistema
- Panel de administración
- Gestión de usuarios y roles
- Reportes y estadísticas
- Exportación de datos

### 🏠 Agente Inmobiliario
- Gestión de propiedades propias
- Agenda de citas con clientes
- Dashboard personal
- Reportes de ventas

### 👤 Cliente
- Búsqueda de propiedades
- Solicitud de citas
- Favoritos y seguimiento
- Perfil personal

## 📊 Funcionalidades del Dashboard

### Estadísticas en Tiempo Real
- Total de usuarios y propiedades
- Propiedades por estado
- Usuarios por rol
- Citas pendientes
- Reportes del sistema

### Gráficos Visuales
- Gráfico de propiedades por estado
- Gráfico de usuarios por rol
- Exportación con gráficos incluidos

### Exportación de Reportes
- **Formatos:** PDF con gráficos
- **Períodos:** Mes, trimestre, año, completo
- **Contenido:** Estadísticas, gráficos, actividades recientes

## 🔍 Búsqueda y Filtros

### Búsqueda de Propiedades
- **Por ciudad:** Input de texto libre con búsqueda parcial
- **Por tipo:** Dropdown con tipos disponibles
- **Por estado:** Filtro por estado de la propiedad
- **Búsqueda general:** Título, dirección, descripción

### Filtros Activos
- Indicadores visuales de filtros aplicados
- Limpieza fácil de filtros
- Combinación de múltiples filtros

## 📧 Sistema de Notificaciones

### Email Automático
- Confirmación de citas
- Notificaciones de estado de propiedades
- Alertas del sistema
- Reportes de actividad

### WebSockets
- Notificaciones en tiempo real
- Actualizaciones de estado
- Chat entre usuarios

## 🛡️ Seguridad

### Autenticación
- Sistema de login seguro
- Protección de rutas por rol
- Tokens CSRF
- Validación de sesiones

### Validación de Datos
- Sanitización de inputs
- Validación de formularios
- Protección contra SQL injection
- Validación de archivos

## 📝 Logs y Monitoreo

### Sistema de Logs
- Logs de errores en `logs/error.log`
- Logs de actividad del sistema
- Logs de transacciones importantes
- Monitoreo de rendimiento

## 🚀 Despliegue

### Producción
1. Configurar servidor web (Apache/Nginx)
2. Configurar SSL/HTTPS
3. Optimizar PHP (OPcache, etc.)
4. Configurar backup automático
5. Monitoreo de logs

### Desarrollo
- Usar `start_local.bat` para desarrollo local
- Configurar ngrok para pruebas externas
- Usar `start_websocket_local.bat` para WebSockets

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

- **Email:** soporte@propeasy.com
- **Documentación:** [Wiki del proyecto](https://github.com/tu-usuario/propeasy/wiki)
- **Issues:** [GitHub Issues](https://github.com/tu-usuario/propeasy/issues)

## 🔄 Changelog

### v2.0.0 (Actual)
- ✅ Sistema de exportación PDF con gráficos
- ✅ Búsqueda por ciudad con input de texto
- ✅ Dashboard mejorado con estadísticas en tiempo real
- ✅ Sistema de filtros avanzados
- ✅ Gestión completa de usuarios y roles
- ✅ Sistema de citas y notificaciones

### v1.0.0
- ✅ CRUD básico de propiedades
- ✅ Sistema de autenticación
- ✅ Panel de administración básico

---

**PropEasy** - Simplificando la gestión inmobiliaria desde 2024 🏠✨ 