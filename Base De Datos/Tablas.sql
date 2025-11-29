
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
    direccion VARCHAR(250) NOT NULL
);


CREATE TABLE Tutor (
    idTutor INT IDENTITY(1,1) PRIMARY KEY,
    rut VARCHAR(12) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(200) NOT NULL,
    telefono VARCHAR(12),
    idCarrera INT NOT NULL --FK a Carrera
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
    idEstudiante INT NOT NULL,   -- FK a Estudiante 
    habilidadesDesarrolladas VARCHAR(500) NOT NULL,
    desafios VARCHAR(500) NOT NULL,
    logros VARCHAR(500) NOT NULL,
    fechaRegistro DATETIME DEFAULT GETDATE()
);


CREATE TABLE InformeSupervisor (
    idInformeS INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    idSupervisor INT NOT NULL,   -- FK a Supervisor
    enlaceInformeS VARCHAR(MAX) NOT NULL    -- URL de archivo 
);


CREATE TABLE InformeTutor (
    idInformeT INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    idTutor INT NOT NULL,        -- FK a Tutor
    enlaceInformeT VARCHAR(MAX) NOT NULL  --URL de informe
);


CREATE TABLE CompetenciaDesarrollo (
    idCompeDes INT IDENTITY(1,1) PRIMARY KEY,
    categoria VARCHAR(30) NOT NULL       --Tipo de competencia desarrollada (Técnica, Genérica, Blanda, Profesional)
);



CREATE TABLE CompetenciaPractica (
    idCompePra INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- FK a Practica
    idCompeDes INT NOT NULL,              -- FK a CompetenciaDesarrollo
    nivel VARCHAR(50),                    -- Basico, Intermedio o Avanzado
    fechaRegistro DATETIME DEFAULT GETDATE()
);


CREATE TABLE EvaluacionArea (
    idEvaluacion INT IDENTITY(1,1) PRIMARY KEY,
    idPractica INT NOT NULL,              -- Práctica evaluada
    idCompeDes INT NOT NULL,              -- Competencia evaluada
    puntaje INT NOT NULL,             -- Nivel de logro (ej: 1 a 5)
    comentario VARCHAR(500),              -- Observaciones de la evaluación
    fechaEvaluacion DATETIME DEFAULT GETDATE()
);


CREATE TABLE CompetenciaCentro (
    idCompCentro INT IDENTITY(1,1) PRIMARY KEY,
    idCentroPractica INT NOT NULL,        -- Centro donde se aplica la competencia
    idEvaluacion INT NOT NULL,            -- Evaluación asociada
    observacion VARCHAR(500)              -- Nota adicional del centro
);


CREATE TABLE Usuarios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    Email VARCHAR(200) NOT NULL UNIQUE,
    Password VARCHAR(200) NOT NULL
);

INSERT INTO Usuarios (Email, Password)
VALUES ('admin@admin.com', '1234');



ALTER TABLE Estudiante
ADD CONSTRAINT FK_Estudiante_Carrera
FOREIGN KEY (idCarrera) REFERENCES Carrera(idCarrera);

ALTER TABLE Tutor
ADD CONSTRAINT FK_Tutor_Carrera
FOREIGN KEY (idCarrera) REFERENCES Carrera(idCarrera);

ALTER TABLE Supervisor
ADD CONSTRAINT FK_Supervisor_Centro
FOREIGN KEY (idCentroPractica) REFERENCES CentroPractica(idCentroPractica);

ALTER TABLE Practica
ADD CONSTRAINT FK_Practica_Estudiante
FOREIGN KEY (idEstudiante) REFERENCES Estudiante(idEstudiante);

ALTER TABLE Practica
ADD CONSTRAINT FK_Practica_Centro
FOREIGN KEY (idCentroPractica) REFERENCES CentroPractica(idCentroPractica);

ALTER TABLE Practica
ADD CONSTRAINT FK_Practica_Tutor
FOREIGN KEY (idTutor) REFERENCES Tutor(idTutor);

ALTER TABLE Practica
ADD CONSTRAINT FK_Practica_Supervisor
FOREIGN KEY (idSupervisor) REFERENCES Supervisor(idSupervisor);

ALTER TABLE Bitacora
ADD CONSTRAINT FK_Bitacora_Practica
FOREIGN KEY (idPractica) REFERENCES Practica(idPractica);

ALTER TABLE Bitacora
ADD CONSTRAINT FK_Bitacora_Estudiante
FOREIGN KEY (idEstudiante) REFERENCES Estudiante(idEstudiante);

ALTER TABLE InformeSupervisor
ADD CONSTRAINT FK_InfSup_Practica
FOREIGN KEY (idPractica) REFERENCES Practica(idPractica);

ALTER TABLE InformeSupervisor
ADD CONSTRAINT FK_InfSup_Supervisor
FOREIGN KEY (idSupervisor) REFERENCES Supervisor(idSupervisor);

ALTER TABLE InformeTutor
ADD CONSTRAINT FK_InfTut_Practica
FOREIGN KEY (idPractica) REFERENCES Practica(idPractica);

ALTER TABLE InformeTutor
ADD CONSTRAINT FK_InfTut_Tutor
FOREIGN KEY (idTutor) REFERENCES Tutor(idTutor);

ALTER TABLE CompetenciaPractica
ADD CONSTRAINT FK_CompePra_Practica
FOREIGN KEY (idPractica) REFERENCES Practica(idPractica);

ALTER TABLE CompetenciaPractica
ADD CONSTRAINT FK_CompePra_CompeDes
FOREIGN KEY (idCompeDes) REFERENCES CompetenciaDesarrollo(idCompeDes);

ALTER TABLE EvaluacionArea
ADD CONSTRAINT FK_EvalArea_Practica
FOREIGN KEY (idPractica) REFERENCES Practica(idPractica);

ALTER TABLE EvaluacionArea
ADD CONSTRAINT FK_EvalArea_CompeDes
FOREIGN KEY (idCompeDes) REFERENCES CompetenciaDesarrollo(idCompeDes);

ALTER TABLE CompetenciaCentro
ADD CONSTRAINT FK_CompCentro_Centro
FOREIGN KEY (idCentroPractica) REFERENCES CentroPractica(idCentroPractica);

ALTER TABLE CompetenciaCentro
ADD CONSTRAINT FK_CompCentro_Evaluacion
FOREIGN KEY (idEvaluacion) REFERENCES EvaluacionArea(idEvaluacion);
