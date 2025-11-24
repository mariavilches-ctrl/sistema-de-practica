CREATE PROCEDURE sp_InsertPractica
    @idEstudiante INT,
    @idCentroPractica INT,
    @idTutor INT,
    @idSupervisor INT,
    @tipo VARCHAR(30),
    @fechaDeInicio DATETIME,
    @fechaDeTermino DATETIME,
    @actividades VARCHAR(500),
    @evidenciaImg VARCHAR(MAX)
AS
BEGIN
    INSERT INTO Practica (idEstudiante, idCentroPractica, idTutor, idSupervisor, tipo, fechaDeInicio, fechaDeTermino, actividades, evidenciaImg)
    VALUES (@idEstudiante, @idCentroPractica, @idTutor, @idSupervisor, @tipo, @fechaDeInicio, @fechaDeTermino, @actividades, @evidenciaImg);
END

CREATE PROCEDURE sp_UpdatePractica
    @idPractica INT,
    @idEstudiante INT,
    @idCentroPractica INT,
    @idTutor INT,
    @idSupervisor INT,
    @tipo VARCHAR(30),
    @fechaDeInicio DATETIME,
    @fechaDeTermino DATETIME,
    @actividades VARCHAR(500),
    @evidenciaImg VARCHAR(MAX)
AS
BEGIN
    UPDATE Practica
    SET idEstudiante = @idEstudiante,
        idCentroPractica = @idCentroPractica,
        idTutor = @idTutor,
        idSupervisor = @idSupervisor,
        tipo = @tipo,
        fechaDeInicio = @fechaDeInicio,
        fechaDeTermino = @fechaDeTermino,
        actividades = @actividades,
        evidenciaImg = @evidenciaImg
    WHERE idPractica = @idPractica;
END

CREATE PROCEDURE sp_DeletePractica
    @idPractica INT
AS
BEGIN
    DELETE FROM Practica WHERE idPractica = @idPractica;
END

CREATE PROCEDURE sp_GetPracticaById
    @idPractica INT
AS
BEGIN
    SELECT * FROM Practica WHERE idPractica = @idPractica;
END
