<?php
// scripts/seed_sample_data.php
// Run this to populate the database with sample data for testing

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Batch.php';
require_once __DIR__ . '/../models/Competency.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/StudentCompetency.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/UserModel.php';

// Ensure Database class is loaded
if (!class_exists('Database')) {
    die("Database configuration not loaded properly.");
}

echo "=== Seeding Sample Data for EIM NC II Tracker ===\n\n";

// Create sample batches
echo "Creating batches...\n";
$batches = [
    ['name' => '2024-A', 'year' => 2024],
    ['name' => '2024-B', 'year' => 2024],
    ['name' => '2025-A', 'year' => 2025]
];

foreach ($batches as $batch) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('INSERT IGNORE INTO batches (name, year) VALUES (:name, :year)');
    $stmt->execute([':name' => $batch['name'], ':year' => $batch['year']]);
    echo "  - {$batch['name']}\n";
}

// Create sample competencies
echo "\nCreating competencies...\n";
$competencies = [
    [
        'code' => 'EIM-001',
        'title' => 'Install Electrical Wiring',
        'description' => 'Install electrical wiring systems in residential and commercial buildings following safety standards and electrical codes.'
    ],
    [
        'code' => 'EIM-002',
        'title' => 'Perform Roughing-in Activities',
        'description' => 'Perform roughing-in activities including conduit installation, box mounting, and wire pulling.'
    ],
    [
        'code' => 'EIM-003',
        'title' => 'Install Lighting System',
        'description' => 'Install various lighting systems including switches, outlets, fixtures, and control systems.'
    ],
    [
        'code' => 'EIM-004',
        'title' => 'Maintain Electrical System',
        'description' => 'Perform preventive maintenance and troubleshooting on electrical systems and equipment.'
    ],
    [
        'code' => 'EIM-005',
        'title' => 'Troubleshoot Electrical Circuits',
        'description' => 'Diagnose and repair faults in electrical circuits using proper testing equipment.'
    ]
];

foreach ($competencies as $comp) {
    try {
        Competency::create($comp['code'], $comp['title'], $comp['description']);
        echo "  - {$comp['code']}: {$comp['title']}\n";
    } catch (PDOException $e) {
        echo "  - {$comp['code']}: Already exists\n";
    }
}

// Get batch IDs
$pdo = Database::getConnection();
$batchIds = $pdo->query('SELECT id FROM batches')->fetchAll(PDO::FETCH_COLUMN);

// Create sample students
echo "\nCreating students...\n";
$students = [
    ['name' => 'Juan Dela Cruz', 'email' => 'juan.cruz@example.com', 'batch_idx' => 0],
    ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'batch_idx' => 0],
    ['name' => 'Pedro Reyes', 'email' => 'pedro.reyes@example.com', 'batch_idx' => 0],
    ['name' => 'Ana Garcia', 'email' => 'ana.garcia@example.com', 'batch_idx' => 1],
    ['name' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@example.com', 'batch_idx' => 1],
    ['name' => 'Elena Torres', 'email' => 'elena.torres@example.com', 'batch_idx' => 1],
    ['name' => 'Miguel Bautista', 'email' => 'miguel.bautista@example.com', 'batch_idx' => 2],
    ['name' => 'Sofia Ramos', 'email' => 'sofia.ramos@example.com', 'batch_idx' => 2],
];

$studentIds = [];
foreach ($students as $student) {
    // Check if user already exists
    $existingUser = UserModel::findByEmail($student['email']);
    if ($existingUser) {
        echo "  - {$student['name']}: Already exists (skipping)\n";
        continue;
    }
    
    // Create user account
    $defaultPassword = password_hash('student123', PASSWORD_DEFAULT);
    UserModel::create($student['name'], $student['email'], $defaultPassword, 'student');
    $user = UserModel::findByEmail($student['email']);
    
    // Create student record
    $batchId = $batchIds[$student['batch_idx']] ?? $batchIds[0];
    $enrollmentDate = date('Y-m-d', strtotime('-' . rand(30, 365) . ' days'));
    
    Student::create($student['name'], $student['email'], $batchId, $enrollmentDate);
    $studentId = $pdo->lastInsertId();
    
    // Link to user
    $stmt = $pdo->prepare('UPDATE students SET user_id = :user_id WHERE id = :id');
    $stmt->execute([':user_id' => $user['id'], ':id' => $studentId]);
    
    // Initialize competencies
    StudentCompetency::initializeStudentCompetencies($studentId);
    
    $studentIds[] = $studentId;
    echo "  - {$student['name']} ({$student['email']})\n";
}

// Update some progress randomly
echo "\nGenerating progress data...\n";
$competencyIds = $pdo->query('SELECT id FROM competencies')->fetchAll(PDO::FETCH_COLUMN);
$statuses = ['not_started', 'in_progress', 'completed'];

foreach ($studentIds as $studentId) {
    foreach ($competencyIds as $competencyId) {
        // Random status with weighted probability
        $rand = rand(1, 100);
        if ($rand <= 40) {
            $status = 'completed';
        } elseif ($rand <= 70) {
            $status = 'in_progress';
        } else {
            $status = 'not_started';
        }
        
        if ($status !== 'not_started') {
            $score = $status === 'completed' ? rand(75, 100) : rand(50, 74);
            StudentCompetency::updateStatus($studentId, $competencyId, $status, $score, 'Sample progress data');
            
            // Create assessment record for completed/in_progress
            $result = $score >= 75 ? 'pass' : 'fail';
            $assessmentType = ['Practical', 'Written', 'Project'][rand(0, 2)];
            Assessment::create($studentId, $competencyId, $assessmentType, $score, $result, 1, 'Auto-generated sample assessment');
        }
    }
}
echo "  - Progress data generated for " . count($studentIds) . " students\n";

echo "\n=== Sample Data Seeding Complete ===\n";
echo "\nSample login credentials:\n";
echo "  Admin: admin@example.com / admin123\n";
echo "  Students: [student-email] / student123\n";
