CREATE PROCEDURE sp_InsertCompetenciaPractica
    @idPractica INT,
    @idCompeDes INT,
    @nivel VARCHAR(50)
AS
BEGIN
    INSERT INTO CompetenciaPractica (idPractica, idCompeDes, nivel)
    VALUES (@idPractica, @idCompeDes, @nivel);
END

CREATE PROCEDURE sp_UpdateCompetenciaPractica
    @idCompePra INT,
    @idPractica INT,
    @idCompeDes INT,
    @nivel VARCHAR(50)
AS
BEGIN
    UPDATE CompetenciaPractica
    SET idPractica = @idPractica,
        idCompeDes = @idCompeDes,
        nivel = @nivel
    WHERE idCompePra = @idCompePra;
END


CREATE PROCEDURE sp_DeleteCompetenciaPractica
    @idCompePra INT
AS
BEGIN
    DELETE FROM CompetenciaPractica WHERE idCompePra = @idCompePra;
END

CREATE PROCEDURE sp_GetCompetenciaPracticaById
    @idCompePra INT
AS
BEGIN
    SELECT * FROM CompetenciaPractica WHERE idCompePra = @idCompePra;
END

