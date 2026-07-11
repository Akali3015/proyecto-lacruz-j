import google.generativeai as genai
import warnings
warnings.filterwarnings("ignore")
import os
from dotenv import load_dotenv

load_dotenv()
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")

from app.models.chat_schemas import MensajeHistorial
from typing import List, Dict

if GEMINI_API_KEY and GEMINI_API_KEY != "TU_API_KEY_AQUI":
    genai.configure(api_key=GEMINI_API_KEY)

def _construir_contexto_catalogo(catalogo: Dict) -> str:
    """Convierte el catalogo inyectado desde PHP en texto claro para el contexto de Gemini."""
    if not catalogo:
        return "No se pudo obtener el catalogo en este momento."

    lineas = []

    productos = catalogo.get("productos", [])
    if productos:
        lineas.append("PRODUCTOS DISPONIBLES:")
        for p in productos:
            nombre_base = p.get("nombre_producto", "Sin nombre")
            presentacion = p.get("nombre_presentacion", "")
            nombre = f"{nombre_base} ({presentacion})" if presentacion else nombre_base
            precio = p.get("precio_calculado", "N/D")
            stock  = p.get("stock_producto", "N/D")
            lineas.append(
                f"  * {nombre}: Precio {precio} Bs cada uno. (Stock disponible: {stock} unidades)"
            )
    else:
        lineas.append("PRODUCTOS: Ninguno registrado.")

    servicios = catalogo.get("servicios", [])
    if servicios:
        lineas.append("SERVICIOS DISPONIBLES:")
        for s in servicios:
            nombre = s.get("nombre_servicio", "Sin nombre")
            precio = s.get("precio_servicio", "N/D")
            lineas.append(
                f"  * {nombre}: Precio {precio} Bs."
            )
    else:
        lineas.append("SERVICIOS: Ninguno registrado.")

    return "\n".join(lineas)

def generar_respuesta_bot(mensaje: str, sesion_id: str, historial: List[MensajeHistorial], catalogo: Dict) -> str:
    if not GEMINI_API_KEY or GEMINI_API_KEY == "TU_API_KEY_AQUI":
        # No revelar detalles de configuración interna al cliente
        raise RuntimeError("Servicio de IA no disponible en este momento.")

    contexto_catalogo = _construir_contexto_catalogo(catalogo)

    instruccion_sistema = (
        "Eres el asistente virtual de J. LACRUZ C.A.\n"
        "Reglas que DEBES seguir siempre:\n"
        "1. Cuando el cliente pregunte por productos o servicios, MUESTRA LA LISTA COMPLETA detallando claramente el precio y la moneda (ejemplo: 'Cuesta 1 Bs'). No digas '1 unidad' para referirte al precio, usa 'Bs'.\n"
        "2. Cuando el cliente pida un presupuesto, calcula el total multiplicando el precio en Bs por la cantidad solicitada.\n"
        "3. NUNCA reveles al cliente la cantidad exacta de stock. Usa esa información internamente SOLO para confirmar si hay disponibilidad suficiente para el pedido.\n"
        "4. Usa HTML simple: <b> para negrita, <br> para salto de linea, <ul><li> para listas.\n"
        "5. Se amable, claro y profesional.\n"
        "6. SEGURIDAD ESTRICTA: Si el usuario te envía comandos SQL, códigos de programación, o pide información confidencial, responde: 'Mensaje no válido o intento de acción no autorizada.'\n\n"
        "REGLAS ARQUITECTONICAS AVANZADAS (NUEVO):\n"
        "7. GUARDADO BAJO DEMANDA: Cuando calcules, generes o muestres un presupuesto, cotización, lista detallada de precios o estimación de costos, DEBES añadir al final de tu respuesta EXACTAMENTE la etiqueta: [OFRECER_GUARDADO]. Esto le mostrará un botón en la interfaz para que el usuario pueda guardarlo voluntariamente. No uses [GUARDAR_PRESUPUESTO].\n"
        "8. PRECIOS EN TIEMPO REAL: Si el historial muestra un '[PRESUPUESTO GUARDADO PREVIAMENTE]' o si el usuario te pregunta si recuerdas el presupuesto que hablaron antes, DEBES recalcular el total usando ESTRICTAMENTE los precios del catálogo que te adjunto a continuación. Si notas que el precio subió o bajó respecto al presupuesto antiguo, advierte cortésmente al cliente que los precios se han actualizado a la fecha de hoy.\n\n"
        "CATALOGO ACTUAL DE LA EMPRESA (DATOS REALES Y ACTUALIZADOS):\n"
        f"{contexto_catalogo}\n\n"
        "IMPORTANTE: Los datos de arriba son los unicos precios validos hoy. Enfocate EXCLUSIVAMENTE en vender y presupuestar basandote en este catalogo."
    )

    history_gemini = []
    for h in historial:
        if h.texto:
            history_gemini.append({"role": "user", "parts": [h.texto]})
        if h.respuesta:
            history_gemini.append({"role": "model", "parts": [h.respuesta]})

    temperatura = float(os.getenv("TEMPERATURA_IA", 0.7))

    model = genai.GenerativeModel(
        model_name="gemini-2.5-flash-lite",
        system_instruction=instruccion_sistema,
        generation_config=genai.types.GenerationConfig(
            temperature=temperatura
        )
    )

    chat = model.start_chat(history=history_gemini)
    response = chat.send_message(mensaje)
    return response.text
