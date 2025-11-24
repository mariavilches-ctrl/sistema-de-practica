CREATE PROCEDURE sp_InsertSupervisor
    @rut VARCHAR(12),
    @nombre VARCHAR(150),
    @correo VARCHAR(150),
    @telefono VARCHAR(12),
    @cargo VARCHAR(50),
    @idCentroPractica INT
AS
BEGIN
    INSERT INTO Supervisor (rut, nombre, correo, telefono, cargo, idCentroPractica)
    VALUES (@rut, @nombre, @correo, @telefono, @cargo, @idCentroPractica);
END

CREATE PROCEDURE sp_UpdateSupervisor
    @idSupervisor INT,
    @rut VARCHAR(12),
    @nombre VARCHAR(150),
    @correo VARCHAR(150),
    @telefono VARCHAR(12),
    @cargo VARCHAR(50),
    @idCentroPractica INT
AS
BEGIN
    UPDATE Supervisor
    SET rut = @rut,
        nombre = @nombre,
        correo = @correo,
        telefono = @telefono,
        cargo = @cargo,
        idCentroPractica = @idCentroPractica
    WHERE idSupervisor = @idSupervisor;
END

CREATE PROCEDURE sp_DeleteSupervisor
    @idSupervisor INT
AS
BEGIN
    DELETE FROM Supervisor WHERE idSupervisor = @idSupervisor;
END

CREATE PROCEDURE sp_GetSupervisorById
    @idSupervisor INT
AS
BEGIN
    SELECT * FROM Supervisor WHERE idSupervisor = @idSupervisor;
END
