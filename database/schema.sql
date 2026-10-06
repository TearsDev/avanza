CREATE DATABASE IF NOT EXISTS avanza CHARACTER SET utf8mb4;
USE avanza:

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY;
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('familia', 'terapeuta', 'administrativo') NOT NULL
);

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    nacimiento DATE,
    diagnostico VARCHAR(255),
    id_familia INT NOT NULL,
    FOREIGN KEY (id_familia) REFERENCES usuarios(id)
);

CREATE TABLE turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_terapeuta INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'cancelado', 'realizado') DEFAULT 'pendiente',
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
    FOREIGN KEY (id_terapeuta) REFERENCES usuarios(id)
);

CREATE TABLE sesiones(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_turno INT NOT NULL,
    notas TEXT,
    FOREIGN KEY (id_turno) REFERENCES turnos(id)
);

INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Familia Demo', 'familia@avanza.app', '$2y$12$VdhF4KwXxkciJEvZ.TttBetpYajFKANjF3zxQS5W..ioSWfQtH.O.', 'familia'),
('Terapeuta Demo', 'terapeuta@avanza.app', '$2y$12$VdhF4KwXxkciJEvZ.TttBetpYajFKANjF3zxQS5W..ioSWfQtH.O.', 'terapeuta'),
('Admin Demo', 'admin@avanza.app', '$2y$12$VdhF4KwXxkciJEvZ.TttBetpYajFKANjF3zxQS5W..ioSWfQtH.O.', 'administrativo');