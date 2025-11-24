CREATE PROCEDURE sp_GetInformeTutorById
    @idInformeT INT
AS
BEGIN
    SELECT * FROM InformeTutor WHERE idInformeT = @idInformeT;
END


CREATE PROCEDURE sp_DeleteInformeTutor
    @idInformeT INT
AS
BEGIN
    DELETE FROM InformeTutor WHERE idInformeT = @idInformeT;
END


CREATE PROCEDURE sp_UpdateInformeTutor
    @idInformeT INT,
    @idPractica INT,
    @idTutor INT,
    @enlaceInformeT VARCHAR(MAX)
AS
BEGIN
    UPDATE InformeTutor
    SET idPractica = @idPractica,
        idTutor = @idTutor,
        enlaceInformeT = @enlaceInformeT
    WHERE idInformeT = @idInformeT;
END


CREATE PROCEDURE sp_InsertInformeTutor
    @idPractica INT,
    @idTutor INT,
    @enlaceInformeT VARCHAR(MAX)
AS
BEGIN
    INSERT INTO InformeTutor (idPractica, idTutor, enlaceInformeT)
    VALUES (@idPractica, @idTutor, @enlaceInformeT);
END
