<?php
class Database {
    private $pdo;

    public function __construct($host, $db, $user, $pass) {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function fetchMembers() {
        $stmt = $this->pdo->query("SELECT Id, Name, ParentId FROM Members ORDER BY CreatedDate ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertMember($name, $parentId) {
        $stmt = $this->pdo->prepare("INSERT INTO Members (Name, ParentId) VALUES (:name, :parentId)");
        $stmt->execute([':name' => $name, ':parentId' => $parentId]);
        return $this->pdo->lastInsertId();
    }

    public function updateMember($id, $name) {
    $stmt = $this->pdo->prepare("UPDATE Members SET Name = :name WHERE Id = :id");
    $stmt->execute([':name' => $name, ':id' => $id]);
}

public function deleteMember($id) {
    // Delete children recursively or set their ParentId to null if you don't want cascade delete
    $stmt = $this->pdo->prepare("DELETE FROM Members WHERE Id = :id");
    $stmt->execute([':id' => $id]);
}
}
?>
