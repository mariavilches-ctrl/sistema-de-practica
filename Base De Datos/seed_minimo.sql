-- seed_minimo.sql — tablas mínimas y datos de prueba (uso local)
-- Úsalo solo en entorno local de desarrollo.

-- Tabla para tipos de práctica (con horas requeridas)
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[TipoPractica]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[TipoPractica] (
        [idTipoPractica] INT IDENTITY(1,1) PRIMARY KEY,
        [nombre] VARCHAR(150) NOT NULL,
        [horasRequeridas] INT NOT NULL
    );
END

-- Tabla para sesiones relacionadas a una práctica
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Sesion]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[Sesion] (
        [idSesion] INT IDENTITY(1,1) PRIMARY KEY,
        [idPractica] INT NULL,
        [fecha] DATE NOT NULL,
        [horaInicio] TIME NOT NULL,
        [horaTermino] TIME NOT NULL,
        [horas] INT NOT NULL,
        [actividad] VARCHAR(500) NULL,
        [estado] VARCHAR(50) DEFAULT 'Programada'
    );
END

-- Asegurar tabla Usuarios para pruebas de login
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Usuarios]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[Usuarios] (
        [id] INT IDENTITY(1,1) PRIMARY KEY,
        [Email] VARCHAR(200) NOT NULL UNIQUE,
        [Password] VARCHAR(200) NOT NULL,
        [nombre] VARCHAR(200) NULL
    );
END

-- Insertar tipos de práctica de ejemplo
IF NOT EXISTS (SELECT 1 FROM [dbo].[TipoPractica] WHERE nombre = 'Práctica I')
BEGIN
    INSERT INTO [dbo].[TipoPractica] (nombre, horasRequeridas) VALUES ('Práctica I', 80);
END

IF NOT EXISTS (SELECT 1 FROM [dbo].[TipoPractica] WHERE nombre = 'Práctica Profesional')
BEGIN
    INSERT INTO [dbo].[TipoPractica] (nombre, horasRequeridas) VALUES ('Práctica Profesional', 320);
END

-- Insertar usuario de prueba para login si no existe
IF NOT EXISTS (SELECT 1 FROM [dbo].[Usuarios] WHERE Email = 'test@example.com')
BEGIN
    INSERT INTO [dbo].[Usuarios] (Email, Password, nombre) VALUES ('test@example.com', 'password123', 'Usuario Prueba');
END

PRINT 'seed_minimo.sql: tablas y datos de prueba creados/asegurados.';
