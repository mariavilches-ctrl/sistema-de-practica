CREATE PROCEDURE sp_InsertEstudiante
    @rut VARCHAR(12),
    @nombreCompleto VARCHAR(200),
    @anoLectivo VARCHAR(10),
    @domicilio VARCHAR(50),
    @telefono VARCHAR(12),
    @correoInstitucional VARCHAR(100),
    @idCarrera INT
AS
BEGIN
    INSERT INTO Estudiante (rut, nombreCompleto, anoLectivo, domicilio, telefono, correoInstitucional, idCarrera)
    VALUES (@rut, @nombreCompleto, @anoLectivo, @domicilio, @telefono, @correoInstitucional, @idCarrera);
END

CREATE PROCEDURE sp_UpdateEstudiante
    @idEstudiante INT,
    @rut VARCHAR(12),
    @nombreCompleto VARCHAR(200),
    @anoLectivo VARCHAR(10),
    @domicilio VARCHAR(50),
    @telefono VARCHAR(12),
    @correoInstitucional VARCHAR(100),
    @idCarrera INT
AS
BEGIN
    UPDATE Estudiante
    SET rut = @rut,
        nombreCompleto = @nombreCompleto,
        anoLectivo = @anoLectivo,
        domicilio = @domicilio,
        telefono = @telefono,
        correoInstitucional = @correoInstitucional,
        idCarrera = @idCarrera
    WHERE idEstudiante = @idEstudiante;
END

CREATE PROCEDURE sp_DeleteEstudiante
    @idEstudiante INT
AS
BEGIN
    DELETE FROM Estudiante WHERE idEstudiante = @idEstudiante;
END

CREATE PROCEDURE sp_GetEstudianteById
    @idEstudiante INT
AS
BEGIN
    SELECT * FROM Estudiante WHERE idEstudiante = @idEstudiante;
END
