-- =====================================================================
-- MIGRACIÓN V2: ADAPTACIÓN DE ROLES, ESPECIALIDADES, REALTORS Y MÓDULOS
-- =====================================================================

USE miami_agency_db;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Modificar tabla realtors para agregar enlaces de credenciales y Dropbox
ALTER TABLE realtors
    ADD COLUMN IF NOT EXISTS credentials_url VARCHAR(500) NULL AFTER miami_zone,
    ADD COLUMN IF NOT EXISTS dropbox_url VARCHAR(500) NULL AFTER credentials_url;

-- 2. Actualizar Roles en la tabla roles
DELETE FROM user_specialties;
DELETE FROM users WHERE id > 0;
DELETE FROM employees WHERE id > 0;
DELETE FROM roles WHERE id > 0;

INSERT INTO roles (id, name, description) VALUES
(1, 'JEFE', 'Dirección General, supervisión total y control operativo'),
(2, 'ASISTENTE', 'Asistente Operativo, gestión de agendas, Realtors y soporte'),
(3, 'ESTRATEGA_MARKETING', 'Estrategia de Marketing, cronograma de emails y campañas'),
(4, 'SOCIAL_MANAGER', 'Social Media Manager, producción y entregables de contenido');

-- 3. Actualizar Especialidades
DELETE FROM specialties WHERE id > 0;

INSERT INTO specialties (id, code, name, description) VALUES
(1, 'EDITOR_VIDEO', 'Editor de Videos', 'Edición de reels, videos de propiedades y walkthroughs'),
(2, 'EDITOR_CARRUSELES', 'Editor de Carruseles', 'Diseño de carruseles de propiedades e infografías'),
(3, 'EMAIL_MARKETING', 'Email Marketing', 'Campañas y newsletters para base de datos de Realtors'),
(4, 'BLOGGERS', 'Bloggers', 'Redacción de artículos de blog y contenido editorial'),
(5, 'AGENDADOR_REUNIONES', 'Agendador de Reuniones', 'Coordinación y agenda de llamadas con clientes y prospectos'),
(6, 'AUTOMATIZADOR', 'Automatizador', 'Automatización de flujos de trabajo, CRM y secuencias');

-- 4. Crear o Actualizar tabla de Contratos Finales (Almacén de PDFs Concluidos con Fecha de Subida Automática)
-- Para no tener problemas de llaves foráneas con templates antiguos:
DROP TABLE IF EXISTS contract_reviews;

CREATE TABLE IF NOT EXISTS contracts_new (
    id INT AUTO_INCREMENT PRIMARY KEY,
    realtor_id INT NOT NULL,
    contract_title VARCHAR(200) NOT NULL,
    pdf_filename VARCHAR(255) NOT NULL,
    notes TEXT NULL,
    uploaded_by_user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contracts_realtor (realtor_id),
    INDEX idx_contracts_created (created_at),
    CONSTRAINT fk_contracts_realtor_new FOREIGN KEY (realtor_id) REFERENCES realtors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Si la tabla contracts ya existía, la respaldamos o reemplazamos
DROP TABLE IF EXISTS contracts;
RENAME TABLE contracts_new TO contracts;

-- 5. Crear tabla para Calendario de Email Marketing
CREATE TABLE IF NOT EXISTS email_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    realtor_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    scheduled_date DATE NOT NULL,
    status VARCHAR(30) DEFAULT 'PROGRAMADO',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ec_realtor (realtor_id),
    INDEX idx_ec_date (scheduled_date),
    CONSTRAINT fk_ec_realtor FOREIGN KEY (realtor_id) REFERENCES realtors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Crear tabla para Agenda de Reuniones
CREATE TABLE IF NOT EXISTS meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    realtor_id INT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    duration_minutes INT DEFAULT 30,
    meeting_link VARCHAR(500) NULL,
    description TEXT NULL,
    status VARCHAR(30) DEFAULT 'PROGRAMADA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_meetings_date (meeting_date),
    INDEX idx_meetings_realtor (realtor_id),
    CONSTRAINT fk_meetings_realtor FOREIGN KEY (realtor_id) REFERENCES realtors(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. Insertar Usuarios Iniciales para los 4 Roles (Contraseña: password123)
-- Hash estándar de PHP password_hash('password123', PASSWORD_BCRYPT)
-- $2y$10$eA0y5iUoY0b16R/i1n3q6.f9Q1wG4sWwZ/Oq0k5iYkH6aN1r9Yd1W (o similar)
INSERT INTO users (id, email, password_hash, role_id, status) VALUES
(1, 'jefe@miamiagency.com', '$2y$10$8szthNrRQe7Za1qhuWWETenrPz4INdVyxV.Dvsky9bZMQOCFZM5.O', 1, 'ACTIVO'),
(2, 'asistente@miamiagency.com', '$2y$10$8szthNrRQe7Za1qhuWWETenrPz4INdVyxV.Dvsky9bZMQOCFZM5.O', 2, 'ACTIVO'),
(3, 'estrategia@miamiagency.com', '$2y$10$8szthNrRQe7Za1qhuWWETenrPz4INdVyxV.Dvsky9bZMQOCFZM5.O', 3, 'ACTIVO'),
(4, 'social@miamiagency.com', '$2y$10$8szthNrRQe7Za1qhuWWETenrPz4INdVyxV.Dvsky9bZMQOCFZM5.O', 4, 'ACTIVO');

INSERT INTO employees (id, user_id, full_name, phone, hire_date) VALUES
(1, 1, 'Carlos Mendoza', '+1 (305) 555-0101', '2024-01-15'),
(2, 2, 'Valentina Rossi', '+1 (305) 555-0102', '2024-03-01'),
(3, 3, 'Sofía Herrera', '+1 (305) 555-0103', '2024-04-10'),
(4, 4, 'Mateo Silva', '+1 (305) 555-0104', '2024-05-20');

-- 8. Asignar Especialidades a los Usuarios
INSERT INTO user_specialties (user_id, specialty_id) VALUES
(2, 5), -- Valentina: Agendador de Reuniones
(3, 3), -- Sofía: Email Marketing
(3, 4), -- Sofía: Bloggers
(3, 6), -- Sofía: Automatizador
(4, 1), -- Mateo: Editor de Videos
(4, 2); -- Mateo: Editor de Carruseles

SET FOREIGN_KEY_CHECKS = 1;
