from fastapi import APIRouter
from fastapi.responses import JSONResponse
from app.models.chat_schemas import ChatRequest
from app.services.gemini_chatbot import generar_respuesta_bot

from fastapi import APIRouter, Header, HTTPException

router = APIRouter()

@router.post("/chat")
async def chat_endpoint(request: ChatRequest, x_internal_token: str = Header(None)):
    if x_internal_token != "JLacruz2026Secure":
        raise HTTPException(status_code=403, detail="No autorizado")

    try:
        respuesta = generar_respuesta_bot(
            mensaje=request.mensaje,
            sesion_id=request.sesion_id,
            historial=request.historial
        )
        return {"status": "success", "respuesta": respuesta}

    except Exception as e:
        error_str = str(e)
        print(f"Error en chat_endpoint: {error_str}")

        if "429" in error_str or "quota" in error_str.lower():
            msg = "El asistente esta ocupado. Por favor intenta de nuevo en unos segundos."
        else:
            msg = f"Error interno: {error_str[:150]}"

        return JSONResponse(status_code=200, content={"status": "success", "respuesta": msg})
