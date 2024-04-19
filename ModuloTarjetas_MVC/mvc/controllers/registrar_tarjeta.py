import web
from mvc.models.modelo_registro import ModeloRegistro
from mvc.models.modelo_login import ModeloLogin

render = web.template.render('mvc/views/', base="layout")

class RegistrarTarjeta:
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
            
            # Aquí deberías obtener los detalles del padre según tus requerimientos
            # Por ejemplo, podrías obtener el ID del padre de la URL o de los parámetros de la solicitud
            params = web.input()
            id_padre = params.get('id_padre')  # Ajusta esto según cómo estés pasando el ID del padre

            # Crear una instancia del modelo para obtener los detalles del padre
            modelo_registro = ModeloRegistro()

            # Obtener los detalles del padre de la base de datos MySQL
            padre = modelo_registro.obtener_padre_por_id(id_padre)

            # Renderizar la plantilla HTML y pasar los detalles del padre como contexto
            return render.registrar_tarjeta(padre=padre)
        except Exception as e:
            print(f"Error en RegistrarTarjeta GET: {e}")
            return "Upsi algo salió mal."

    def POST(self):
        try:
            # Obtener los datos del formulario
            data = web.input()

            # Obtener los detalles del padre desde los datos del formulario o de donde sea que los obtengas
            # Aquí asumiré que tienes el ID del padre en los datos del formulario
            id_padre = data.id_padre

            # Crear una instancia del modelo para registrar la tarjeta
            modelo_registro = ModeloRegistro()

            # Obtener los detalles del padre de la base de datos MySQL
            padre = modelo_registro.obtener_padre_por_id(id_padre)

            # Registrar la tarjeta en la base de datos MongoDB
            exito = modelo_registro.registrar_tarjeta(padre['nombre'], padre['primer_apellido'], padre['segundo_apellido'], padre['correo_electronico'], data.numero_tarjeta)

            if exito:
                # Redireccionar a alguna página de éxito o a donde necesites
                return web.seeother('/lista_padres')
            else:
                return "Error al registrar la tarjeta."
        except Exception as e:
            print(f"Error en RegistrarTarjeta POST: {e}")
            return "Upsi algo salió mal."
