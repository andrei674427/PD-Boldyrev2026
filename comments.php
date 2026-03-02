<?php
class Comments {
    private $conn;
    private $table = 'comments';

    public $id;
    public $news_id;
    public $user_name;
    public $text;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $sql = 'INSERT INTO ' . $this->table .
               ' (news_id, user_name, text)
                VALUES (:news_id, :user_name, :text)';

        $stmt = $this->conn->prepare($sql);

        $this->user_name = htmlspecialchars(strip_tags($this->user_name));
        $this->text = htmlspecialchars(strip_tags($this->text));

        $stmt->bindParam(':news_id', $this->news_id);
        $stmt->bindParam(':user_name', $this->user_name);
        $stmt->bindParam(':text', $this->text);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getAll() {
        $sql = 'SELECT * FROM ' . $this->table .
               ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $sql = 'SELECT * FROM ' . $this->table .
               ' WHERE id = :id LIMIT 1';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->news_id = $row['news_id'];
            $this->user_name = $row['user_name'];
            $this->text = $row['text'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function update() {
        $sql = 'UPDATE ' . $this->table . ' SET
                text = :text
                WHERE id = :id';

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':text', $this->text);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete($id) {
        $sql = 'DELETE FROM ' . $this->table .
               ' WHERE id = :id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // ===== BONUS =====
    public function getByNewsId($news_id) {
        $sql = 'SELECT * FROM ' . $this->table .
               ' WHERE news_id = :news_id
                 ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':news_id', $news_id);
        $stmt->execute();
        return $stmt;
    }
}
