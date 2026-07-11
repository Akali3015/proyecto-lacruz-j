# Ejecuta el microservicio desde el entorno virtual local
# Úsalo desde cualquier carpeta, el script se ubicará en MicroServicioJLaCruz\start.ps1
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$PythonExe = Join-Path $ScriptDir 'venv\Scripts\python.exe'
if (-not (Test-Path $PythonExe)) {
    Write-Error "No se encontró el Python del entorno virtual en: $PythonExe"
    exit 1
}
Push-Location $ScriptDir
& $PythonExe -m uvicorn main:app --reload --port 8000
Pop-Location
