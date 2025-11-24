CREATE PROCEDURE sp_InsertTutor
    @rut VARCHAR(12),
    @nombre VARCHAR(150),
    @correo VARCHAR(200),
    @telefono VARCHAR(12),
    @idCarrera INT
AS
BEGIN
    INSERT INTO Tutor (rut, nombre, correo, telefono, idCarrera)
    VALUES (@rut, @nombre, @correo, @telefono, @idCarrera);
END

CREATE PROCEDURE sp_UpdateTutor
    @idTutor INT,
    @rut VARCHAR(12),
    @nombre VARCHAR(150),
    @correo VARCHAR(200),
    @telefono VARCHAR(12),
    @idCarrera INT
AS
BEGIN
    UPDATE Tutor
    SET rut = @rut,
        nombre = @nombre,
        correo = @correo,
        telefono = @telefono,
        idCarrera = @idCarrera
    WHERE idTutor = @idTutor;
END

CREATE PROCEDURE sp_DeleteTutor
    @idTutor INT
AS
BEGIN
    DELETE FROM Tutor WHERE idTutor = @idTutor;
END

CREATE PROCEDURE sp_GetTutorById
    @idTutor INT
AS
BEGIN
    SELECT * FROM Tutor WHERE idTutor = @idTutor;
END
