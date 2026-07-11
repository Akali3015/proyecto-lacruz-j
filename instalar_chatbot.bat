@echo off
color 0A
echo =======================================================
echo    INSTALADOR AUTOMATICO DEL MICROSERVICIO (IA)
echo =======================================================
echo.

echo 1. Verificando si Python esta instalado...
python --version >nul 2>&1
if %errorlevel% neq 0 (
    color 0C
    echo ERROR: Python no esta instalado o no esta en el PATH.
    echo Por favor, instala Python marcando la casilla "Add to PATH".
    pause
    exit /b
)
echo Python detectado correctamente.
echo.

echo 2. Entrando a la carpeta del Microservicio...
cd /d "%~dp0"
if exist "MicroServicioJLaCruz" (
    cd MicroServicioJLaCruz
) else (
    echo Buscando en el directorio actual...
)
echo.

echo 3. Creando/Reparando el Entorno Virtual (Burbuja de Aislamiento)...
:: Si existe un entorno previo e incompleto, se borra para evitar conflictos
if exist "venv" (
    echo Detectado entorno virtual previo. Limpiando archivos antiguos...
    rd /s /q venv
)
python -m venv venv
echo Entorno creado exitosamente.
echo.

echo 4. Instalando las Librerias (FastAPI, Uvicorn, Google Gemini)...
echo Por favor espera, esto puede tardar unos minutos dependiendo del internet.
call venv\Scripts\activate.bat

:: Forzar la restauracion de pip por si la instalacion de Python es minimalista
echo Restaurando gestor pip...
python -m ensurepip --default-pip >nul 2>&1
python -m pip install --upgrade pip

echo Aplicando parche antibug para Pydantic (Rust)...
python -m pip uninstall pydantic pydantic-core -y >nul 2>&1
python -m pip install pydantic pydantic-core --no-cache-dir

echo Instalando el resto de dependencias...
python -m pip install fastapi uvicorn google-generativeai requests slowapi
echo.

echo =======================================================
echo  INSTALACION COMPLETADA CON EXITO!
echo  Ya puedes cerrar esta ventana y levantar el servidor.
echo =======================================================
pause
