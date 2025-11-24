CREATE PROCEDURE sp_InsertCompetenciaDesarrollo
    @categoria VARCHAR(30)
AS
BEGIN
    INSERT INTO CompetenciaDesarrollo (categoria)
    VALUES (@categoria);
END

CREATE PROCEDURE sp_UpdateCompetenciaDesarrollo
    @idCompeDes INT,
    @categoria VARCHAR(30)
AS
BEGIN
    UPDATE CompetenciaDesarrollo
    SET categoria = @categoria
    WHERE idCompeDes = @idCompeDes;
END

CREATE PROCEDURE sp_DeleteCompetenciaDesarrollo
    @idCompeDes INT
AS
BEGIN
    DELETE FROM CompetenciaDesarrollo WHERE idCompeDes = @idCompeDes;
END

CREATE PROCEDURE sp_GetCompetenciaDesarrolloById
    @idCompeDes INT
AS
BEGIN
    SELECT * FROM CompetenciaDesarrollo WHERE idCompeDes = @idCompeDes;
END


