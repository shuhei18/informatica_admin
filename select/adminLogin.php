<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        $error = 'ユーザー名とパスワードを入力してください。';
    } else {
        try {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                header('Location: admin.php');
                exit;
            } else {
                $error = 'ユーザー名またはパスワードが間違っています。';
            }
        } catch (PDOException $e) {
            $error = 'エラーが発生しました。';
            error_log("Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>管理者ログイン</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #e9ecef;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        width: 400px;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 8px;
        color: #495057;
        font-weight: 600;
    }

    input[type="text"],
    input[type="password"] {
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 16px;
        color: #495057;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        border-color: #80bdff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
    }

    .button-container {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        gap: 10px;
    }

    button,
    .admin-button {
        padding: 10px;
        background-color: #6c766e;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        width: 100%; /* ボタンの幅を同じにするために100%に設定 */
        font-size: 16px;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #218838;
    }

    .admin-button {
        background-color: #0069d9;
    }

    .admin-button:hover {
        background-color: #0056b3;
    }

    .admin-button {
        text-decoration: none; /* 下線を削除 */
    }

    p.error {
        text-align: center;
        color: red;
        font-weight: bold;
        margin-bottom: 15px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>管理者ログイン</h1>
        <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form action="adminLogin.php" method="post">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" required>

            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>

            <div class="button-container">
                <button type="submit">ログイン</button>
            </div>
        </form>
    </div>
</body>

</html>
