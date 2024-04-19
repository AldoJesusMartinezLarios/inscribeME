import MySQLdb
import requests

class ModeloRegistro:
    def __init__(self):
        self.db = MySQLdb.connect(host="b14vky9o2hkkpprffyye-mysql.services.clever-cloud.com",
                                  user="ulb6pebtftpox0ww",
                                  passwd="N4oYjXMmHw9Dq9BDFb6X",
                                  db="b14vky9o2hkkpprffyye")
        self.cursor = self.db.cursor()

    def listar_alumnos(self, offset, limite):
        try:
            consulta_sql = "SELECT * FROM alumnos LIMIT %s OFFSET %s"
            self.cursor.execute(consulta_sql, (limite, offset))
            alumnos = self.cursor.fetchall()

            alumnos_con_atributos = []
            for alumno in alumnos:
                alumno_con_atributos = {
                    'id_aspirante': alumno[0],
                    'nombre': alumno[1],
                    'primer_apellido': alumno[2],
                    'segundo_apellido': alumno[3]
                }
                alumnos_con_atributos.append(alumno_con_atributos)
            return alumnos_con_atributos
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en listar_alumnos:", e)
            return []

    def obtener_total_alumnos(self):
        try:
            self.cursor.execute("SELECT COUNT(*) FROM alumnos")
            total_alumnos = self.cursor.fetchone()[0]
            return total_alumnos
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en obtener_total_alumnos:", e)
            return 0
    
    def obtener_padre_asignado(self, id_aspirante):
        try:
            consulta_sql = "SELECT * FROM padres WHERE id_aspirante = %s"
            self.cursor.execute(consulta_sql, (id_aspirante,))
            padre = self.cursor.fetchone()
            if padre:
                return {
                    'id_padre': padre[0],
                    'nombre': padre[2],
                    'primer_apellido': padre[3],
                    'segundo_apellido': padre[4],
                    'correo_electronico': padre[5],
                    'telefono': padre[6],
                    'contraseña': padre[7]
                }
            else:
                return None
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en obtener_padre_asignado:", e)
            return None


    def asignar_padre(self, id_aspirante, nombre, primer_apellido, segundo_apellido, correo_electronico, telefono, contraseña):
        try:
            consulta_sql = "INSERT INTO padres (id_aspirante, nombre, primer_apellido, segundo_apellido, correo_electronico, telefono, contraseña) VALUES (%s, %s, %s, %s, %s, %s, %s)"
            self.cursor.execute(consulta_sql, (id_aspirante, nombre, primer_apellido, segundo_apellido, correo_electronico, telefono, contraseña))
            self.db.commit()
            return True
        except Exception as e:
            print("Error en asignar_padre:", e)
            self.db.rollback()
            return False
        
        
    def obtener_id_padre_por_id_alumno(self, id_alumno):
        try:
            consulta_sql = "SELECT id_padre FROM padres WHERE id_aspirante = %s"
            self.cursor.execute(consulta_sql, (id_alumno,))
            resultado = self.cursor.fetchone()
            if resultado:
                return resultado[0]
            else:
                return None
        except Exception as e:
            print("Error en obtener_id_padre_por_id_alumno:", e)
            return None

    def actualizar_padre(self, id_padre, nombre, primer_apellido, segundo_apellido, correo_electronico, telefono, contraseña):
        try:
            consulta_sql = "UPDATE padres SET nombre = %s, primer_apellido = %s, segundo_apellido = %s, correo_electronico = %s, telefono = %s, contraseña = %s WHERE id_padre = %s"
            self.cursor.execute(consulta_sql, (nombre, primer_apellido, segundo_apellido, correo_electronico, telefono, contraseña, id_padre))
            self.db.commit()
            return True
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en actualizar_padre:", e)
            self.db.rollback()
            return False
        
    def obtener_padre_por_id(self, id_padre):
        try:
            consulta_sql = "SELECT * FROM padres WHERE id_padre = %s"
            self.cursor.execute(consulta_sql, (id_padre,))
            padre = self.cursor.fetchone()
            if padre:
                return {
                    'id_padre': padre[0],
                    'id_aspirante': padre[1],
                    'nombre': padre[2],
                    'primer_apellido': padre[3],
                    'segundo_apellido': padre[4],
                    'correo_electronico': padre[5],
                    'telefono': padre[6],
                    'contraseña': padre[7]
                }
            else:
                return None
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en obtener_padre_por_id:", e)
            return None

    def listar_padres(self, offset, limite):
        try:
            consulta_sql = "SELECT * FROM padres LIMIT %s OFFSET %s"
            self.cursor.execute(consulta_sql, (limite, offset))
            padres = self.cursor.fetchall()

            padres_con_atributos = []
            for padre in padres:
                padre_con_atributos = {
                    'id_padre': padre[0],
                    'nombre': padre[2],
                    'primer_apellido': padre[3],
                    'segundo_apellido': padre[4],
                    'correo_electronico': padre[5]
                }
                padres_con_atributos.append(padre_con_atributos)  # Agregar el padre_con_atributos a la lista
            return padres_con_atributos
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en listar_padres:", e)
            return []


    def obtener_total_padres(self):
        try:
            self.cursor.execute("SELECT COUNT(*) FROM padres")
            total_padres = self.cursor.fetchone()[0]
            return total_padres
        except Exception as e:
            # Manejar la excepción de manera adecuada
            print("Error en obtener_total_padres:", e)
            return 0

    def registrar_tarjeta(self, nombre, primer_apellido, segundo_apellido, correo, numero_tarjeta):
        try:
            # Crear un objeto de tipo Tarjeta para enviar al servidor
            tarjeta = {
                "nombre_padre": nombre,
                "primer_apellido_padre": primer_apellido,
                "segundo_apellido_padre": segundo_apellido,
                "correo_padre": correo,
                "codigo_tarjeta": numero_tarjeta
            }

            # Realizar una solicitud POST a la API de FastAPI
            response = requests.post("https://fastapi-mongodb-b1r3.onrender.com/registrar_tarjeta/", json=tarjeta)

            # Verificar si la solicitud fue exitosa (código de estado 200)
            if response.status_code == 200:
                return True
            else:
                return False
        except Exception as e:
            print("Error en registrar_tarjeta:", e)
            return False
    
    def actualizar_tarjeta(self, nombre, primer_apellido, segundo_apellido, correo, numero_tarjeta):
        try:
            # Crear un objeto de tipo Tarjeta para enviar al servidor
            tarjeta = {
                "nombre_padre": nombre,
                "primer_apellido_padre": primer_apellido,
                "segundo_apellido_padre": segundo_apellido,
                "correo_padre": correo,
                "codigo_tarjeta": numero_tarjeta
            }

            # Realizar una solicitud PUT a la API de FastAPI
            response = requests.put(f"https://fastapi-mongodb-b1r3.onrender.com/alumnos_padres/{correo}", json=tarjeta)

            # Verificar si la solicitud fue exitosa (código de estado 200)
            if response.status_code == 200:
                return True
            else:
                print(f"Error al actualizar tarjeta: {response.status_code} - {response.text}")
                return False
        except Exception as e:
            print("Error en actualizar_tarjeta:", e)
            return False
