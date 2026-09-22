-- Esquema de base de datos para Barber Manager
-- Ejecutar en MySQL (XAMPP)

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS barber_manager_db;
USE barber_manager_db;

-- Tabla de barberías
CREATE TABLE IF NOT EXISTS barberias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    INDEX idx_estado (estado),
    INDEX idx_email (email)
);

-- Tabla de barberos
CREATE TABLE IF NOT EXISTS barberos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barberia_id INT,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    especialidad VARCHAR(255) NOT NULL,
    experiencia INT DEFAULT 0,
    estado BOOLEAN DEFAULT TRUE,
    INDEX idx_barberia_id (barberia_id),
    INDEX idx_estado (estado),
    INDEX idx_email (email),
    FOREIGN KEY (barberia_id) REFERENCES barberias(id) ON DELETE SET NULL
);

-- Tabla de turnos
CREATE TABLE IF NOT EXISTS turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clienteNombre VARCHAR(255) NOT NULL,
    clienteTelefono VARCHAR(20) NOT NULL,
    barberoId INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    servicio VARCHAR(255),
    INDEX idx_barbero_id (barberoId),
    INDEX idx_fecha (fecha),
    INDEX idx_fecha_hora (fecha, hora),
    FOREIGN KEY (barberoId) REFERENCES barberos(id) ON DELETE CASCADE
);

-- Insertar datos de ejemplo (opcional)
-- Barbería de ejemplo
INSERT INTO barberias (nombre, email, telefono, direccion, estado) VALUES
('Barbería Central', 'info@barberiacentral.com', '123456789', 'Av. Libertador 123', TRUE);

-- Barbero de ejemplo
INSERT INTO barberos (barberia_id, nombre, email, telefono, especialidad, experiencia, estado) VALUES
(1, 'Juan Pérez', 'juan@barberiacentral.com', '987654321', 'Cortes clásicos', 5, TRUE);

-- Turno de ejemplo
INSERT INTO turnos (clienteNombre, clienteTelefono, barberoId, fecha, hora, servicio) VALUES
('Carlos López', '1122334455', 1, '2026-05-20', '10:00:00', 'Corte y barba');