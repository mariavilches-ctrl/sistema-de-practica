CREATE PROCEDURE sp_GetEvaluacionAreaById
    @idEvaluacion INT
AS
BEGIN
    SELECT * FROM EvaluacionArea WHERE idEvaluacion = @idEvaluacion;
END


CREATE PROCEDURE sp_DeleteEvaluacionArea
    @idEvaluacion INT
AS
BEGIN
    DELETE FROM EvaluacionArea WHERE idEvaluacion = @idEvaluacion;
END


CREATE PROCEDURE sp_UpdateEvaluacionArea
    @idEvaluacion INT,
    @idPractica INT,
    @idCompeDes INT,
    @puntaje INT,
    @comentario VARCHAR(500)
AS
BEGIN
    UPDATE EvaluacionArea
    SET idPractica = @idPractica,
        idCompeDes = @idCompeDes,
        puntaje = @puntaje,
        comentario = @comentario
    WHERE idEvaluacion = @idEvaluacion;
END


CREATE PROCEDURE sp_InsertEvaluacionArea
    @idPractica INT,
    @idCompeDes INT,
    @puntaje INT,
    @comentario VARCHAR(500)
AS
BEGIN
    INSERT INTO EvaluacionArea (idPractica, idCompeDes, puntaje, comentario)
    VALUES (@idPractica, @idCompeDes, @puntaje, @comentario);
END
