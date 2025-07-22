<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Panel Administrativo - ' . APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables (si es necesario) -->
    <?php if (isset($includeDataTables) && $includeDataTables): ?>
        <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <?php endif; ?>
    
    <style>
        :root {
            --admin-primary: #2c3e50;
            --admin-secondary: #34495e;
            --admin-success: #27ae60;
            --admin-warning: #f39c12;
            --admin-danger: #e74c3c;
            --admin-info: #3498db;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1001;
            border-bottom: 3px solid var(--admin-info);
        }
        
        .admin-header .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .admin-header .header-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .admin-header .header-subtitle {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            margin: 0;
            font-weight: 300;
        }
        
        .admin-header .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .admin-header .header-clock {
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .admin-header .btn-header {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }
        
        .admin-header .btn-header:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .admin-header .btn-header.btn-primary {
            background: var(--admin-info);
            border-color: var(--admin-info);
        }
        
        .admin-header .btn-header.btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
        }
        
        .admin-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 80px); /* Altura del header */
        }
        
        .admin-sidebar {
            background: white;
            width: 250px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: calc(100vh - 80px);
            overflow-y: auto;
            z-index: 1000;
        }
        
        .admin-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .nav-link {
            color: var(--admin-primary);
            padding: 14px 20px;
            border-radius: 8px;
            margin: 8px 10px;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: block;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--admin-primary);
            color: white;
            transform: translateX(5px);
            text-decoration: none;
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stats-summary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .danger-zone {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .danger-zone h4 {
            color: white;
            margin-bottom: 15px;
        }
        
        .btn-danger-zone {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-danger-zone:hover {
            background: white;
            color: var(--admin-danger);
        }
        
        /* Badges y elementos de estado */
        .role-badge, .status-badge, .type-badge, .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .role-badge.role-admin { background: var(--admin-danger); color: white; }
        .role-badge.role-agente { background: var(--admin-warning); color: white; }
        .role-badge.role-cliente { background: var(--admin-info); color: white; }
        
        .status-badge.status-activo { background: var(--admin-success); color: white; }
        .status-badge.status-suspendido { background: var(--admin-danger); color: white; }
        .status-badge.status-pendiente { background: var(--admin-warning); color: white; }
        .status-badge.status-resuelto { background: var(--admin-success); color: white; }
        .status-badge.status-descartado { background: #6c757d; color: white; }
        .status-badge.status-en_revision { background: var(--admin-warning); color: white; }
        .status-badge.status-activa { background: var(--admin-success); color: white; }
        .status-badge.status-vendida { background: var(--admin-info); color: white; }
        .status-badge.status-rechazada { background: var(--admin-danger); color: white; }
        
        .type-badge.type-casa { background: var(--admin-primary); color: white; }
        .type-badge.type-apartamento { background: var(--admin-info); color: white; }
        .type-badge.type-terreno { background: var(--admin-success); color: white; }
        .type-badge.type-comercial { 
            background: linear-gradient(135deg, #8B4513, #D2691E); 
            color: white; 
            border: 1px solid #A0522D;
            box-shadow: 0 2px 4px rgba(139, 69, 19, 0.3);
        }
        .type-badge.type-local_comercial { 
            background: linear-gradient(135deg, #8B4513, #D2691E); 
            color: white; 
            border: 1px solid #A0522D;
            box-shadow: 0 2px 4px rgba(139, 69, 19, 0.3);
        }
        .type-badge.type-irregularidad { background: var(--admin-danger); color: white; }
        .type-badge.type-spam { background: #6c757d; color: white; }
        .type-badge.type-inapropiado { background: var(--admin-warning); color: white; }
        .type-badge.type-fraude { background: var(--admin-danger); color: white; }
        .type-badge.type-queja_agente { background: var(--admin-warning); color: white; }
        .type-badge.type-problema_plataforma { background: var(--admin-info); color: white; }
        .type-badge.type-informacion_falsa { background: var(--admin-danger); color: white; }
        .type-badge.type-otro { background: #6c757d; color: white; }
        
        .priority-badge.priority-alta { background: var(--admin-danger); color: white; }
        .priority-badge.priority-media { background: var(--admin-warning); color: white; }
        .priority-badge.priority-baja { background: var(--admin-success); color: white; }
        
        /* Badges para logs */
        .level-badge, .module-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .level-badge.level-error { background: var(--admin-danger); color: white; }
        .level-badge.level-warning { background: var(--admin-warning); color: white; }
        .level-badge.level-info { background: var(--admin-info); color: white; }
        .level-badge.level-debug { background: #6c757d; color: white; }
        
        .module-badge.module-auth { background: var(--admin-primary); color: white; }
        .module-badge.module-property { background: var(--admin-success); color: white; }
        .module-badge.module-user { background: var(--admin-info); color: white; }
        .module-badge.module-system { background: var(--admin-secondary); color: white; }
        
        /* Botón de toggle para sidebar móvil */
        .sidebar-toggle {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            margin-right: 1rem;
            display: none;
        }
        
        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            color: white;
        }
        
        /* Overlay para móvil */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
            
            .admin-header .header-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
            
            .admin-header .header-left {
                display: flex;
                align-items: center;
            }
            
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                position: fixed;
                top: 80px;
                left: 0;
                z-index: 1000;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .admin-container {
                flex-direction: column;
            }
            
            .admin-header {
                padding: 1rem;
            }
            
            .admin-header .header-title {
                font-size: 1.3rem;
                margin: 0;
            }
            
            .admin-header .header-subtitle {
                display: none;
            }
            
            .admin-header .header-actions {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .admin-header .header-clock {
                font-size: 0.8rem;
                padding: 0.3rem 0.8rem;
            }
            
            .admin-header .btn-header {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .admin-header .header-actions {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .admin-header .header-clock {
                order: -1;
            }
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--admin-primary);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--admin-secondary);
        }
    </style>
    
    <?php if (isset($additionalStyles)): ?>
        <style><?= $additionalStyles ?></style>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">
                    <i class="fas fa-cogs me-2"></i>Panel de Control Total
                </h1>
                <p class="header-subtitle">Administración completa del sistema PropEasy</p>
            </div>
            <div class="header-actions">
                <div class="header-clock" id="header-clock">
                    <i class="fas fa-clock me-1"></i>
                    <span id="current-time"></span>
                </div>
                <a href="/dashboard" class="btn-header">
                    <i class="fas fa-home me-1"></i>Volver al Sistema
                </a>
                <a href="/logout" class="btn-header btn-primary">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="p-4">
                <nav class="nav flex-column">
                    <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="/admin/dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>" href="/admin/users?action=list">
                        <i class="fas fa-users"></i> Gestión de Usuarios
                    </a>
                    <a class="nav-link <?= $currentPage === 'properties' ? 'active' : '' ?>" href="/admin/properties?action=list">
                        <i class="fas fa-home"></i> Gestión de Propiedades
                    </a>
                    <a class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>" href="/admin/reports?action=list">
                        <i class="fas fa-flag"></i> Gestión de Reportes
                    </a>
                    <a class="nav-link <?= $currentPage === 'logs' ? 'active' : '' ?>" href="/admin/logs">
                        <i class="fas fa-file-alt"></i> Logs del Sistema
                    </a>
                    <a class="nav-link <?= $currentPage === 'backup' ? 'active' : '' ?>" href="/admin/backup">
                        <i class="fas fa-database"></i> Backup & Restore
                    </a>
                    <a class="nav-link <?= $currentPage === 'config' ? 'active' : '' ?>" href="/admin/config">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                    <hr>
                    <a class="nav-link" href="/dashboard">
                        <i class="fas fa-home"></i> Volver al Sistema
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <?= $content ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables (si es necesario) -->
    <?php if (isset($includeDataTables) && $includeDataTables): ?>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <?php endif; ?>
    
    <!-- Scripts adicionales -->
    <?php if (isset($additionalScripts)): ?>
        <script><?= $additionalScripts ?></script>
    <?php endif; ?>
    
    <script>
        // Funcionalidad para sidebar móvil
        function toggleSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            
            // Prevenir scroll del body cuando sidebar está abierto
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        function closeSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        // Cerrar sidebar en móvil al hacer clic en un enlace
        document.querySelectorAll('.admin-sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
        
        // Cerrar sidebar al hacer clic fuera en móvil
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.admin-sidebar');
                const sidebarToggle = document.querySelector('.sidebar-toggle');
                
                if (!sidebar.contains(e.target) && !sidebarToggle?.contains(e.target)) {
                    closeSidebar();
                }
            }
        });
        
        // Cerrar sidebar al cambiar tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
        
        // Reloj en tiempo real
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        
        // Actualizar reloj cada segundo
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html> 