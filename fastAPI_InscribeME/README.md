# FastAPI para la aplicación InscribeME
Mysql, FastAPI

API para que el padre del alumno pueda visualizar las calificaciones de su hijo.

gunicorn -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8000 main:app

