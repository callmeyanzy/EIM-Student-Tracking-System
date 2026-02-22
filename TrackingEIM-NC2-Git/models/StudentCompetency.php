<?php
// models/StudentCompetency.php
require_once __DIR__ . '/../config/database.php';

class StudentCompetency
{
    public static function getByStudent($studentId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT sc.*, c.code, c.title, c.description
            FROM student_competencies sc
            JOIN competencies c ON sc.competency_id = c.id
            WHERE sc.student_id = :student_id
            ORDER BY c.code
        ');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public static function getByCompetency($competencyId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT sc.*, s.name as student_name, s.email
            FROM student_competencies sc
            JOIN students s ON sc.student_id = s.id
            WHERE sc.competency_id = :competency_id
            ORDER BY s.name
        ');
        $stmt->execute([':competency_id' => $competencyId]);
        return $stmt->fetchAll();
    }

    public static function updateStatus($studentId, $competencyId, $status, $practicalScore = null, $remarks = null)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO student_competencies (student_id, competency_id, status, practical_score, remarks, assessment_date)
            VALUES (:student_id, :competency_id, :status, :practical_score, :remarks, CURDATE())
            ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            practical_score = VALUES(practical_score),
            remarks = VALUES(remarks),
            assessment_date = CURDATE()
        ');
        return $stmt->execute([
            ':student_id' => $studentId,
            ':competency_id' => $competencyId,
            ':status' => $status,
            ':practical_score' => $practicalScore,
            ':remarks' => $remarks
        ]);
    }

    public static function getBatchProgress($batchId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT 
                s.id as student_id,
                s.name as student_name,
                c.id as competency_id,
                c.code as competency_code,
                c.title as competency_title,
                sc.status,
                sc.practical_score
            FROM students s
            CROSS JOIN competencies c
            LEFT JOIN student_competencies sc ON s.id = sc.student_id AND c.id = sc.competency_id
            WHERE s.batch_id = :batch_id
            ORDER BY s.name, c.code
        ');
        $stmt->execute([':batch_id' => $batchId]);
        return $stmt->fetchAll();
    }

    public static function getCompletionStats()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('
            SELECT 
                COUNT(*) as total_records,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = "not_started" THEN 1 ELSE 0 END) as not_started
            FROM student_competencies
        ');
        return $stmt->fetch();
    }

    public static function getCompletedCompetenciesCount()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM student_competencies WHERE status = "completed"');
        $row = $stmt->fetch();
        return (int)$row['cnt'];
    }

    public static function initializeStudentCompetencies($studentId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT IGNORE INTO student_competencies (student_id, competency_id, status)
            SELECT :student_id, id, "not_started" FROM competencies
        ');
        return $stmt->execute([':student_id' => $studentId]);
    }
}
