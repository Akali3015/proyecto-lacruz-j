from pydantic import BaseModel, field_validator
from typing import List, Optional, Dict
import re

class MensajeHistorial(BaseModel):
    texto: str
    respuesta: Optional[str] = None
    fecha: str

    @field_validator('fecha')
    @classmethod
    def validar_formato_fecha(cls, v):
        # Acepta formatos: "2024-01-15 14:30:00" o "2024-01-15"
        patron = r'^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$'
        if not re.match(patron, v):
            raise ValueError('Formato de fecha inválido')
        return v

class ChatRequest(BaseModel):
    mensaje: str
    sesion_id: str
    historial: List[MensajeHistorial] = []
    catalogo: Dict = {}
