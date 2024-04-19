"""Framework web.py """
import web

# Rutas de los controladores
urls = (
    '/', 'mvc.controllers.login.Login',
    '/lista_alumnos', 'mvc.controllers.lista_alumnos.Listar',
    '/asignar_padre', 'mvc.controllers.asignar_padre.AsignarPadre',
    '/detalle', 'mvc.controllers.detalle_padre.DetallePadre', 
    '/actualizar_padre', 'mvc.controllers.actualizar_padre.ActualizarPadre',
    '/lista_padres', 'mvc.controllers.lista_padres.ListarPadres',
    '/padres/registrar_tarjeta', 'mvc.controllers.registrar_tarjeta.RegistrarTarjeta',
    '/padres/actualizar_tarjeta', 'mvc.controllers.actualizar_tarjeta.ActualizarTarjeta',
    '/logout', 'mvc.controllers.logout.Logout'
)


app = web.application(urls, globals())

# Punto de entrada
if __name__ == "__main__":
    web.config.debug = True
    app.run()