@echo off
echo ==========================================python -m pip install fastapi
echo    INICIANDO EL MICROSERVICIO DEL CHATBOT
echo ==========================================

:: Entrar automaticamente a la carpeta donde este guardado este archivo
cd /d "%~dp0"

:: Si no existe el entorno virtual, lo crea
if not exist "venv\Scripts\activate.bat" (
    echo [INFO] Creando entorno virtual por primera vez...
    python -m venv venv
)

:: Activar el entorno
call venv\Scripts\activate.bat

:: Verificar de manera inteligente si falta alguna dependencia importándola con Python
echo [INFO] Verificando integridad del entorno virtual...
python -c "import fastapi, uvicorn, slowapi, requests, google.generativeai" >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Detectadas dependencias faltantes o corruptas. Iniciando reparacion automatica...
    python -m ensurepip --default-pip >nul 2>&1
    python -m pip install --upgrade pip
    pip install -r requirements.txt
) else (
    echo [INFO] Entorno virtual verificado y completo.
)

:: Encender el servidor
echo [INFO] Encendiendo el motor de Inteligencia Artificial...
python -m uvicorn main:app --port 8000

pause
