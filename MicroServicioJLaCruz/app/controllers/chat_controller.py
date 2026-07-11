from fastapi import APIRouter, Header, HTTPException, Request
from fastapi.responses import JSONResponse
from app.models.chat_schemas import ChatRequest
from app.services.gemini_chatbot import generar_respuesta_bot
from slowapi import Limiter
from slowapi.util import get_remote_address
import os
import logging
from dotenv import load_dotenv

load_dotenv()
TOKEN_SECRETO = os.getenv("TOKEN_MICROSERVICIO")

# Limitador compartido con main.py — mismo key por IP
limitador = Limiter(key_func=get_remote_address)

router = APIRouter()

@router.post("/chat")
@limitador.limit("20/minute")
async def chat_endpoint(
    request: Request,
    datos: ChatRequest,
    x_internal_token: str = Header(None)
):
    # Validar token de seguridad interno
    if x_internal_token != TOKEN_SECRETO:
        raise HTTPException(status_code=403, detail="No autorizado")

    try:
        respuesta = generar_respuesta_bot(
            mensaje=datos.mensaje,
            sesion_id=datos.sesion_id,
            historial=datos.historial,
            catalogo=datos.catalogo
        )
        return {"status": "success", "respuesta": respuesta}

    except Exception as e:
        # El error técnico va al log del servidor, nunca al cliente
        logging.error("Error en chat_endpoint: %s", str(e))

        if "429" in str(e) or "quota" in str(e).lower():
            msg = "El asistente esta ocupado. Por favor intenta de nuevo en unos segundos."
        else:
            msg = "El asistente presentó un error interno. Por favor, intente de nuevo más tarde."

        return JSONResponse(status_code=200, content={"status": "success", "respuesta": msg})
