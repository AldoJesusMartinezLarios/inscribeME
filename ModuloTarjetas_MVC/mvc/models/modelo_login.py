import MySQLdb
import hashlib

class ModeloLogin:
    def __init__(self):
        self.db = MySQLdb.connect(
            host="b14vky9o2hkkpprffyye-mysql.services.clever-cloud.com",
            user="ulb6pebtftpox0ww",
            passwd="N4oYjXMmHw9Dq9BDFb6X",
            db="b14vky9o2hkkpprffyye"
        )
        self.cursor = self.db.cursor()

    def validate_user(self, username, password):
        query = "SELECT password, token FROM usuarios WHERE username = %s"
        self.cursor.execute(query, (username,))
        result = self.cursor.fetchone()
        if result:
            stored_password, token = result
            hashed_password = hashlib.md5(password.encode()).hexdigest()  # Calcula el hash MD5 de la contraseña proporcionada
            if stored_password == hashed_password:
                return token  # Devuelve el token si la contraseña coincide
        return None  # Devuelve None si las credenciales son incorrectas

    def get_username_by_token(self, token):
        query = "SELECT username FROM usuarios WHERE token = %s"
        self.cursor.execute(query, (token,))
        result = self.cursor.fetchone()
        if result:
            return result[0]  # Devolver el nombre de usuario asociado al token
        return None  # Devolver None si el token no está asociado a ningún usuario