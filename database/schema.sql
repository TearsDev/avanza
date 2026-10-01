CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('family', 'therapist', 'administrative') NOT NULL,
    fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE obras_sociales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    diagnostico VARCHAR(255),
    id_obra_social INT,
    id_familia_responsable INT NOT NULL,
    fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT true,
    FOREIGN KEY (id_obra_social) REFERENCES obras_sociales(id) ON DELETE SET NULL,
    FOREIGN KEY (id_familia_responsable) REFERENCES usuarios(id) ON DELETE RESTRICT
);

CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE terapeutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    id_especialidad INT NOT NULL,
    matricula VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_especialidad) REFERENCES especialidades(id) ON DELETE RESTRICT
);

CREATE TABLE paciente_terapeuta (
    id_paciente INT NOT NULL,
    id_terapeuta INT NOT NULL,
    PRIMARY KEY (id_paciente, id_terapeuta),
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_terapeuta) REFERENCES terapeutas(id) ON DELETE CASCADE
);

CREATE TABLE turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_terapeuta INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'cancelado', 'realizado') DEFAULT 'pendiente',
    creado_por INT NOT NULL,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_terapeuta) REFERENCES terapeutas(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_terapeuta_horario (id_terapeuta, fecha, hora)
);

CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_turno INT NOT NULL UNIQUE,
    notas_clinicas TEXT,
    objetivos_trabajos TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_turno) REFERENCES turnos(id) ON DELETE CASCADE
);

CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_terapeuta INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_terapeuta) REFERENCES terapeutas(id) ON DELETE CASCADE
);

CREATE TABLE log_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    accion VARCHAR(255) NOT NULL, 
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expira DATETIME NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE INDEX idx_turnos_fecha ON turnos(fecha);
CREATE INDEX idx_pacientes_familia ON pacientes(id_familia_responsable);
CREATE INDEX idx_log_fecha ON log_actividad(fecha);