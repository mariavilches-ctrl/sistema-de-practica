CREATE PROCEDURE sp_GetInformeSupervisorById
    @idInformeS INT
AS
BEGIN
    SELECT * FROM InformeSupervisor WHERE idInformeS = @idInformeS;
END


CREATE PROCEDURE sp_DeleteInformeSupervisor
    @idInformeS INT
AS
BEGIN
    DELETE FROM InformeSupervisor WHERE idInformeS = @idInformeS;
END


CREATE PROCEDURE sp_UpdateInformeSupervisor
    @idInformeS INT,
    @idPractica INT,
    @idSupervisor INT,
    @enlaceInformeS VARCHAR(MAX)
AS
BEGIN
    UPDATE InformeSupervisor
    SET idPractica = @idPractica,
        idSupervisor = @idSupervisor,
        enlaceInformeS = @enlaceInformeS
    WHERE idInformeS = @idInformeS;
END


CREATE PROCEDURE sp_InsertInformeSupervisor
    @idPractica INT,
    @idSupervisor INT,
    @enlaceInformeS VARCHAR(MAX)
AS
BEGIN
    INSERT INTO InformeSupervisor (idPractica, idSupervisor, enlaceInformeS)
    VALUES (@idPractica, @idSupervisor, @enlaceInformeS);
END
