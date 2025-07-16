# PropEasy - Sistema Web de Venta de Bienes Raíces

## Descripción

PropEasy es una plataforma web diseñada para optimizar la gestión inmobiliaria mediante funcionalidades avanzadas de publicación, búsqueda, comunicación y gestión de propiedades.

## Características Principales

### Para Clientes
- **Publicación de Propiedades**: Los clientes pueden publicar sus propiedades para venta
- **Sistema de Token de Validación**: Cada propiedad requiere validación por un agente
- **Búsqueda Avanzada**: Filtros por precio, ubicación, características, etc.
- **Sistema de Favoritos**: Marcar y gestionar propiedades favoritas
- **Comunicación Directa**: Chat interno con agentes
- **Seguimiento de Solicitudes**: Estado de las solicitudes de compra

### Para Agentes Inmobiliarios
- **Gestión de Propiedades**: Panel para administrar propiedades asignadas
- **Validación de Propiedades**: Sistema de validación con tokens únicos
- **Dashboard Personalizado**: Estadísticas y métricas de rendimiento
- **Agenda de Citas**: Gestión de citas con clientes
- **Comunicación**: Chat interno con clientes

### Para Administradores
- **Dashboard Global**: Estadísticas del sistema
- **Gestión de Usuarios**: Control de agentes y clientes
- **Reportes**: Análisis de irregularidades y quejas
- **Configuración**: Ajustes del sistema

## Tecnologías Utilizadas

- **Backend**: PHP 8.2
- **Base de Datos**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Tailwind CSS
- **Iconos**: Font Awesome
- **Email**: PHPMailer

## Instalación

### Requisitos Previos
- PHP 8.2 o superior
- MySQL 8.0 o superior
- Apache/Nginx
- Composer

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
   - Importar el archivo `database/schema.sql`
   - Configurar las credenciales en `config/database.php`

4. **Configurar el servidor web**
   - Apuntar el DocumentRoot a la carpeta `public/`
   - Asegurar que el archivo `.htaccess` esté habilitado

5. **Configurar permisos**
   ```bash
   chmod 755 public/uploads/properties
   ```

6. **Configurar email** (opcional)
   - Editar las configuraciones SMTP en `config/config.php`

## Estructura del Proyecto

```
propeasy/
├── app/
│   ├── controllers/     # Controladores de la aplicación
│   ├── core/           # Clases principales (Router, Database)
│   ├── helpers/        # Funciones auxiliares
│   ├── models/         # Modelos de datos
│   └── views/          # Vistas de la aplicación
├── config/             # Archivos de configuración
├── database/           # Esquemas y migraciones
├── public/             # Archivos públicos (DocumentRoot)
├── vendor/             # Dependencias de Composer
└── README.md
```

## Usuarios de Prueba

El sistema incluye usuarios de prueba predefinidos:

### Administrador
- **Email**: admin@propeasy.com
- **Contraseña**: password
- **Rol**: Administrador

### Agente
- **Email**: juan.perez@propeasy.com
- **Contraseña**: password
- **Rol**: Agente Inmobiliario

### Cliente
- **Email**: maria.garcia@example.com
- **Contraseña**: password
- **Rol**: Cliente

## Flujo de Trabajo

### Publicación de Propiedad por Cliente

1. El cliente se registra/inicia sesión
2. Completa el formulario de publicación de propiedad
3. El sistema genera un token único de validación
4. La propiedad queda en estado "En Revisión"
5. El cliente comparte el token con un agente
6. El agente valida la propiedad usando el token
7. La propiedad se activa y aparece en el listado público

### Gestión por Agente

1. El agente accede a su panel
2. Ve las propiedades asignadas y pendientes de validación
3. Valida propiedades usando los tokens proporcionados
4. Gestiona solicitudes de compra
5. Agenda citas con clientes
6. Mantiene comunicación vía chat

## Sistema de Favoritos

### Características del Sistema de Favoritos

- **Marcar/Desmarcar Favoritos**: Toggle desde listado y detalle de propiedades
- **Listado de Favoritos**: Vista dedicada en `/favorites` con paginación
- **Contador en Header**: Contador dinámico actualizado en tiempo real
- **Notificaciones Visuales**: Alertas de éxito/error con animaciones
- **Seguridad**: Solo usuarios autenticados, validación de permisos

### Estructura de Base de Datos

#### Tabla: `favoritos_propiedades`
```sql
CREATE TABLE favoritos_propiedades (
  id int NOT NULL AUTO_INCREMENT,
  usuario_id int NOT NULL,
  propiedad_id int NOT NULL,
  fecha_agregado datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_usuario_propiedad (usuario_id, propiedad_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE
);
```

### API Endpoints de Favoritos

- `GET /favorites` - Listado de favoritos
- `POST /favorites/agregar` - Agregar favorito
- `POST /favorites/eliminar` - Eliminar favorito
- `POST /favorites/toggle` - Toggle favorito
- `GET /favorites/contador` - Obtener contador
- `GET /favorites/verificar` - Verificar estado

### Uso del Sistema de Favoritos

1. **Marcar Favorito**: Hacer clic en el botón de corazón en cualquier propiedad
2. **Ver Favoritos**: Ir a "Favoritos" en el menú de navegación
3. **Eliminar Favorito**: Desde el listado con botón "X" o desde propiedades haciendo clic nuevamente en el corazón

## Configuración

### Variables de Entorno

Las principales configuraciones se encuentran en `config/config.php`:

- **Base de datos**: `config/database.php`
- **Email SMTP**: Configuraciones en `config/config.php`
- **Rutas**: Definidas automáticamente
- **Seguridad**: Configuraciones de sesión y tokens

### Personalización

- **Tema**: Modificar las clases de Tailwind CSS en las vistas
- **Funcionalidades**: Extender los controladores y modelos
- **Base de datos**: Agregar nuevas tablas según necesidades

## Seguridad

- **Autenticación**: Sistema de login con verificación de email
- **Autorización**: Control de acceso basado en roles (RBAC)
- **Validación**: Sanitización de datos de entrada
- **Tokens**: Sistema de tokens únicos para validaciones
- **Sesiones**: Configuración segura de sesiones

## Mantenimiento

### Tareas Automáticas

El sistema incluye eventos MySQL para:
- Limpieza automática de tokens expirados
- Mantenimiento de la base de datos

### Logs

- **Errores**: `logs/error.log`
- **Actividad**: Tabla `logs_actividad` en la base de datos

## API (Futuro)

El sistema está preparado para incluir una API REST que permitirá:
- Integración con aplicaciones móviles
- Webhooks para notificaciones
- Integración con sistemas externos

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## Soporte

Para soporte técnico o preguntas:
- Email: soporte@propeasy.com
- Documentación: [Wiki del proyecto]
- Issues: [GitHub Issues]

## Changelog

### v1.0.0 (2025-01-14)
- Sistema de autenticación completo
- Gestión de propiedades
- Sistema de validación por tokens
- Dashboard para agentes
- Interfaz responsiva con Tailwind CSS
- Sistema de roles y permisos
- Sistema de favoritos completo
- Chat interno entre usuarios
- Sistema de notificaciones por email 