CREATE PROCEDURE sp_GetCompetenciaCentroById
    @idCompCentro INT
AS
BEGIN
    SELECT * FROM CompetenciaCentro WHERE idCompCentro = @idCompCentro;
END


CREATE PROCEDURE sp_DeleteCompetenciaCentro
    @idCompCentro INT
AS
BEGIN
    DELETE FROM CompetenciaCentro WHERE idCompCentro = @idCompCentro;
END


CREATE PROCEDURE sp_UpdateCompetenciaCentro
    @idCompCentro INT,
    @idCentroPractica INT,
    @idEvaluacion INT,
    @observacion VARCHAR(500)
AS
BEGIN
    UPDATE CompetenciaCentro
    SET idCentroPractica = @idCentroPractica,
        idEvaluacion = @idEvaluacion,
        observacion = @observacion
    WHERE idCompCentro = @idCompCentro;
END


CREATE PROCEDURE sp_InsertCompetenciaCentro
    @idCentroPractica INT,
    @idEvaluacion INT,
    @observacion VARCHAR(500)
AS
BEGIN
    INSERT INTO CompetenciaCentro (idCentroPractica, idEvaluacion, observacion)
    VALUES (@idCentroPractica, @idEvaluacion, @observacion);
END
