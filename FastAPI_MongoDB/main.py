from fastapi import FastAPI, HTTPException
from fastapi.responses import JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
from pydantic import BaseModel, Field
from pytz import timezone
from bson import ObjectId
from datetime import datetime
from dotenv import load_dotenv
import os
import pytz  # Importar pytz para obtener la zona horaria
from typing import Optional, List  # Importar Optional desde typing
from datetime import datetime, timedelta, date


# Cargar variables de entorno desde el archivo .env
load_dotenv()

# Crear la instancia de la aplicación FastAPI
app = FastAPI()

origins = [
    "*"
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Definir la zona horaria de México
ZONA_HORARIA = pytz.timezone("America/Mexico_City")

# Obtener la fecha de hoy en la zona horaria de Ciudad de México
fecha_hoy_mexico = datetime.combine(datetime.now(ZONA_HORARIA).date(), datetime.min.time())


# Definir el modelo de datos para Entradas
class EntradaSalida(BaseModel):
    codigo_tarjeta: str = Field(..., title="Código de tarjeta")
    momento: Optional[datetime] = None
    lector: str = Field(..., title="Tipo de lector")

# Definir el modelo de datos para Registrar Tarjeta
class RegistrarTarjeta(BaseModel):
    nombre_padre: str = Field(..., title="Nombre del padre")
    primer_apellido_padre: str = Field(..., title="Primer apellido del padre")
    segundo_apellido_padre: str = Field(..., title="Segundo apellido del padre")
    correo_padre: str = Field(..., title="Correo del padre")
    codigo_tarjeta: str = Field(..., title="Código de tarjeta")

# Definir un modelo de datos para la respuesta de la API
class CorreosPadresResponse(BaseModel):
    correos_padres: list[str]

class RFIDData(BaseModel):
    valorRFID: str

# Definir un modelo de datos para la respuesta de entradas y salidas
class EntradasSalidasResponse(BaseModel):
    entradas: List[EntradaSalida]
    salidas: List[EntradaSalida]

last_rfid_data = RFIDData(valorRFID="")

@app.get("/v1/leer_nueva_tarjeta")
async def leer_nueva_tarjeta():
    return last_rfid_data

@app.post("/v1/actualizar_valor_rfid")
async def actualizar_valor_rfid(rfid_data: RFIDData):
    global last_rfid_data
    last_rfid_data = rfid_data
    return {"mensaje": "Valor RFID actualizado correctamente"}

# MongoDB connection URL
MONGO_URL = os.getenv("MONGODB_URI")
client = AsyncIOMotorClient(MONGO_URL)
database = client["inscribeme"]
collection_alumnos_padres = database["alumnos_y_padres"]
collection_entradas = database["entradas"]
collection_salidas = database["salidas"]

# Operaciones CRUD para Alumnos y Padres

# Obtener todos los registros de alumnos y padres
@app.get("/obtener_registros/", response_model=list[RegistrarTarjeta])
async def get_alumnos_padres():
    alumnos_padres = await collection_alumnos_padres.find().to_list(None)
    return alumnos_padres

# Ruta para obtener la lista de correos electrónicos de los padres que ya tienen tarjeta asignada
@app.get("/obtener_correos_padres", response_model=CorreosPadresResponse)
async def obtener_correos_padres():
    correos_padres = []
    padres_con_tarjeta = await collection_alumnos_padres.distinct("correo_padre", {"codigo_tarjeta": {"$exists": True}})
    correos_padres.extend(padres_con_tarjeta)
    return {"correos_padres": correos_padres}

# Obtener un registro específico por su ID
@app.get("/padres/{id_padre}", response_model=RegistrarTarjeta)
async def get_alumno_padre(id_padre: str):
    alumno_padre = await collection_alumnos_padres.find_one({"_id": ObjectId(id_padre)})
    if alumno_padre:
        return alumno_padre
    else:
        raise HTTPException(status_code=404, detail="Alumno y padre no encontrado")

# Crear un nuevo registro de alumno y padre
@app.post("/alumnos_padres/", response_model=RegistrarTarjeta)
async def create_alumno_padre(data: RegistrarTarjeta):
    alumno_padre_data = data.dict()
    result = await collection_alumnos_padres.insert_one(alumno_padre_data)
    alumno_padre_data["_id"] = result.inserted_id
    return alumno_padre_data

# Actualizar un registro de alumno y padre existente por correo electrónico
@app.put("/alumnos_padres/{correo_padre}", response_model=RegistrarTarjeta)
async def update_alumno_padre(correo_padre: str, data: RegistrarTarjeta):
    alumno_padre_data = data.dict()
    # Buscar el documento con el correo electrónico proporcionado
    result = await collection_alumnos_padres.update_one({"correo_padre": correo_padre}, {"$set": alumno_padre_data})
    if result.modified_count == 1:
        # Si se actualiza correctamente, devolver los datos actualizados
        alumno_padre_data["correo_padre"] = correo_padre  # Actualizar el correo electrónico en los datos devueltos
        return alumno_padre_data
    else:
        # Si no se encuentra el documento, devolver un error
        raise HTTPException(status_code=404, detail="Alumno y padre no encontrado")

# Eliminar un registro de alumno y padre
@app.delete("/alumnos_padres/{id_padre}", response_model=dict)
async def delete_alumno_padre(id_padre: str):
    result = await collection_alumnos_padres.delete_one({"_id": ObjectId(id_padre)})
    if result.deleted_count == 1:
        return {"mensaje": "Alumno y padre eliminado correctamente"}
    else:
        raise HTTPException(status_code=404, detail="Alumno y padre no encontrado")

# Operación para registrar tanto entradas como salidas

# Registrar una entrada o salida
@app.post("/registro/entrada_salida/")
async def registrar_entrada_salida(data: EntradaSalida):
    entrada_salida_data = data.dict()

    # Extraer la fecha del momento y convertirla en datetime
    fecha_momento = entrada_salida_data["momento"].replace(hour=0, minute=0, second=0, microsecond=0)

    # Verificar si ya existe una entrada o salida para el mismo código de tarjeta en la misma fecha
    codigo_tarjeta = entrada_salida_data["codigo_tarjeta"]
    if entrada_salida_data["lector"] == "entrada":
        existing_entry = await collection_entradas.find_one({
            "codigo_tarjeta": codigo_tarjeta,
            "momento": {"$gte": fecha_momento, "$lt": fecha_momento + timedelta(days=1)}
        })
        if existing_entry:
            return {"mensaje": "Ya existe una entrada para este código de tarjeta en esta fecha"}
    elif entrada_salida_data["lector"] == "salida":
        existing_exit = await collection_salidas.find_one({
            "codigo_tarjeta": codigo_tarjeta,
            "momento": {"$gte": fecha_momento, "$lt": fecha_momento + timedelta(days=1)}
        })
        if existing_exit:
            return {"mensaje": "Ya existe una salida para este código de tarjeta en esta fecha"}

    # Si no existe, insertar el registro en la colección correspondiente según el lector
    if entrada_salida_data["lector"] == "entrada":
        result = await collection_entradas.insert_one(entrada_salida_data)
    elif entrada_salida_data["lector"] == "salida":
        result = await collection_salidas.insert_one(entrada_salida_data)

    return {"mensaje": "Entrada o salida registrada correctamente"}


    # Si no existe, insertar el registro en la colección correspondiente según el lector
    if entrada_salida_data["lector"] == "entrada":
        result = await collection_entradas.insert_one(entrada_salida_data)
    elif entrada_salida_data["lector"] == "salida":
        result = await collection_salidas.insert_one(entrada_salida_data)

    return {"mensaje": "Entrada o salida registrada correctamente"}


# Operaciones para registrar tarjetas de padres

# Registrar una nueva tarjeta para un padre
@app.post("/registrar_tarjeta/", response_model=dict)
async def registrar_tarjeta(data: RegistrarTarjeta):
    tarjeta_data = data.dict()
    result = await collection_alumnos_padres.insert_one(tarjeta_data)
    if result.inserted_id:
        return {"mensaje": "Tarjeta registrada correctamente"}
    else:
        raise HTTPException(status_code=500, detail="Error al registrar la tarjeta")

# Obtener código de tarjeta asociado con el correo electrónico del padre
async def obtener_codigo_tarjeta(correo_padre: str):
    padre = await collection_alumnos_padres.find_one({"correo_padre": correo_padre})
    if padre:
        return padre.get("codigo_tarjeta")
    else:
        return None

# Obtener entradas asociadas con un correo electrónico de padre
@app.get("/entradas/{correo_padre}", response_model=list[EntradaSalida])
async def get_entradas_por_correo_padre(correo_padre: str):
    codigo_tarjeta = await obtener_codigo_tarjeta(correo_padre)
    if codigo_tarjeta:
        entradas = await collection_entradas.find({"codigo_tarjeta": codigo_tarjeta}).to_list(None)
        return entradas
    else:
        raise HTTPException(status_code=404, detail="Correo electrónico no asociado a ningún padre")

# Obtener salidas asociadas con un correo electrónico de padre
@app.get("/salidas/{correo_padre}", response_model=list[EntradaSalida])
async def get_entradas_por_correo_padre(correo_padre: str):
    codigo_tarjeta = await obtener_codigo_tarjeta(correo_padre)
    if codigo_tarjeta:
        salidas = await collection_salidas.find({"codigo_tarjeta": codigo_tarjeta}).to_list(None)
        return salidas
    else:
        raise HTTPException(status_code=404, detail="Correo electrónico no asociado a ningún padre")


# Obtener entradas del día de hoy asociadas con un correo electrónico de padre
@app.get("/entradas_hoy/{correo_padre}", response_model=list[EntradaSalida])
async def get_entradas_hoy_por_correo_padre(correo_padre: str, zona_horaria: str = "America/Mexico_City"):
    codigo_tarjeta = await obtener_codigo_tarjeta(correo_padre)
    if codigo_tarjeta:
        # Obtener la fecha de hoy en la zona horaria especificada
        fecha_hoy_mexico = datetime.combine(datetime.now(pytz.timezone(zona_horaria)).date(), datetime.min.time())

        # Buscar las entradas asociadas con el código de tarjeta y la fecha de hoy
        entradas_hoy = await collection_entradas.find({
            "codigo_tarjeta": codigo_tarjeta,
            "momento": {"$gte": fecha_hoy_mexico, "$lt": fecha_hoy_mexico + timedelta(days=1)}
        }).to_list(None)

        if entradas_hoy:
            return entradas_hoy
        else:
            raise HTTPException(status_code=404, detail="Su hijo no ha entrado hoy a la institución")
    else:
        raise HTTPException(status_code=404, detail="Correo electrónico no asociado a ningún padre")


# Obtener salidas del día de hoy asociadas con un correo electrónico de padre
@app.get("/salidas_hoy/{correo_padre}", response_model=list[EntradaSalida])
async def get_salidas_hoy_por_correo_padre(correo_padre: str, zona_horaria: str = "America/Mexico_City"):
    codigo_tarjeta = await obtener_codigo_tarjeta(correo_padre)
    if codigo_tarjeta:
        # Obtener la fecha de hoy en la zona horaria especificada
        fecha_hoy_mexico = datetime.combine(datetime.now(pytz.timezone(zona_horaria)).date(), datetime.min.time())

        # Buscar las salidas asociadas con el código de tarjeta y la fecha de hoy
        salidas_hoy = await collection_salidas.find({
            "codigo_tarjeta": codigo_tarjeta,
            "momento": {"$gte": fecha_hoy_mexico, "$lt": fecha_hoy_mexico + timedelta(days=1)}
        }).to_list(None)

        if salidas_hoy:
            return salidas_hoy
        else:
            raise HTTPException(status_code=404, detail="Sin salida por el momento")
    else:
        raise HTTPException(status_code=404, detail="Correo electrónico no asociado a ningún padre")

# Obtener todas las entradas y salidas asociadas con un correo electrónico de padre
@app.get("/entradas_salidas/{correo_padre}", response_model=EntradasSalidasResponse)
async def get_entradas_salidas_por_correo_padre(correo_padre: str):
    codigo_tarjeta = await obtener_codigo_tarjeta(correo_padre)
    if codigo_tarjeta:
        # Calcula la fecha actual y la fecha hace 7 días
        fecha_actual = datetime.now()
        fecha_limite = fecha_actual - timedelta(days=8)

        # Consulta las entradas dentro del rango de fechas
        entradas_cursor = collection_entradas.find({
            "codigo_tarjeta": codigo_tarjeta,
            "momento": {"$gte": fecha_limite, "$lte": fecha_actual}
        })

        # Consulta las salidas dentro del rango de fechas
        salidas_cursor = collection_salidas.find({
            "codigo_tarjeta": codigo_tarjeta,
            "momento": {"$gte": fecha_limite, "$lte": fecha_actual}
        })

        # Convertir los resultados de la consulta a listas de Python
        entradas = [
            {"codigo_tarjeta": entrada["codigo_tarjeta"],
             "momento": entrada["momento"].isoformat(),
             "lector": entrada["lector"]} 
            for entrada in await entradas_cursor.to_list(None)
        ]

        salidas = [
            {"codigo_tarjeta": salida["codigo_tarjeta"],
             "momento": salida["momento"].isoformat(),
             "lector": salida["lector"]} 
            for salida in await salidas_cursor.to_list(None)
        ]

        return EntradasSalidasResponse(entradas=entradas, salidas=salidas)
    else:
        raise HTTPException(status_code=404, detail="Correo electrónico no asociado a ningún padre")


# Manejar excepciones de tipo general
@app.exception_handler(Exception)
async def exception_handler(request, exc):
    return JSONResponse(status_code=500, content={"message": "Error interno del servidor"})
