-- =====================================================================
-- ESQUEMA DE BASE DE DATOS RELACIONAL NORMALIZADO EN 3NF (MySQL / MariaDB)
-- AGENCIA AUDIOVISUAL Y OPERACIONES CON REALTORS EN MIAMI
-- =====================================================================

CREATE DATABASE IF NOT EXISTS miami_agency_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE miami_agency_db;

-- ---------------------------------------------------------------------
-- DOMINIO DE AUTENTICACIÓN Y CONTROL DE ACCESO (RBAC)
-- ---------------------------------------------------------------------

-- 1. Tabla de Roles Principales
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    status VARCHAR(20) DEFAULT 'ACTIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Especialidades / Permisos Granulares de Empleados
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    status VARCHAR(20) DEFAULT 'ACTIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabla de Usuarios (Credenciales de Acceso)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'ACTIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status),
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 4. Tabla Intermedia: Especialidades asignadas a cada Usuario (3NF - M:N)
CREATE TABLE IF NOT EXISTS user_specialties (
    user_id INT NOT NULL,
    specialty_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, specialty_id),
    INDEX idx_us_specialty (specialty_id),
    CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_us_specialty FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- DOMINIO OPERATIVO DE LA AGENCIA
-- ---------------------------------------------------------------------

-- 5. Perfil de Empleados (Extensión del Usuario con Datos Laborales)
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NULL,
    hire_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_employees_user (user_id),
    CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Clientes Realtors (Miami Real Estate Agents / Brokerages)
CREATE TABLE IF NOT EXISTS realtors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    miami_zone VARCHAR(100) NOT NULL,
    social_handles JSON NULL,
    status VARCHAR(20) DEFAULT 'ACTIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_realtors_zone (miami_zone),
    INDEX idx_realtors_status (status)
) ENGINE=InnoDB;

-- 7. Plantillas Base Dinámicas de Contratos
CREATE TABLE IF NOT EXISTS contract_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    template_body_html LONGTEXT NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 8. Contratos Generados
CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    realtor_id INT NOT NULL,
    template_id INT NOT NULL,
    generated_by_employee_id INT NOT NULL,
    contract_code VARCHAR(50) NOT NULL UNIQUE,
    compiled_body_html LONGTEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    monthly_fee DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(30) DEFAULT 'BORRADOR',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contracts_status (status),
    INDEX idx_contracts_realtor (realtor_id),
    INDEX idx_contracts_employee (generated_by_employee_id),
    INDEX idx_contracts_dates (start_date, end_date),
    CONSTRAINT fk_contracts_realtor FOREIGN KEY (realtor_id) REFERENCES realtors(id) ON DELETE RESTRICT,
    CONSTRAINT fk_contracts_template FOREIGN KEY (template_id) REFERENCES contract_templates(id) ON DELETE RESTRICT,
    CONSTRAINT fk_contracts_employee FOREIGN KEY (generated_by_employee_id) REFERENCES employees(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 9. Historial y Revisiones de Contratos (Auditoría del Jefe)
CREATE TABLE IF NOT EXISTS contract_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    reviewer_user_id INT NOT NULL,
    status_assigned VARCHAR(30) NOT NULL,
    feedback_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cr_contract (contract_id),
    INDEX idx_cr_reviewer (reviewer_user_id),
    CONSTRAINT fk_cr_contract FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
    CONSTRAINT fk_cr_reviewer FOREIGN KEY (reviewer_user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 10. Tareas y Actividades Audiovisuales
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    realtor_id INT NOT NULL,
    employee_id INT NOT NULL,
    contract_id INT NULL,
    activity_type VARCHAR(50) NOT NULL,
    priority VARCHAR(20) DEFAULT 'MEDIA',
    scheduled_date DATE NOT NULL,
    current_status VARCHAR(30) DEFAULT 'NUEVA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_act_employee_status (employee_id, current_status),
    INDEX idx_act_realtor (realtor_id),
    INDEX idx_act_contract (contract_id),
    INDEX idx_act_scheduled (scheduled_date),
    CONSTRAINT fk_act_realtor FOREIGN KEY (realtor_id) REFERENCES realtors(id) ON DELETE CASCADE,
    CONSTRAINT fk_act_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT,
    CONSTRAINT fk_act_contract FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. Entregables Audiovisuales (Subidos por Empleados: Links Drive, Frame.io)
CREATE TABLE IF NOT EXISTS activity_deliverables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    deliverable_url VARCHAR(500) NOT NULL,
    deliverable_type VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_deliv_activity (activity_id),
    CONSTRAINT fk_deliv_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Revisiones y Feedback de Entregables (Por el Jefe)
CREATE TABLE IF NOT EXISTS activity_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deliverable_id INT NOT NULL,
    reviewer_user_id INT NOT NULL,
    review_status VARCHAR(30) NOT NULL,
    review_notes TEXT NOT NULL,
    reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ar_deliverable (deliverable_id),
    INDEX idx_ar_reviewer (reviewer_user_id),
    CONSTRAINT fk_ar_deliverable FOREIGN KEY (deliverable_id) REFERENCES activity_deliverables(id) ON DELETE CASCADE,
    CONSTRAINT fk_ar_reviewer FOREIGN KEY (reviewer_user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
