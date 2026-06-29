from pydantic import BaseModel
from typing import List, Optional, Dict

class MensajeHistorial(BaseModel):
    texto: str
    respuesta: Optional[str] = None
    fecha: str

class ChatRequest(BaseModel):
    mensaje: str
    sesion_id: str
    historial: List[MensajeHistorial] = []
