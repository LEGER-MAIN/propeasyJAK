@echo off
chcp 65001 >nul
echo ========================================
echo    INICIANDO PROPEASY - MODO LOCAL
echo ========================================

echo [1/4] Deteniendo procesos anteriores...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im ngrok.exe >nul 2>&1

echo [2/4] Iniciando servidor web en puerto 80...
start "Servidor Web Local" cmd /c "php -S 0.0.0.0:80 -t public"

echo [3/4] Iniciando WebSocket en puerto 8080...
start "WebSocket Local" cmd /c "php app/websocket_server.php"

echo [4/4] Esperando que los servicios se inicien...
timeout /t 3 /nobreak >nul

echo.
echo ========================================
echo    SISTEMA LOCAL INICIADO
echo ========================================
echo.
echo URLs disponibles:
echo - Web: http://localhost:80
echo - WebSocket: ws://localhost:8080
echo.
echo El chat funcionará automáticamente en modo local.
echo No necesitas ngrok para desarrollo local.
echo.
echo Para acceder desde otra máquina en la red:
echo - Web: http://[TU-IP]:80
echo - WebSocket: ws://[TU-IP]:8080
echo.
echo Para obtener tu IP: ipconfig
echo.
start http://localhost:80
pause 