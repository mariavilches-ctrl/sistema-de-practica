
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


CREATE TABLE Practica (
    idPractica INT IDENTITY(1,1) PRIMARY KEY,
    idEstudiante INT NOT NULL,   -- FK a Estudiante
    idCentroPractica INT NOT NULL,        -- FK a CentroPractica
    idTutor INT NOT NULL,            -- FK a Tutor
    idSupervisor INT NOT NULL,       -- FK a Supervisor
    tipo VARCHAR(30) NOT NULL,             --FK a inicial, intermedia o profesional
    fechaDeInicio DATETIME DEFAULT GETDATE(),
    fechaDeTermino DATETIME,
    actividades VARCHAR(500) NOT NULL,
    evidenciaImg VARCHAR(MAX) NOT NULL    
);


CREATE TABLE Bitacora (
    idBitacora INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    rutEstudiante VARCHAR(12) NOT NULL,   -- FK a Estudiante 
    habilidadesDesarrolladas VARCHAR(500) NOT NULL,
    desafios VARCHAR(500) NOT NULL,
    logros VARCHAR(500) NOT NULL,
    fechaRegistro DATETIME DEFAULT GETDATE()
);


CREATE TABLE InformeSupervisor (
    idInformeS INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    idSupervisor VARCHAR(12) NOT NULL,   -- FK a Supervisor
    enlaceInformeS VARCHAR(MAX) NOT NULL    -- URL de archivo 
);


CREATE TABLE InformeTutor (
    idInformeT INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    idTutor VARCHAR(12) NOT NULL,        -- FK a Tutor
    enlaceInformeT VARCHAR(MAX) NOT NULL  --URL de informe
);


CREATE TABLE CompetenciaDesarrollo (
    idCompeDes INT IDENTITY(1,1) PRIMARY KEY,
    categoria VARCHAR(30) NOT NULL       
);



CREATE TABLE CompetenciaPractica (
    idCompePra INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    idCompeDes INT NOT NULL,              -- FK a CompetenciaDesarrollo
    nivel VARCHAR(50),                    -- Basico, Intermedio o Avanzado
    fechaRegistro DATETIME DEFAULT GETDATE()
);





