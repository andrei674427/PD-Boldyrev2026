<?php
class News {
    private $conn;
    private $table = 'news';

    public $id;
    public $title;
    public $content;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ===== CREATE =====
    public function create() {
        $sql = 'INSERT INTO ' . $this->table .
               ' (title, content)
                VALUES (:title, :content)';

        $stmt = $this->conn->prepare($sql);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // ===== READ ALL =====
    public function getAll() {
        $sql = 'SELECT * FROM ' . $this->table .
               ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // ===== READ ONE =====
    public function getById($id) {
        $sql = 'SELECT * FROM ' . $this->table .
               ' WHERE id = :id LIMIT 1';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // ===== UPDATE =====
    public function update() {
        $sql = 'UPDATE ' . $this->table . ' SET
                title = :title,
                content = :content
                WHERE id = :id';

        $stmt = $this->conn->prepare($sql);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // ===== DELETE =====
    public function delete($id) {
        $sql = 'DELETE FROM ' . $this->table .
               ' WHERE id = :id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
