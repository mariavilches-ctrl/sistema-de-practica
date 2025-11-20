
CREATE TABLE Carrera (
    idCarrera INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    directorCarrera VARCHAR(150) NOT NULL,
    facultad VARCHAR(200) NOT NULL
);


CREATE TABLE Estudiante (
    rut VARCHAR(12) PRIMARY KEY,
    nombreCompleto VARCHAR(200) NOT NULL,
    anoLectivo VARCHAR(10),
    domicilio VARCHAR(50),
    telefono VARCHAR(12),
    correoInstitucional VARCHAR(100) NOT NULL,
    idCarrera INT NOT NULL              -- FK a Carrera
);

