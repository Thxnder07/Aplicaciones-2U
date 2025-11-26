-- =====================================================
-- SCRIPT DE MIGRACIÓN - Sistema de Gestión de Eventos
-- =====================================================
-- Este script actualiza la base de datos existente
-- agregando las tablas y campos necesarios para el
-- sistema completo de autenticación, inscripciones y cursos
-- =====================================================

USE eventhub_db;

-- =====================================================
-- 1. ACTUALIZAR TABLA eventos
-- =====================================================

-- Agregar campo estado (Activo/Inactivo)
ALTER TABLE eventos 
ADD COLUMN estado ENUM('activo', 'inactivo') DEFAULT 'activo' AFTER destacado;

-- Agregar campo cupos
ALTER TABLE eventos 
ADD COLUMN cupos INT DEFAULT 100 AFTER estado;

-- Agregar campo cupos_disponibles
ALTER TABLE eventos 
ADD COLUMN cupos_disponibles INT DEFAULT 100 AFTER cupos;

-- Actualizar cupos_disponibles con el valor de cupos para eventos existentes
UPDATE eventos SET cupos_disponibles = cupos WHERE cupos_disponibles IS NULL;

-- Asegurar que fecha_inicio existe (ya existe pero verificamos)
-- Si no existe, descomentar la siguiente línea:
-- ALTER TABLE eventos ADD COLUMN fecha_inicio DATE AFTER fecha_texto;

-- =====================================================
-- 2. CREAR TABLA cursos
-- =====================================================

CREATE TABLE IF NOT EXISTS cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    fecha DATE,
    horario VARCHAR(100),
    ponente_id INT NULL,
    cupos INT DEFAULT 50,
    cupos_disponibles INT DEFAULT 50,
    precio DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (ponente_id) REFERENCES ponentes(id) ON DELETE SET NULL,
    INDEX idx_evento (evento_id),
    INDEX idx_ponente (ponente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. CREAR TABLA inscripciones
-- =====================================================

CREATE TABLE IF NOT EXISTS inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    evento_id INT NOT NULL,
    curso_id INT NULL COMMENT 'NULL si se inscribe solo al evento',
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('confirmada', 'cancelada') DEFAULT 'confirmada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_inscripcion_evento (usuario_id, evento_id),
    UNIQUE KEY unique_inscripcion_curso (usuario_id, evento_id, curso_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_evento (evento_id),
    INDEX idx_curso (curso_id),
    INDEX idx_fecha (fecha_inscripcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. CREAR TABLA log_acciones (Opcional)
-- =====================================================

CREATE TABLE IF NOT EXISTS log_acciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(100) NOT NULL COMMENT 'crear_evento, editar_evento, eliminar_evento, etc.',
    entidad VARCHAR(50) NOT NULL COMMENT 'evento, curso, inscripcion, usuario',
    entidad_id INT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detalles TEXT,
    ip_address VARCHAR(45),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_entidad (entidad, entidad_id),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. ACTUALIZAR USUARIO ADMIN (Si no existe)
-- =====================================================

-- Verificar si existe el admin, si no, crearlo
-- Password: admin123 (debe ser hasheado con password_hash en PHP)
-- IMPORTANTE: Actualizar este hash con el generado por PHP
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Admin', 'admin@eventhub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE nombre = nombre;

-- =====================================================
-- 6. DATOS DE PRUEBA (Opcional - Solo para desarrollo)
-- =====================================================

-- Insertar algunos cursos de ejemplo asociados a eventos
-- (Descomentar si necesitas datos de prueba)

/*
INSERT INTO cursos (evento_id, nombre, descripcion, fecha, horario, ponente_id, cupos, cupos_disponibles, precio) 
SELECT 
    e.id,
    CONCAT('Curso: ', e.titulo),
    'Descripción del curso asociado al evento',
    e.fecha_inicio,
    e.horario,
    (SELECT id FROM ponentes LIMIT 1),
    30,
    30,
    e.precio * 0.5
FROM eventos e
LIMIT 3;
*/

-- =====================================================
-- 7. VERIFICACIONES
-- =====================================================

-- Verificar que las tablas se crearon correctamente
SELECT 'Verificando estructura de tablas...' AS status;

SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'eventhub_db'
AND TABLE_NAME IN ('eventos', 'cursos', 'inscripciones', 'log_acciones', 'usuarios')
ORDER BY TABLE_NAME;

-- Verificar campos agregados a eventos
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'eventhub_db'
AND TABLE_NAME = 'eventos'
AND COLUMN_NAME IN ('estado', 'cupos', 'cupos_disponibles');

-- =====================================================
-- FIN DEL SCRIPT DE MIGRACIÓN
-- =====================================================

SELECT 'Migración completada exitosamente!' AS resultado;

