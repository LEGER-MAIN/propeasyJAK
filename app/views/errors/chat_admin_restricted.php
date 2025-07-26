<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Restringido - PropEasy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --color-azul-marino: #1e3a8a;
            --color-azul-marino-light: #dbeafe;
            --text-primary: #1f2937;
            --bg-light: #ffffff;
            --color-gris-claro: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-azul-marino), #3b82f6);
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2rem;
            color: #d97706;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-azul-marino);
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .error-details h3 {
            color: var(--color-azul-marino);
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-details ul {
            list-style: none;
            padding: 0;
        }

        .error-details li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-details li:last-child {
            border-bottom: none;
        }

        .error-details i {
            color: #10b981;
            width: 20px;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--color-azul-marino);
            color: white;
        }

        .btn-primary:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .error-container {
                padding: 2rem;
                margin: 1rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h1 class="error-title">Acceso Restringido</h1>
        
        <p class="error-message">
            El sistema de chat no está disponible para administradores. 
            Esta funcionalidad está diseñada exclusivamente para la comunicación 
            entre clientes y agentes inmobiliarios.
        </p>

        <div class="error-details">
            <h3>
                <i class="fas fa-info-circle"></i>
                ¿Por qué no puedo acceder al chat?
            </h3>
            <ul>
                <li>
                    <i class="fas fa-check"></i>
                    Los administradores gestionan el sistema, no las ventas
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    El chat es para comunicación cliente-agente
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    Puedes monitorear conversaciones desde el dashboard
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    Acceso a reportes y estadísticas del sistema
                </li>
            </ul>
        </div>

        <div class="btn-group">
            <a href="/dashboard" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i>
                Ir al Dashboard
            </a>
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html> 