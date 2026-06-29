import requests
from app.core.config import MAIN_SYSTEM_API_URL

def obtener_catalogo():
    """
    Hace una petición HTTP al sistema principal (PHP) para obtener 
    los productos y servicios actualizados de la base de datos.
    """
    try:
        headers = {"X-Internal-Token": "JLacruz2026Secure"}
        response = requests.get(MAIN_SYSTEM_API_URL, headers=headers)
        response.raise_for_status()
        data = response.json()
        if data.get("status") == "success":
            return data.get("data", {})
        else:
            print("Error en el formato de respuesta del catálogo:", data)
            return None
    except Exception as e:
        print(f"Error al conectar con el sistema principal: {e}")
        return None
