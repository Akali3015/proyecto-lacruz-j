@echo off
echo ==========================================
echo    INICIANDO EL MICROSERVICIO DEL CHATBOT
echo ==========================================

:: Entrar automaticamente a la carpeta donde este guardado este archivo
cd /d "%~dp0"

:: Si no existe el entorno virtual, lo crea y lo instala todo
if not exist "venv\Scripts\activate.bat" (
    echo [INFO] Creando entorno virtual e instalando librerias por primera vez...
    python -m venv venv
    call venv\Scripts\activate.bat
    pip install -r requirements.txt
) else (
    echo [INFO] Entorno ya instalado, activando...
    call venv\Scripts\activate.bat
)

:: Encender el servidor
echo [INFO] Encendiendo el motor de Inteligencia Artificial...
python -m uvicorn app.main:app --port 8000

pause
