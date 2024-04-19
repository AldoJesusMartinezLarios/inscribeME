import web
import hashlib
from mvc.models.modelo_registro import ModeloRegistro
from mvc.models.modelo_login import ModeloLogin

render = web.template.render('mvc/views/', base="layout")

class ActualizarPadre:
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
            
            data = web.input(id_alumno=None)
            id_alumno = data.id_alumno

            if id_alumno:
                # Crear una instancia del modelo
                modelo_registro = ModeloRegistro()

                # Obtener el ID del padre asociado al alumno
                id_padre = modelo_registro.obtener_id_padre_por_id_alumno(id_alumno)

                if id_padre:
                    # Obtener los datos del padre por su ID
                    padre = modelo_registro.obtener_padre_por_id(id_padre)

                    if padre:
                        return render.actualizar_padre(padre=padre)
                    else:
                        return "Padre no encontrado"
                else:
                    return "El alumno no tiene un padre asignado"
            else:
                return "ID del alumno no especificado"
        except Exception as e:
            print(f"Error en ActualizarPadre GET: {e}")
            return "Oops! Algo salió mal."


    def POST(self):
        try:
            data = web.input(id_padre=None, nombre=None, primer_apellido=None, segundo_apellido=None, correo=None, telefono=None, contraseña=None)

            # Obtener los datos del formulario
            id_padre = data.id_padre
            nombre = data.nombre
            primer_apellido = data.primer_apellido
            segundo_apellido = data.segundo_apellido
            correo = data.correo
            telefono = data.telefono
            # Encriptar la contraseña usando MD5
            contraseña = hashlib.md5(data.contraseña.encode()).hexdigest()

            # Crear una instancia del modelo
            modelo_registro = ModeloRegistro()

            # Actualizar los datos del padre en la base de datos
            exito = modelo_registro.actualizar_padre(id_padre, nombre, primer_apellido, segundo_apellido, correo, telefono, contraseña)
            
            if exito:
                # Redireccionar a una página de éxito o a donde necesites
                return web.seeother('/lista_alumnos')
            else:
                return "Error al actualizar el padre."
        except Exception as e:
            print(f"Error en ActualizarPadre POST: {e}")
            return "Oops! Algo salió mal."
