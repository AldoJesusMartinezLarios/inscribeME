import web

class Logout:
    def GET(self):
        try:
            # Eliminar la cookie de token
            web.setcookie('token', '', expires=-1)
            # Otros pasos para limpiar cualquier otra información de sesión relacionada con el usuario
            # Por ejemplo, si estás utilizando sesiones en la base de datos, aquí podrías eliminar la sesión del usuario

            # Redirigir al usuario a la página de inicio de sesión
            raise web.seeother('/')
        except Exception as e:
            print(f"Error en Logout GET: {e}")
            return "Upsi algo salió mal"
