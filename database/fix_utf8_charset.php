<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

$pdo = \Config\Database::getConnection();

echo "1. Altering database default charset to utf8mb4...\n";
$pdo->exec("ALTER DATABASE miami_agency_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

$tables = [
    'roles',
    'specialties',
    'users',
    'user_specialties',
    'employees',
    'realtors',
    'contracts',
    'email_campaigns',
    'meetings',
    'activities',
    'activity_deliverables',
    'activity_reviews'
];

echo "2. Converting all tables to utf8mb4 and utf8mb4_unicode_ci...\n";
foreach ($tables as $t) {
    echo "  Converting {$t}...\n";
    $pdo->exec("ALTER TABLE {$t} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
}

echo "3. Repairing corrupted accents and strings in tables...\n";

// Fix roles
$roles = [
    1 => 'Dirección General, supervisión total y control operativo',
    2 => 'Asistente Operativo, gestión de agendas, Realtors y soporte',
    3 => 'Estrategia de Marketing, cronograma de emails y campañas',
    4 => 'Social Media Manager, producción y entregables de contenido'
];
$stmt = $pdo->prepare("UPDATE roles SET description = ? WHERE id = ?");
foreach ($roles as $id => $desc) {
    $stmt->execute([$desc, $id]);
}

// Fix specialties
$specialties = [
    1 => ['name' => 'Editor de Videos', 'desc' => 'Edición de reels, videos de propiedades y walkthroughs'],
    2 => ['name' => 'Editor de Carruseles', 'desc' => 'Diseño de carruseles de propiedades e infografías'],
    3 => ['name' => 'Email Marketing', 'desc' => 'Campañas y newsletters para base de datos de Realtors'],
    4 => ['name' => 'Bloggers', 'desc' => 'Redacción de artículos de blog y contenido editorial'],
    5 => ['name' => 'Agendador de Reuniones', 'desc' => 'Coordinación y agenda de llamadas con clientes y prospectos'],
    6 => ['name' => 'Automatizador', 'desc' => 'Automatización de flujos de trabajo, CRM y secuencias']
];
$stmt = $pdo->prepare("UPDATE specialties SET name = ?, description = ? WHERE id = ?");
foreach ($specialties as $id => $data) {
    $stmt->execute([$data['name'], $data['desc'], $id]);
}

// Fix employees
$employees = [
    1 => 'Carlos Mendoza (Jefe)',
    2 => 'Valentina Rossi (Asistente)',
    3 => 'Sofía Herrera (Estratega Mkt)',
    4 => 'Mateo Silva (Social Manager)'
];
$stmt = $pdo->prepare("UPDATE employees SET full_name = ? WHERE id = ?");
foreach ($employees as $id => $name) {
    $stmt->execute([$name, $id]);
}

// Fix realtors
$realtors = [
    1 => 'One Sotheby’s International Realty',
    2 => 'The Jills Zeder Group / Coldwell Banker',
    3 => 'Compass Florida Luxury Division',
    4 => 'Fortune International Group'
];
$stmt = $pdo->prepare("UPDATE realtors SET company_name = ? WHERE id = ?");
foreach ($realtors as $id => $comp) {
    $stmt->execute([$comp, $id]);
}

// Fix contracts
$contracts = [
    1 => ['title' => 'Acuerdo Operativo Audiovisual Anual - One Sothebys Brickell', 'notes' => 'Contrato firmado legalmente y validado.'],
    2 => ['title' => 'Convenio de Producción Mensual Redes - The Jills Zeder', 'notes' => 'PDF subido concluido por gerencia.']
];
$stmt = $pdo->prepare("UPDATE contracts SET contract_title = ?, notes = ? WHERE id = ?");
foreach ($contracts as $id => $data) {
    $stmt->execute([$data['title'], $data['notes'], $id]);
}

// Fix activities
$activities = [
    1 => ['title' => 'Edición Reel Penthouse Brickell Flatiron', 'desc' => 'Montaje con música de tendencia y tomas de balcón al atardecer'],
    2 => ['title' => 'Carrusel 5 Tips de Inversión en South Beach', 'desc' => 'Diseño de 7 láminas en Canva/Photoshop con paleta de colores de Coldwell'],
    3 => ['title' => 'Grabación Dron & Gimbal Mansión Coral Gables', 'desc' => 'Rodaje en locación con Realtor Marcus Sterling a las 9:00 AM'],
    4 => ['title' => 'Edición Video Walkthrough Sunny Isles', 'desc' => 'Recorte de tomas y etalonaje en DaVinci Resolve']
];
$stmt = $pdo->prepare("UPDATE activities SET title = ?, description = ? WHERE id = ?");
foreach ($activities as $id => $data) {
    $stmt->execute([$data['title'], $data['desc'], $id]);
}

// Fix deliverables
$stmt = $pdo->prepare("UPDATE activity_deliverables SET notes = ? WHERE id = 1");
$stmt->execute(['Versión preliminar con corrección de color y música instrumental aprobada.']);

// Fix email_campaigns
$campaigns = [
    1 => ['title' => 'Newsletter Mensual: Oportunidades Pre-construcción Brickell', 'notes' => 'Base de datos compradores calificados de Elena Rostova'],
    2 => ['title' => 'Email Blast: Reducción de Precio Mansión Venetian Islands', 'notes' => 'Filtro contactos VIP Miami Beach']
];
$stmt = $pdo->prepare("UPDATE email_campaigns SET title = ?, notes = ? WHERE id = ?");
foreach ($campaigns as $id => $data) {
    $stmt->execute([$data['title'], $data['notes'], $id]);
}

// Fix meetings
$meetings = [
    1 => ['title' => 'Revisión Estratégica Trimestral con Elena Rostova', 'desc' => 'Alineación de lanzamientos audiovisuales en Brickell'],
    2 => ['title' => 'Kickoff Campaña de Dron con Marcus Sterling', 'desc' => 'Coordinación logística de acceso a propiedad en South Beach']
];
$stmt = $pdo->prepare("UPDATE meetings SET title = ?, description = ? WHERE id = ?");
foreach ($meetings as $id => $data) {
    $stmt->execute([$data['title'], $data['desc'], $id]);
}

echo "4. Migration complete! Checking database for remaining question mark anomalies...\n";

$anomalies = 0;
foreach ($tables as $tbl) {
    $rows = $pdo->query("SELECT * FROM {$tbl}")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        foreach ($row as $k => $v) {
            if (is_string($v) && (str_contains($v, '??') || preg_match('/[a-zA-Z]\?[a-zA-Z]/', $v))) {
                echo "  WARNING: {$tbl}.{$k} (ID {$row['id']}): {$v}\n";
                $anomalies++;
            }
        }
    }
}

if ($anomalies === 0) {
    echo "SUCCESS: All tables converted to utf8mb4 and all accents/special characters fully restored!\n";
} else {
    echo "FOUND {$anomalies} anomalies remaining.\n";
}
