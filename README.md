# 🏠 PropEasy - Sistema de Gestión Inmobiliaria

Sistema completo de gestión inmobiliaria con chat en tiempo real, citas, reportes y más.

## 🚀 Inicio Rápido

### **Requisitos:**
- PHP 8.0+
- MySQL
- ngrok (se descarga automáticamente)

### **Opciones de inicio:**

#### **1. Con ngrok (Internet + Local):**
```powershell
.\start_propeasy.bat
```

#### **2. Solo local (sin ngrok):**
```powershell
.\start_local.bat
```

#### **3. Solo WebSocket local:**
```powershell
.\start_websocket_local.bat
```

#### **4. Probar configuración:**
```powershell
php test_websocket.php
```

**El script principal hace todo automáticamente:**
- ✅ Descarga e instala ngrok
- ✅ Inicia servidor web (puerto 80)
- ✅ Inicia WebSocket (puerto 8080)
- ✅ Configura ngrok con túneles HTTP y WebSocket
- ✅ Actualiza la configuración dinámicamente
- ✅ Abre el navegador automáticamente

---

## 🌐 Acceso

### **Modo Local (sin ngrok):**
- **Web**: `http://localhost:80`
- **WebSocket**: `ws://localhost:8080`
- **Desde otra máquina**: `http://[TU-IP]:80` y `ws://[TU-IP]:8080`

### **Modo ngrok (Internet + Local):**
- **Local**: `http://localhost:80`
- **Internet**: URL de ngrok en `http://localhost:4040`
- **WebSocket**: Configurado automáticamente para chat en tiempo real

---

## 💬 Chat en Tiempo Real

El chat funciona automáticamente una vez que el sistema esté corriendo. Incluye:
- ✅ Mensajes en tiempo real
- ✅ Chat directo entre usuarios
- ✅ Chat con agentes desde propiedades
- ✅ Notificaciones automáticas

---

## 🛠️ Características Principales

### **Gestión de Usuarios:**
- ✅ **Sistema de roles**: Admin, Agentes, Clientes
- ✅ **Registro con campos obligatorios**: Ciudad y Sector
- ✅ **Perfiles públicos de agentes** activos por defecto
- ✅ **Validación de email** con tokens
- ✅ **Gestión de sesiones** segura

### **Gestión de Propiedades:**
- ✅ **Publicación de propiedades** por clientes
- ✅ **Validación por agentes** con sistema de aprobación
- ✅ **Búsqueda avanzada** con filtros múltiples
- ✅ **Gestión de imágenes** con galería
- ✅ **Estados de publicación**: Activa, Vendida, En revisión, Rechazada

### **Sistema de Comunicación:**
- ✅ **Chat en tiempo real** con WebSocket
- ✅ **Sistema de citas** con calendario
- ✅ **Solicitudes de compra** con seguimiento
- ✅ **Notificaciones automáticas** por email

### **Reportes y Estadísticas:**
- ✅ **Dashboard administrativo** completo
- ✅ **Estadísticas de agentes** y propiedades
- ✅ **Reportes de actividad** detallados
- ✅ **Exportación a CSV**

---

## 🗄️ Base de Datos

### **Estructura Optimizada:**
- ✅ **Tabla usuarios** con campos obligatorios (ciudad, sector)
- ✅ **Campos eliminados**: licencia_inmobiliaria, especialidades, experiencia_anos, horario_disponibilidad, redes_sociales
- ✅ **Perfil público** activo por defecto para agentes
- ✅ **Relaciones optimizadas** entre tablas

### **Tablas Principales:**
- `usuarios` - Gestión de usuarios y perfiles
- `propiedades` - Catálogo de propiedades
- `imagenes_propiedades` - Galería de imágenes
- `solicitudes_compra` - Solicitudes de compra
- `citas` - Sistema de citas
- `mensajes` - Chat en tiempo real
- `logs_actividad` - Auditoría del sistema

---

## 📁 Estructura del Proyecto

```
propeasy/
├── app/                    # Código principal
│   ├── controllers/        # Controladores MVC
│   ├── models/            # Modelos de datos
│   ├── views/             # Vistas y templates
│   ├── core/              # Núcleo del sistema
│   ├── helpers/           # Funciones auxiliares
│   └── websocket_server.php # Servidor WebSocket
├── config/                 # Configuración
│   ├── config.php         # Configuración general
│   ├── database.php       # Configuración BD
│   └── ngrok.php          # Configuración dinámica ngrok
├── public/                 # Archivos públicos
│   ├── css/               # Estilos
│   ├── js/                # JavaScript
│   ├── uploads/           # Archivos subidos
│   └── index.php          # Punto de entrada
├── vendor/                 # Dependencias Composer
├── logs/                   # Logs del sistema
├── database/               # Estructura de base de datos
│   └── scheme.sql         # Esquema completo
├── start_propeasy.bat      # Script de inicio automático
├── composer.json           # Dependencias PHP
├── composer.lock           # Lock de dependencias
├── .htaccess               # Configuración Apache
├── .gitignore              # Archivos ignorados
└── README.md               # Documentación
```

---

## 🔧 Configuración

### **Archivos de Configuración:**
- `config/config.php` - Configuración general de la aplicación
- `config/database.php` - Configuración de base de datos
- `config/ngrok.php` - Configuración dinámica para ngrok

### **Variables de Entorno:**
- `APP_URL` - URL base de la aplicación
- `UPLOADS_URL` - URL para archivos subidos
- `WS_URL` - URL del WebSocket (configurada automáticamente)

---

## 🚀 Despliegue

### **Desarrollo Local:**
1. Clonar el repositorio
2. Configurar base de datos MySQL
3. Ejecutar `composer install`
4. Ejecutar `start_propeasy.bat`

### **Producción:**
1. Configurar servidor web (Apache/Nginx)
2. Configurar base de datos
3. Configurar WebSocket server
4. Configurar SSL para chat seguro

---

## 🐛 Solución de Problemas

### **Error 500 en búsqueda de propiedades:**
- ✅ **Solucionado**: Error en filtro de precio máximo corregido

### **Chat no funciona en tiempo real:**
- ✅ **Solucionado**: Configuración automática de WebSocket con ngrok

### **Agentes no aparecen en listado:**
- ✅ **Solucionado**: Perfil público activo por defecto

### **Error 404 en perfiles de agentes:**
- ✅ **Solucionado**: Consultas SQL corregidas para campos eliminados

---

## 📊 Estado Actual

### **✅ Funcionalidades Completadas:**
- Sistema de usuarios completo
- Gestión de propiedades
- Chat en tiempo real
- Sistema de citas
- Reportes y estadísticas
- Configuración automática con ngrok
- Validación de formularios
- Sistema de notificaciones

### **✅ Base de Datos:**
- Estructura optimizada
- Campos obligatorios implementados
- Campos innecesarios eliminados
- Relaciones optimizadas

### **✅ Interfaz de Usuario:**
- Diseño responsive
- Validación en tiempo real
- Mensajes de error claros
- Navegación intuitiva

---

## 🎯 Próximas Mejoras

- [ ] Sistema de pagos integrado
- [ ] App móvil nativa
- [ ] Integración con mapas
- [ ] Sistema de calificaciones
- [ ] Notificaciones push

---

**🎯 ¡PropEasy está listo para producción!**

*Sistema completo de gestión inmobiliaria con todas las funcionalidades principales implementadas y probadas.* 