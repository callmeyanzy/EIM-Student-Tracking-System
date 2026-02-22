<?php
// models/Student.php
require_once __DIR__ . '/../config/database.php';

class Student
{
    public static function getTotalStudents(): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM students');
        $row = $stmt->fetch();
        return (int)$row['cnt'];
    }

    public static function all()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('
            SELECT s.*, b.name as batch_name 
            FROM students s 
            LEFT JOIN batches b ON s.batch_id = b.id 
            ORDER BY s.name
        ');
        return $stmt->fetchAll();
    }

    public static function find(int $id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT s.*, b.name as batch_name 
            FROM students s 
            LEFT JOIN batches b ON s.batch_id = b.id 
            WHERE s.id = :id
        ');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create($name, $email, $batchId, $enrollmentDate = null)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO students (name, email, batch_id, enrollment_date, status, tesda_qualification) 
            VALUES (:name, :email, :batch_id, :enrollment_date, "active", "EIM NC II")
        ');
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':batch_id' => $batchId,
            ':enrollment_date' => $enrollmentDate
        ]);
    }

    public static function update($id, $name, $email, $batchId, $status, $enrollmentDate = null)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            UPDATE students 
            SET name = :name, email = :email, batch_id = :batch_id, status = :status, enrollment_date = :enrollment_date 
            WHERE id = :id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':batch_id' => $batchId,
            ':status' => $status,
            ':enrollment_date' => $enrollmentDate
        ]);
    }

    public static function delete($id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function getByBatch($batchId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT s.*, b.name as batch_name 
            FROM students s 
            LEFT JOIN batches b ON s.batch_id = b.id 
            WHERE s.batch_id = :batch_id 
            ORDER BY s.name
        ');
        $stmt->execute([':batch_id' => $batchId]);
        return $stmt->fetchAll();
    }

    public static function getProgressStats($studentId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT 
                COUNT(*) as total_competencies,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = "not_started" THEN 1 ELSE 0 END) as not_started
            FROM student_competencies 
            WHERE student_id = :student_id
        ');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetch();
    }

    public static function getCompetencyStatus($studentId)
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

    public static function getOverallProgressPercentage($studentId)
    {
        $stats = self::getProgressStats($studentId);
        if ($stats['total_competencies'] == 0) {
            return 0;
        }
        return round(($stats['completed'] / $stats['total_competencies']) * 100);
    }

    public static function getActiveStudentsCount()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM students WHERE status = "active"');
        $row = $stmt->fetch();
        return (int)$row['cnt'];
    }
}
