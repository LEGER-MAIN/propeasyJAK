@echo off
chcp 65001 >nul
echo ========================================
echo    INICIANDO WEBSOCKET LOCAL
echo ========================================

echo [1/3] Deteniendo WebSocket anterior...
taskkill /f /im php.exe >nul 2>&1

echo [2/3] Iniciando WebSocket en puerto 8080...
start "WebSocket Local" cmd /c "php app/websocket_server.php"

echo [3/3] Verificando que el WebSocket esté funcionando...
timeout /t 3 /nobreak >nul

echo.
echo ========================================
echo    WEBSOCKET LOCAL INICIADO
echo ========================================
echo.
echo URLs disponibles:
echo - WebSocket Local: ws://localhost:8080
echo - Web Local: http://localhost:80 (si tienes servidor web)
echo.
echo Para probar el WebSocket:
echo 1. Abre el navegador en http://localhost:80
echo 2. El chat funcionará automáticamente
echo 3. O usa un cliente WebSocket para probar ws://localhost:8080
echo.
pause 