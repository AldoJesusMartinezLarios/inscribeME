import web
from mvc.models.modelo_registro import ModeloRegistro
from mvc.models.modelo_login import ModeloLogin
import requests  # Importa la librería requests para realizar solicitudes HTTP

render = web.template.render('mvc/views/', base="layout")

class ListarPadres:
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
            
            # Crear una instancia del modelo
            modelo_padres = ModeloRegistro()

            # Obtener el número total de padres
            total_padres = modelo_padres.obtener_total_padres()
            padres_por_pagina = 5

            # Calcular el número total de páginas
            total_paginas = (total_padres + padres_por_pagina - 1) // padres_por_pagina

            # Obtener el número de página actual de la URL
            parametros = web.input(pagina=1)
            pagina_actual = int(parametros.pagina)

            # Calcular el offset para la paginación
            offset = (pagina_actual - 1) * padres_por_pagina

            # Obtener la lista de padres para la página actual
            padres = modelo_padres.listar_padres(offset=offset, limite=padres_por_pagina)

            # Realizar una solicitud a tu API para obtener la lista de correos de padres con tarjeta asignada
            response = requests.get('https://fastapi-mongodb-b1r3.onrender.com/obtener_correos_padres')
            if response.status_code == 200:
                # Si la solicitud fue exitosa, obtener la lista de correos de padres del cuerpo de la respuesta
                correos_padres_con_tarjeta = response.json().get('correos_padres', [])
            else:
                # Si la solicitud falló, asignar una lista vacía
                correos_padres_con_tarjeta = []

            # Renderizar la plantilla HTML y pasar los datos como contexto
            return render.lista_padres(padres=padres, pagina_actual=pagina_actual, total_paginas=total_paginas, correos_padres_con_tarjeta=correos_padres_con_tarjeta)
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print(f"Error en ListarPadres GET: {e}")
            return "Ups! Algo salió mal."
