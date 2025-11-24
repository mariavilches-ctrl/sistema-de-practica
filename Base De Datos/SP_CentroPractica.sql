CREATE PROCEDURE sp_InsertCentroPractica
    @rutEmpresa VARCHAR(12),
    @nombre VARCHAR(150),
    @descripcion VARCHAR(500),
    @habilidadesEsperadas VARCHAR(MAX),
    @direccion VARCHAR(250)
AS
BEGIN
    INSERT INTO CentroPractica (rutEmpresa, nombre, descripcion, habilidadesEsperadas, direccion)
    VALUES (@rutEmpresa, @nombre, @descripcion, @habilidadesEsperadas, @direccion);
END

CREATE PROCEDURE sp_UpdateCentroPractica
    @idCentroPractica INT,
    @rutEmpresa VARCHAR(12),
    @nombre VARCHAR(150),
    @descripcion VARCHAR(500),
    @habilidadesEsperadas VARCHAR(MAX),
    @direccion VARCHAR(250)
AS
BEGIN
    UPDATE CentroPractica
    SET rutEmpresa = @rutEmpresa,
        nombre = @nombre,
        descripcion = @descripcion,
        habilidadesEsperadas = @habilidadesEsperadas,
        direccion = @direccion
    WHERE idCentroPractica = @idCentroPractica;
END

CREATE PROCEDURE sp_DeleteCentroPractica
    @idCentroPractica INT
AS
BEGIN
    DELETE FROM CentroPractica WHERE idCentroPractica = @idCentroPractica;
END

CREATE PROCEDURE sp_GetCentroPracticaById
    @idCentroPractica INT
AS
BEGIN
    SELECT * FROM CentroPractica WHERE idCentroPractica = @idCentroPractica;
END
