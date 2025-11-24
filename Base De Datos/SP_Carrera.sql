CREATE PROCEDURE sp_InsertCarrera
    @nombre VARCHAR(150),
    @directorCarrera VARCHAR(150),
    @facultad VARCHAR(200)
AS
BEGIN
    INSERT INTO Carrera (nombre, directorCarrera, facultad)
    VALUES (@nombre, @directorCarrera, @facultad);
END

CREATE PROCEDURE sp_UpdateCarrera
    @idCarrera INT,
    @nombre VARCHAR(150),
    @directorCarrera VARCHAR(150),
    @facultad VARCHAR(200)
AS
BEGIN
    UPDATE Carrera
    SET nombre = @nombre,
        directorCarrera = @directorCarrera,
        facultad = @facultad
    WHERE idCarrera = @idCarrera;
END

CREATE PROCEDURE sp_DeleteCarrera
    @idCarrera INT
AS
BEGIN
    DELETE FROM Carrera WHERE idCarrera = @idCarrera;
END

CREATE PROCEDURE sp_GetCarreraById
    @idCarrera INT
AS
BEGIN
    SELECT * FROM Carrera WHERE idCarrera = @idCarrera;
END
