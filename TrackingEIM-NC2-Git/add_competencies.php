<?php
require_once __DIR__ . '/config/database.php';

$competencies = [
    [
        'code' => 'EIM-001',
        'title' => 'Install electrical lighting systems, auxiliary outlets, and lighting fixtures',
        'description' => 'Install and configure electrical lighting systems including auxiliary outlets and various lighting fixtures according to electrical standards and safety regulations.'
    ],
    [
        'code' => 'EIM-002',
        'title' => 'Install electrical protective devices for lighting and grounding systems',
        'description' => 'Install protective devices such as circuit breakers, fuses, and grounding systems to ensure electrical safety and compliance with standards.'
    ],
    [
        'code' => 'EIM-003',
        'title' => 'Install wiring devices for floor and wall-mounted outlets, lighting fixtures, and switches',
        'description' => 'Install and wire various electrical devices including floor and wall-mounted outlets, lighting fixtures, and switches following proper wiring techniques.'
    ],
    [
        'code' => 'EIM-004',
        'title' => 'Apply quality standards',
        'description' => 'Apply and maintain quality standards in all electrical installation work to ensure reliability, safety, and compliance with industry regulations.'
    ],
    [
        'code' => 'EIM-005',
        'title' => 'Perform computer operations',
        'description' => 'Perform basic computer operations including documentation, reporting, and using software tools relevant to electrical installation and maintenance work.'
    ],
    [
        'code' => 'EIM-006',
        'title' => 'Perform mensuration and calculation',
        'description' => 'Perform accurate measurements and calculations required for electrical installations including load calculations, material estimates, and circuit designs.'
    ]
];

$pdo = Database::getConnection();
$stmt = $pdo->prepare('INSERT IGNORE INTO competencies (code, title, description) VALUES (:code, :title, :description)');

echo "Adding competencies...\n\n";

foreach ($competencies as $comp) {
    try {
        $stmt->execute([':code' => $comp['code'], ':title' => $comp['title'], ':description' => $comp['description']]);
        echo "✓ {$comp['code']}: {$comp['title']}\n";
    } catch (PDOException $e) {
        echo "✗ {$comp['code']}: Already exists or error - {$e->getMessage()}\n";
    }
}

echo "\nDone!\n";
