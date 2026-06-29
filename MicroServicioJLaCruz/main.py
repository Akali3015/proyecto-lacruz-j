from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.controllers.chat_controller import router as chat_router

app = FastAPI(title="MicroServicio J. LACRUZ C.A.", version="1.0")

# Configurar CORS para permitir peticiones desde el localhost de PHP
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Incluir las rutas
app.include_router(chat_router, prefix="/api")

@app.get("/")
def read_root():
    return {"mensaje": "Microservicio de ChatBot J. LACRUZ C.A. está en línea"}
