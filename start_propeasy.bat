@echo off
chcp 65001 >nul
echo ========================================
echo    INICIANDO PROPEASY - TODO AUTOMATICO
echo ========================================

echo [1/7] Deteniendo procesos anteriores...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im ngrok.exe >nul 2>&1

echo [2/7] Iniciando servidor web...
start "Servidor Web" cmd /c "php -S 0.0.0.0:80 -t public"

echo [3/7] Iniciando WebSocket...
start "WebSocket" cmd /c "php app/websocket_server.php"

echo [4/7] Iniciando ngrok...
start "ngrok" cmd /c "ngrok.exe start app"

echo [5/7] Esperando que ngrok se conecte...
timeout /t 5 /nobreak >nul

echo [6/7] Configurando WebSocket y actualizando configuracion...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:4040/api/tunnels' -Method POST -ContentType 'application/json' -Body '{\"name\":\"websocket\",\"addr\":\"http://localhost:8080\",\"proto\":\"http\"}'; Write-Host '✅ Túnel WebSocket agregado' } catch { Write-Host '⚠️ Error al agregar túnel WebSocket' }"

echo [6.5/7] Actualizando configuracion del WebSocket...
powershell -Command "try { Start-Sleep -Seconds 2; $tunnels = Invoke-WebRequest -Uri 'http://localhost:4040/api/tunnels' | ConvertFrom-Json; $websocketUrl = ($tunnels.tunnels | Where-Object { $_.name -eq 'websocket' }).public_url; if ($websocketUrl) { $wssUrl = $websocketUrl -replace 'https://', 'wss://'; $content = Get-Content 'config/ngrok.php' -Raw; $newContent = $content -replace 'wss://[^\.]+\.ngrok-free\.app', $wssUrl; Set-Content 'config/ngrok.php' $newContent; Write-Host '✅ Configuración actualizada:' $wssUrl } else { Write-Host '⚠️ No se encontró túnel WebSocket' } } catch { Write-Host '⚠️ Error al actualizar configuración' }"

echo [7/7] Abriendo navegador...
timeout /t 2 /nobreak >nul
start http://localhost:80
start http://localhost:4040

echo.
echo ========================================
echo    SISTEMA INICIADO COMPLETAMENTE
echo ========================================
echo.
echo URLs disponibles:
echo - Local: http://localhost:80
echo - ngrok: http://localhost:4040
echo.
echo Navegador abierto automáticamente.
echo El chat funcionará en tiempo real automáticamente.
echo.
pause 