import web
from mvc.models.modelo_login import ModeloLogin

render = web.template.render('mvc/views/', base='layout')
m_login = ModeloLogin()

class Login:
    def GET(self):
        try:
            return render.login(incorrecto=False)  # Pasamos la bandera incorrecto como False por defecto
        except Exception as e:
            print(f"Error: {e}")
            return "Lo siento, algo salió mal."

    def POST(self):
        try:
            form = web.input()
            username = form.username
            password = form.password

            print(f"Username: {username}")
            print(f"Password: {password}")

            token = m_login.validate_user(username, password)
            if token:
                # Convertir el token en sesión
                web.setcookie('token', token, expires=3600)  # Configura la cookie para que expire en una hora
                print(f"Token: {token}")
                raise web.seeother('/lista_alumnos')
            else:
                return render.login(incorrecto=True)  # Pasamos la bandera incorrecto como True
        except Exception as e:
            print(f"Error: {e}")
            return "Lo siento, algo salió mal."
