-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS gestion_usuarios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_usuarios;

-- Tabla de administradores
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de usuarios/asociados
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_asociado VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    tipo_discapacidad VARCHAR(100) NOT NULL,
    porcentaje_discapacidad INT,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    ciudad VARCHAR(100),
    codigo_postal VARCHAR(10),
    fecha_afiliacion DATE NOT NULL,
    estado ENUM('Activo', 'Inactivo', 'Suspendido') DEFAULT 'Activo',
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar un administrador por defecto (usuario: admin, password: admin123)
-- El hash se generará dinámicamente al importar
INSERT INTO administradores (usuario, password, nombre, email) VALUES 
('admin', '$2y$10$YourNewHashHere', 'Administrador', 'admin@familiaunida.com');

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_numero_asociado ON usuarios(numero_asociado);
CREATE INDEX idx_nombre_apellidos ON usuarios(nombre, apellidos);
CREATE INDEX idx_estado ON usuarios(estado);
CREATE INDEX idx_fecha_afiliacion ON usuarios(fecha_afiliacion); 