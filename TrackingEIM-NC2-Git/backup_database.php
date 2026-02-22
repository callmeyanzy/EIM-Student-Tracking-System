<?php
// backup_database.php - Export database to SQL file
require_once __DIR__ . '/config/database.php';

function backupDatabase($filename = null) {
    if (!$filename) {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    }
    
    $pdo = Database::getConnection();
    $tables = [];
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $output = "-- EIM NC II Database Backup\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        // Get CREATE TABLE
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $output .= "-- Table: $table\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $output .= $row[1] . ";\n\n";
        
        // Get data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $rowValues = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } else {
                        $rowValues[] = $pdo->quote($value);
                    }
                }
                $values[] = "(" . implode(', ', $rowValues) . ")";
            }
            $output .= implode(",\n", $values) . ";\n\n";
        }
    }
    
    $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Save to file
    file_put_contents($filename, $output);
    return $filename;
}

// Run backup
$backupFile = backupDatabase();
echo "Database backup created: $backupFile\n";
echo "File size: " . number_format(filesize($backupFile)) . " bytes\n";
?>
