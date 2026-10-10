SET @inscripcion_carrera_tipo = (
    SELECT COLUMN_TYPE
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inscripcion'
      AND COLUMN_NAME = 'id_carrera'
    LIMIT 1
);

SET @inscripcion_carrera_nullable = (
    SELECT IS_NULLABLE
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inscripcion'
      AND COLUMN_NAME = 'id_carrera'
    LIMIT 1
);

SET @inscripcion_carrera_sql = IF(
    @inscripcion_carrera_tipo IS NULL OR @inscripcion_carrera_nullable = 'YES',
    'SELECT 1',
    CONCAT(
        'ALTER TABLE `inscripcion` MODIFY COLUMN `id_carrera` ',
        @inscripcion_carrera_tipo,
        ' NULL DEFAULT NULL'
    )
);

PREPARE inscripcion_carrera_stmt FROM @inscripcion_carrera_sql;
EXECUTE inscripcion_carrera_stmt;
DEALLOCATE PREPARE inscripcion_carrera_stmt;
