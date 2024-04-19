import web
import os
import hashlib
from mvc.models.modelo_registro import ModeloRegistro
from mvc.models.modelo_login import ModeloLogin

render = web.template.render('mvc/views/', base="layout")

class Listar:
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
            modelo_alumnos = ModeloRegistro()

            # Obtener el número total de alumnos
            total_alumnos = modelo_alumnos.obtener_total_alumnos()
            alumnos_por_pagina = 5

            # Calcular el número total de páginas
            total_paginas = (total_alumnos + alumnos_por_pagina - 1) // alumnos_por_pagina

            # Obtener el número de página actual de la URL
            parametros = web.input(pagina=1)
            pagina_actual = int(parametros.pagina)

            # Calcular el offset para la paginación
            offset = (pagina_actual - 1) * alumnos_por_pagina

            # Obtener la lista de alumnos para la página actual
            alumnos = modelo_alumnos.listar_alumnos(offset=offset, limite=alumnos_por_pagina)

            # Obtener información sobre si cada alumno tiene un padre asignado
            padres_asignados = {}
            for alumno in alumnos:
                padre_asignado = modelo_alumnos.obtener_padre_asignado(alumno['id_aspirante'])
                padres_asignados[alumno['id_aspirante']] = padre_asignado is not None

            # Calcular el hash del ID de cada alumno
            hash_alumnos = [hashlib.sha256(str(alumno['id_aspirante']).encode()).hexdigest() for alumno in alumnos]

            # Renderizar la plantilla HTML y pasar los datos como contexto
            return render.lista_alumnos(alumnos=alumnos, pagina_actual=pagina_actual, total_paginas=total_paginas, hash_alumnos=hash_alumnos, padres_asignados=padres_asignados)
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print(f"Error en Listar GET: {e}")
            return "Upsi algo salió mal"
