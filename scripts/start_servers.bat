@echo off
echo Iniciando servidores...

echo Iniciando servidor web en puerto 8000...
start "Servidor Web" php -S 127.0.0.1:8000 -t public

echo Iniciando servidor WebSocket en puerto 8080...
start "Servidor WebSocket" php app/websocket_server.php

echo Servidores iniciados!
echo.
echo Servidor web: http://localhost:8000
echo WebSocket: ws://localhost:8080
echo.
pause 