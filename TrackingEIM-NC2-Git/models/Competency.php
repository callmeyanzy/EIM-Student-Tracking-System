<?php
// models/Competency.php
require_once __DIR__ . '/../config/database.php';

class Competency
{
    public static function all()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM competencies ORDER BY code');
        return $stmt->fetchAll();
    }

    public static function find(int $id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM competencies WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create($code, $title, $description)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO competencies (code, title, description) VALUES (:code, :title, :desc)');
        return $stmt->execute([':code'=>$code, ':title'=>$title, ':desc'=>$description]);
    }

    public static function update($id, $code, $title, $description)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE competencies SET code = :code, title = :title, description = :desc WHERE id = :id');
        return $stmt->execute([':code'=>$code, ':title'=>$title, ':desc'=>$description, ':id'=>$id]);
    }

    public static function delete($id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM competencies WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }

    public static function getWithStats()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('
            SELECT c.*, COUNT(sc.student_id) as student_count,
            SUM(CASE WHEN sc.status = "completed" THEN 1 ELSE 0 END) as completed_count
            FROM competencies c
            LEFT JOIN student_competencies sc ON c.id = sc.competency_id
            GROUP BY c.id
            ORDER BY c.code
        ');
        return $stmt->fetchAll();
    }

    public static function getByStudent($studentId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT c.*, sc.status, sc.practical_score, sc.assessment_date, sc.remarks
            FROM competencies c
            LEFT JOIN student_competencies sc ON c.id = sc.competency_id AND sc.student_id = :student_id
            ORDER BY c.code
        ');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public static function getCount()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM competencies');
        $row = $stmt->fetch();
        return (int)$row['cnt'];
    }
}
