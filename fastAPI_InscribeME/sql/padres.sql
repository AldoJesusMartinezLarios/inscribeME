CREATE TABLE alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo varchar(50),
    calificacion FLOAT
);

CREATE TABLE padres (
    id_padre INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo varchar(50),
    email VARCHAR(50),
    contraseña TEXT,
    id_alumno INT,
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno)
);

INSERT INTO alumnos (nombre_completo, calificacion)
VALUES ("Juan Lopez", 8);

INSERT INTO alumnos (nombre_completo, calificacion)
VALUES ("Guillermo del Toro", 7);

INSERT INTO alumnos (nombre_completo, calificacion)
VALUES ("Robert Oppenheimer", 10);

INSERT INTO padres (nombre_completo, email, contraseña, id_alumno)
VALUES ("Padre de Juan", "padrejuan@gmail.com", "202cb962ac59075b964b07152d234b70", 1);

INSERT INTO padres (nombre_completo, email, contraseña, id_alumno)
VALUES ("Padre de Guillermo", "padreguillermo@gmail.com", "202cb962ac59075b964b07152d234b70", 2);

INSERT INTO padres (nombre_completo, email, contraseña, id_alumno)
VALUES ("Padre de Oppenheimer", "padreoppie@gmail.com", "202cb962ac59075b964b07152d234b70", 3);
