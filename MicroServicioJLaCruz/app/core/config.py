import os
from dotenv import load_dotenv

# Cargar variables del archivo .env
load_dotenv()

# Configuraciones principales
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
MAIN_SYSTEM_API_URL = os.getenv("MAIN_SYSTEM_API_URL")

if not GEMINI_API_KEY or GEMINI_API_KEY == "TU_API_KEY_AQUI":
    print("ADVERTENCIA: No se ha configurado la API Key de Gemini en el archivo .env")
