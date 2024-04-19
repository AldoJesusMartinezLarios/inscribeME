import web
import hashlib
from mvc.models.modelo_registro import ModeloRegistro
from mvc.models.modelo_login import ModeloLogin

render = web.template.render('mvc/views/', base="layout")

class AsignarPadre:
    def GET(self):
        try:
            # Verificar token de usuario
            token_cookie = web.cookies().get('token')
            if not token_cookie:
                raise web.seeother('/')  # Redirigir a la página de inicio de sesión si no hay token en las cookies

            # Obtener username asociado al token
            m_login = ModeloLogin()
            username = m_login.get_username_by_token(token_cookie)
            if not username:
                raise web.seeother('/')  # Redirigir a la página de inicio de sesión si el token no es válido
            
            # Obtener el ID del alumno de la URL
            params = web.input()
            id_alumno = params.get('id_alumno')

            # Renderizar la plantilla y pasar el ID del alumno como contexto
            return render.asignar_padre(id_aspirante=id_alumno)
        except Exception as e:
            print(f"Error en AsignarPadre GET: {e}")
            return "Oops! Algo salió mal."


    def POST(self):
        try:
            data = web.input(id_aspirante=None, nombre=None, primer_apellido=None, segundo_apellido=None, correo=None, telefono=None, contraseña=None)

            # Obtener los datos del formulario
            id_aspirante = data.id_aspirante
            nombre = data.nombre
            primer_apellido = data.primer_apellido
            segundo_apellido = data.segundo_apellido
            correo = data.correo
            telefono = data.telefono
            # Encriptar la contraseña usando MD5
            contraseña = hashlib.md5(data.contraseña.encode()).hexdigest()

            # Crear una instancia del modelo
            modelo_registro = ModeloRegistro()

            # Asignar el padre al alumno
            exito = modelo_registro.asignar_padre(id_aspirante, nombre, primer_apellido, segundo_apellido, correo, telefono, contraseña)
            
            if exito:
                # Redireccionar a una página de éxito o a donde necesites
                return web.seeother('/lista_alumnos')
            else:
                return "Error al asignar el padre."
        except Exception as e:
            print(f"Error en AsignarPadre POST: {e}")
            return "Oops! Algo salió mal."
