<?php
// models/Assessment.php
require_once __DIR__ . '/../config/database.php';

class Assessment
{
    public static function create($studentId, $competencyId, $assessmentType, $score, $result, $assessedBy, $remarks = null)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO assessments (student_id, competency_id, assessment_type, score, result, assessed_by, remarks, assessed_at)
            VALUES (:student_id, :competency_id, :assessment_type, :score, :result, :assessed_by, :remarks, NOW())
        ');
        return $stmt->execute([
            ':student_id' => $studentId,
            ':competency_id' => $competencyId,
            ':assessment_type' => $assessmentType,
            ':score' => $score,
            ':result' => $result,
            ':assessed_by' => $assessedBy,
            ':remarks' => $remarks
        ]);
    }

    public static function getByStudent($studentId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT a.*, c.code as competency_code, c.title as competency_title, u.name as assessed_by_name
            FROM assessments a
            JOIN competencies c ON a.competency_id = c.id
            LEFT JOIN users u ON a.assessed_by = u.id
            WHERE a.student_id = :student_id
            ORDER BY a.assessed_at DESC
        ');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public static function getRecent($limit = 10)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT a.*, s.name as student_name, c.code as competency_code, c.title as competency_title
            FROM assessments a
            JOIN students s ON a.student_id = s.id
            JOIN competencies c ON a.competency_id = c.id
            ORDER BY a.assessed_at DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getByCompetency($competencyId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT a.*, s.name as student_name, u.name as assessed_by_name
            FROM assessments a
            JOIN students s ON a.student_id = s.id
            LEFT JOIN users u ON a.assessed_by = u.id
            WHERE a.competency_id = :competency_id
            ORDER BY a.assessed_at DESC
        ');
        $stmt->execute([':competency_id' => $competencyId]);
        return $stmt->fetchAll();
    }

    public static function getPassRateStats()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('
            SELECT 
                COUNT(*) as total_assessments,
                SUM(CASE WHEN result = "pass" THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN result = "fail" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN result = "pending" THEN 1 ELSE 0 END) as pending
            FROM assessments
        ');
        return $stmt->fetch();
    }

    public static function getPassRatePercentage()
    {
        $stats = self::getPassRateStats();
        if ($stats['total_assessments'] == 0) {
            return 0;
        }
        return round(($stats['passed'] / $stats['total_assessments']) * 100);
    }

    public static function getStudentAssessmentStats($studentId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT 
                COUNT(*) as total_assessments,
                SUM(CASE WHEN result = "pass" THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN result = "fail" THEN 1 ELSE 0 END) as failed,
                AVG(score) as average_score
            FROM assessments
            WHERE student_id = :student_id
        ');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetch();
    }
}
