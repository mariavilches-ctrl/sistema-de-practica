CREATE PROCEDURE sp_InsertBitacora
    @idPractica INT,
    @idEstudiante INT,
    @habilidadesDesarrolladas VARCHAR(500),
    @desafios VARCHAR(500),
    @logros VARCHAR(500)
AS
BEGIN
    INSERT INTO Bitacora (idPractica, idEstudiante, habilidadesDesarrolladas, desafios, logros)
    VALUES (@idPractica, @idEstudiante, @habilidadesDesarrolladas, @desafios, @logros);
END

CREATE PROCEDURE sp_UpdateBitacora
    @idBitacora INT,
    @idPractica INT,
    @idEstudiante INT,
    @habilidadesDesarrolladas VARCHAR(500),
    @desafios VARCHAR(500),
    @logros VARCHAR(500)
AS
BEGIN
    UPDATE Bitacora
    SET idPractica = @idPractica,
        idEstudiante = @idEstudiante,
        habilidadesDesarrolladas = @habilidadesDesarrolladas,
        desafios = @desafios,
        logros = @logros
    WHERE idBitacora = @idBitacora;
END

CREATE PROCEDURE sp_DeleteBitacora
    @idBitacora INT
AS
BEGIN
    DELETE FROM Bitacora WHERE idBitacora = @idBitacora;
END

CREATE PROCEDURE sp_GetBitacoraById
    @idBitacora INT
AS
BEGIN
    SELECT * FROM Bitacora WHERE idBitacora = @idBitacora;
END

