# PropEasy - Sistema Web de Venta de Bienes Raíces

Sistema web completo para la gestión y venta de propiedades inmobiliarias, con funcionalidades de chat en tiempo real, gestión de citas, y sistema de usuarios.

## 🚀 Características

- **Gestión de Propiedades**: Crear, editar y gestionar propiedades inmobiliarias
- **Chat en Tiempo Real**: Comunicación directa entre clientes y agentes
- **Sistema de Citas**: Programación y gestión de visitas a propiedades
- **Gestión de Usuarios**: Roles de cliente, agente y administrador
- **Sistema de Favoritos**: Los clientes pueden guardar propiedades favoritas
- **Notificaciones**: Sistema de alertas y recordatorios
- **Reportes**: Generación de reportes y estadísticas

## 🛠️ Tecnologías

- **Backend**: PHP 8.3
- **Base de Datos**: MySQL 8.4
- **WebSocket**: Ratchet (PHP)
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor**: PHP Development Server / Apache

## 📋 Requisitos

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Composer (para dependencias)
- Extensión PHP PDO
- Extensión PHP JSON

## ⚙️ Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd propeasy
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar base de datos**
   - Copiar `config/database.example.php` a `config/database.php`
   - Configurar credenciales de la base de datos
   - Importar el esquema: `database/scheme.sql`

4. **Configurar permisos**
   ```bash
   chmod -R 755 public/uploads/
   ```

## 🚀 Inicio Rápido

### Desarrollo Local
```bash
# Iniciar servidores automáticamente
scripts/start_servers.bat

# O manualmente:
php -S 127.0.0.1:8000 -t public
php app/websocket_server.php
```

### Acceso
- **Aplicación Web**: http://localhost:8000
- **WebSocket**: ws://localhost:8080

## 🔧 Configuración

### Variables de Entorno
- `APP_URL`: URL base de la aplicación
- `DB_HOST`: Host de la base de datos
- `DB_NAME`: Nombre de la base de datos
- `DB_USER`: Usuario de la base de datos
- `DB_PASS`: Contraseña de la base de datos

### Configuración de ngrok
El sistema detecta automáticamente si está ejecutándose a través de ngrok y ajusta las URLs correspondientes.

## 📁 Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/     # Controladores de la aplicación
│   ├── core/           # Núcleo del sistema
│   ├── helpers/        # Funciones auxiliares
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas de la aplicación
│   └── websocket_server.php
├── config/             # Archivos de configuración
├── database/           # Esquemas de base de datos
├── public/             # Archivos públicos
│   ├── css/           # Estilos
│   ├── js/            # JavaScript
│   └── uploads/       # Archivos subidos
├── scripts/           # Scripts de utilidad
└── vendor/            # Dependencias de Composer
```

## 🛠️ Scripts de Utilidad

- `scripts/start_servers.bat` - Inicia los servidores web y WebSocket
- `scripts/fix_images.php` - Repara las imágenes en la base de datos
- `scripts/cleanup_project.php` - Limpia archivos temporales
- `scripts/send_appointment_reminders.php` - Envía recordatorios de citas

## 🔐 Roles de Usuario

- **Cliente**: Puede buscar propiedades, contactar agentes, programar citas
- **Agente**: Puede gestionar propiedades, responder consultas, programar citas
- **Administrador**: Acceso completo al sistema, gestión de usuarios y reportes

## 📞 Soporte

Para soporte técnico o reportar problemas, contactar al equipo de desarrollo.

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo LICENSE para más detalles.

---

**PropEasy** - Simplificando la venta de bienes raíces 🏠 