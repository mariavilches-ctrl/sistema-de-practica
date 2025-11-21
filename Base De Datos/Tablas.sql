
CREATE TABLE Carrera (
    idCarrera INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    directorCarrera VARCHAR(150) NOT NULL,
    facultad VARCHAR(200) NOT NULL
);


CREATE TABLE Estudiante (
    idEstudiante INT IDENTITY(1,1) PRIMARY KEY,
    rut VARCHAR(12) NOT NULL,
    nombreCompleto VARCHAR(200) NOT NULL,
    anoLectivo VARCHAR(10),
    domicilio VARCHAR(50),
    telefono VARCHAR(12),
    correoInstitucional VARCHAR(100) NOT NULL,
    idCarrera INT NOT NULL              -- FK a Carrera
);


CREATE TABLE CentroPractica (
    idCentroPractica INT IDENTITY(1,1) PRIMARY KEY,
    rutEmpresa VARCHAR(12) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(500),
    habilidadesEsperadas VARCHAR(MAX),
    direccion VARCHAR(250) NOT NULL,
);


CREATE TABLE Tutor (
    idTutor INT IDENTITY(1,1) PRIMARY KEY,
    rut VARCHAR(12) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(200) NOT NULL,
    telefono VARCHAR(12),
    idCarrera VARCHAR(50) NOT NULL --FK a Carrera
);

CREATE TABLE Supervisor (
    idSupervisor INT IDENTITY(1,1) PRIMARY KEY,
    rut VARCHAR(12) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(12) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    idCentroPractica INT NOT NULL         --FK a CentroPractica
);




