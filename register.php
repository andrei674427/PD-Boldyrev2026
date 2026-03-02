<?php
require_once 'database.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $error = "Имя пользователя и пароль обязательны.";
    } else {

        // Проверяем email (у тебя UNIQUE)
        if ($email) {
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email=?");
            $checkEmail->execute([$email]);
            if ($checkEmail->fetch()) {
                $error = "Email уже используется.";
            }
        }

        if (!$error) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, role)
                VALUES (?, ?, ?, 'user')
            ");
            $stmt->execute([
                $username,
                $email ?: null,
                $hashed
            ]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

            header("Location: index.php");
            exit;
        }
    }
}
?>