<?php
// models/Batch.php
require_once __DIR__ . '/../config/database.php';

class Batch
{
    public static function all()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM batches ORDER BY name');
        return $stmt->fetchAll();
    }

    public static function find(int $id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM batches WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create($name)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO batches (name) VALUES (:name)');
        return $stmt->execute([':name'=>$name]);
    }

    public static function update($id, $name)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE batches SET name = :name WHERE id = :id');
        return $stmt->execute([':name'=>$name, ':id'=>$id]);
    }

    public static function delete($id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM batches WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }
}
