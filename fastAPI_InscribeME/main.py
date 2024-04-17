import fastapi
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from fastapi import Depends, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import hashlib
import mysql.connector
import jwt
from jwt import decode
import secrets

app = fastapi.FastAPI()

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

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="token")

SECRET_KEY = secrets.token_urlsafe(32)

# Configura la conexión a la base de datos MySQL
db_config = {
    'host': 'b14vky9o2hkkpprffyye-mysql.services.clever-cloud.com',
    'user': 'ulb6pebtftpox0ww',
    'password': 'N4oYjXMmHw9Dq9BDFb6X',
    'database': 'b14vky9o2hkkpprffyye',
}

# Función para obtener una nueva conexión a la base de datos MySQL
def get_connection():
    return mysql.connector.connect(**db_config)

# Función para calcular el hash MD5 de una cadena
def md5_hash(text):
    return hashlib.md5(text.encode()).hexdigest()

# Modelo de datos para la autenticación de los padres
class PadreLogin(BaseModel):
    correo_electronico: str
    contraseña: str

# Modelo de datos para la respuesta de la calificación
class Calificacion(BaseModel):
    nombre_completo_alumno: str
    calificacion: float

# Función para verificar las credenciales de un padre
def authenticate_padre(correo_electronico: str, contraseña: str, conn):
    c = conn.cursor()
    c.execute('SELECT id_padre, nombre, correo_electronico, contraseña FROM padres WHERE correo_electronico = %s AND contraseña = %s', (correo_electronico, md5_hash(contraseña)))
    padre = c.fetchone()
    if padre:
        return padre
    return None

# Ruta para que los padres obtengan un token JWT
@app.post("/token")
async def login_for_access_token(form_data: OAuth2PasswordRequestForm = Depends(), conn=Depends(get_connection)):
    padre = authenticate_padre(form_data.username, form_data.password, conn)
    if not padre:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="correo_electronico o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    # Crea el payload con el id_padre
    payload = {"id_padre": padre[0]}
    
    # Genera el token JWT firmado con la SECRET_KEY
    token = jwt.encode(payload, SECRET_KEY, algorithm="HS256")
    
    return {"access_token": token, "token_type": "bearer"}

# Ruta protegida para que los padres obtengan la calificación de su hijo
@app.get("/calificacion", response_model=Calificacion)
async def get_calificacion(token: str = Depends(oauth2_scheme), conn=Depends(get_connection)):
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=["HS256"])
        id_padre = payload["id_padre"]  # Obtener el valor directamente del payload
    except jwt.exceptions.DecodeError:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Token inválido")

    c = conn.cursor()
    c.execute('SELECT alumnos.nombre_completo, alumnos.calificacion FROM alumnos JOIN padres ON alumnos.id_alumno = padres.id_alumno WHERE padres.id_padre = %s', (id_padre,))
    calificacion = c.fetchone()
    if not calificacion:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="No se encontró la calificación del alumno asociado al padre")
    return {"nombre_completo_alumno": calificacion[0], "calificacion": calificacion[1]}