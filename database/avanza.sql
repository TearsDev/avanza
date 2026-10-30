CREATE DATABASE avanza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE avanza;

CREATE TABLE centro (
    id INT PRIMARY KEY DEFAULT 1,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    CONSTRAINT centro_una_fila CHECK (id = 1)
);

CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE obras_sociales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    rol ENUM('terapeuta', 'administrativo') NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE terapeutas (
    id_usuario INT PRIMARY KEY,
    id_especialidad INT NOT NULL,
    matricula VARCHAR(30) NOT NULL UNIQUE,
    telefono VARCHAR(30) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_especialidad) REFERENCES especialidades(id)
);

CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_terapeuta INT NOT NULL,
    dia TINYINT NOT NULL,
    hora_desde TIME NOT NULL,
    hora_hasta TIME NOT NULL,
    UNIQUE (id_terapeuta, dia),
    CONSTRAINT horarios_dia CHECK (dia BETWEEN 1 AND 5),
    FOREIGN KEY (id_terapeuta) REFERENCES terapeutas(id_usuario) ON DELETE CASCADE
);

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    dni VARCHAR(15) NOT NULL UNIQUE,
    nacimiento DATE NOT NULL,
    diagnostico VARCHAR(255) NOT NULL,
    id_obra_social INT NOT NULL,
    resp_nombre VARCHAR(100) NOT NULL,
    resp_vinculo VARCHAR(50) NOT NULL,
    resp_email VARCHAR(100),
    resp_telefono VARCHAR(30) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_obra_social) REFERENCES obras_sociales(id)
);

CREATE TABLE asignaciones (
    id_paciente INT NOT NULL,
    id_terapeuta INT NOT NULL,
    PRIMARY KEY (id_paciente, id_terapeuta),
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_terapeuta) REFERENCES terapeutas(id_usuario) ON DELETE CASCADE
);

CREATE TABLE turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_terapeuta INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'realizado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
    FOREIGN KEY (id_terapeuta) REFERENCES terapeutas(id_usuario)
);

CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_turno INT NOT NULL UNIQUE,
    notas TEXT NOT NULL,
    FOREIGN KEY (id_turno) REFERENCES turnos(id) ON DELETE CASCADE
);