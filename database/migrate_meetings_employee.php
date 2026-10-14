<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

$pdo = \Config\Database::getConnection();

// Verificar si la columna employee_id ya existe
$stmt = $pdo->query("SHOW COLUMNS FROM meetings LIKE 'employee_id'");
$colExists = $stmt->fetch();

if (!$colExists) {
    echo "Agregando columna employee_id a la tabla meetings...\n";
    $pdo->exec("ALTER TABLE meetings ADD COLUMN employee_id INT NULL AFTER realtor_id");
    $pdo->exec("ALTER TABLE meetings ADD CONSTRAINT fk_meetings_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL");
    $pdo->exec("ALTER TABLE meetings ADD INDEX idx_meetings_employee (employee_id)");
    echo "Columna e índice creados exitosamente.\n";
} else {
    echo "La columna employee_id ya existe en la tabla meetings.\n";
}

// Asignar empleados por defecto a las reuniones de prueba existentes si están vacías
// Reunión 1: Asignada a Carlos Mendoza (Jefe, id=1) o Valentina Rossi (Asistente, id=2)
$pdo->exec("UPDATE meetings SET employee_id = 2 WHERE id = 1 AND (employee_id IS NULL OR employee_id = 0)");
$pdo->exec("UPDATE meetings SET employee_id = 4 WHERE id = 2 AND (employee_id IS NULL OR employee_id = 0)");

echo "Migración completada con éxito.\n";
