# 🏠 PropEasy - Sistema de Gestión Inmobiliaria

Sistema completo de gestión inmobiliaria con chat en tiempo real, citas, reportes y más.

## 🚀 Inicio Rápido

### **Requisitos:**
- PHP 8.0+
- ngrok (incluido en el proyecto)

### **Para iniciar:**
```powershell
.\start_propeasy.bat
```

**El script hace todo automáticamente:**
- ✅ Inicia servidor web
- ✅ Inicia WebSocket
- ✅ Configura ngrok
- ✅ Configura el túnel WebSocket
- ✅ Actualiza la configuración
- ✅ Abre el navegador automáticamente

---

## 🌐 Acceso

- **Local**: `http://localhost:80`
- **Internet**: URL de ngrok en `http://localhost:4040`

---

## 💬 Chat en Tiempo Real

El chat funciona automáticamente una vez que el sistema esté corriendo.

---

## 🛠️ Características

- ✅ **Chat en tiempo real** con WebSocket
- ✅ **Sistema de citas** con calendario
- ✅ **Gestión de propiedades** completa
- ✅ **Reportes y estadísticas**
- ✅ **Sistema de usuarios** (admin, agentes, clientes)
- ✅ **Notificaciones** automáticas
- ✅ **Acceso desde Internet** con ngrok

---

## 📁 Estructura del Proyecto

```
propeasy/
├── app/                    # Código principal
├── config/                 # Configuración
├── public/                 # Archivos públicos
├── vendor/                 # Dependencias
├── logs/                   # Logs del sistema
├── database/               # Estructura de base de datos
├── start_propeasy.bat      # Script principal
├── ngrok.exe               # ngrok incluido
├── composer.json           # Dependencias
├── composer.lock           # Lock de dependencias
├── .htaccess               # Configuración Apache
├── .gitignore              # Archivos ignorados
└── README.md               # Este archivo
```

---

**🎯 ¡PropEasy está listo para usar!** 