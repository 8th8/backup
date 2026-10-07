<?php
session_start();

require_once "../config/database.php";

$error = "";

// Kiểm tra nếu admin đã đăng nhập
if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

// Xử lý khi người dùng gửi form đăng nhập
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Kiểm tra dữ liệu nhập vào
    if (empty($username) || empty($password)) {
        $error = "ユーザー名とパスワードを入力してください。";
    } else {

        // Tìm tài khoản admin trong database
        $sql = "SELECT id, username, password
                FROM admins
                WHERE username = :username
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":username" => $username
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Xác minh mật khẩu đã được mã hóa
        if ($admin && password_verify($password, $admin["password"])) {

            // Tạo session mới sau khi đăng nhập thành công
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "ユーザー名またはパスワードが正しくありません。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>管理者ログイン | Sushi Order</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="admin-login-page">

    <div class="login-container">

        <div class="login-card">

            <div class="login-logo">
                <h1>🍣 Sushi Order</h1>
                <p>管理者ログイン</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message" role="alert">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <div class="form-group">
                    <label for="username">
                        ユーザー名
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="ユーザー名を入力"
                        autocomplete="username"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">
                        パスワード
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="パスワードを入力"
                        autocomplete="current-password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary login-btn">
                    ログイン
                </button>

            </form>

            <div class="login-footer">
                <a href="../index.php">
                    ← トップページへ戻る
                </a>
            </div>

        </div>

    </div>

</body>

</html>