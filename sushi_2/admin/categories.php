<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Kiểm tra kết nối database
if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    exit('Database connection is not configured.');
}

// Khởi tạo CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

$message = '';
$error = '';

// Escape dữ liệu trước khi hiển thị HTML
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Chuẩn hóa và kiểm tra tên danh mục
function validateCategoryName($value): string
{
    if (!is_string($value)) {
        throw new InvalidArgumentException('Tên danh mục không hợp lệ.');
    }

    $name = trim($value);

    if ($name === '') {
        throw new InvalidArgumentException('Vui lòng nhập tên danh mục.');
    }

    if (mb_strlen($name, 'UTF-8') > 100) {
        throw new InvalidArgumentException(
            'Tên danh mục không được vượt quá 100 ký tự.'
        );
    }

    return $name;
}

// Xử lý thêm, sửa, xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($postedToken) ||
        !hash_equals($csrfToken, $postedToken)
    ) {
        http_response_code(403);
        exit('Yêu cầu không hợp lệ. Vui lòng tải lại trang.');
    }

    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            // Thêm danh mục
            $name = validateCategoryName($_POST['name'] ?? '');

            $stmt = $pdo->prepare(
                'INSERT INTO categories (name, created_at)
                 VALUES (:name, NOW())'
            );

            $stmt->execute([':name' => $name]);

            $_SESSION['category_flash'] = 'Đã thêm danh mục thành công.';
        } elseif ($action === 'update') {
            // Cập nhật danh mục
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new InvalidArgumentException(
                    'ID danh mục không hợp lệ.'
                );
            }

            $name = validateCategoryName($_POST['name'] ?? '');

            $stmt = $pdo->prepare(
                'UPDATE categories
                 SET name = :name
                 WHERE id = :id'
            );

            $stmt->execute([
                ':name' => $name,
                ':id' => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                $check = $pdo->prepare(
                    'SELECT id FROM categories WHERE id = :id'
                );
                $check->execute([':id' => $id]);

                if (!$check->fetchColumn()) {
                    throw new RuntimeException(
                        'Không tìm thấy danh mục cần sửa.'
                    );
                }
            }

            $_SESSION['category_flash'] = 'Đã cập nhật danh mục thành công.';
        } elseif ($action === 'delete') {
            // Xóa danh mục chỉ khi chưa có món ăn sử dụng
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new InvalidArgumentException(
                    'ID danh mục không hợp lệ.'
                );
            }

            $pdo->beginTransaction();

            $checkCategory = $pdo->prepare(
                'SELECT id
                 FROM categories
                 WHERE id = :id
                 FOR UPDATE'
            );

            $checkCategory->execute([':id' => $id]);

            if (!$checkCategory->fetchColumn()) {
                throw new RuntimeException(
                    'Không tìm thấy danh mục cần xóa.'
                );
            }

            $checkFoods = $pdo->prepare(
                'SELECT COUNT(*)
                 FROM foods
                 WHERE category_id = :id'
            );

            $checkFoods->execute([':id' => $id]);

            if ((int) $checkFoods->fetchColumn() > 0) {
                throw new RuntimeException(
                    'Danh mục đang có món ăn. Hãy chuyển các món sang danh mục khác trước khi xóa.'
                );
            }

            $delete = $pdo->prepare(
                'DELETE FROM categories WHERE id = :id'
            );

            $delete->execute([':id' => $id]);

            $pdo->commit();

            $_SESSION['category_flash'] = 'Đã xóa danh mục thành công.';
        } else {
            throw new InvalidArgumentException(
                'Thao tác không hợp lệ.'
            );
        }

        // Chuyển hướng để tránh gửi lại form khi tải lại trang
        header('Location: categories.php');
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if (
            $e instanceof InvalidArgumentException ||
            $e instanceof RuntimeException
        ) {
            $error = $e->getMessage();
        } elseif ($e instanceof PDOException && $e->getCode() === '23000') {
            $error = 'Tên danh mục đã tồn tại hoặc đang được dữ liệu khác sử dụng.';
        } else {
            error_log('Category management error: ' . $e->getMessage());
            $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
        }
    }
}

// Hiển thị thông báo sau khi thao tác thành công
if (isset($_SESSION['category_flash'])) {
    $message = $_SESSION['category_flash'];
    unset($_SESSION['category_flash']);
}

// Lấy danh sách danh mục và số món thuộc từng danh mục
try {
    $stmt = $pdo->query(
        'SELECT c.id, c.name, c.created_at,
                COUNT(f.id) AS food_count
         FROM categories c
         LEFT JOIN foods f ON f.category_id = c.id
         GROUP BY c.id, c.name, c.created_at
         ORDER BY c.id DESC'
    );

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Category list error: ' . $e->getMessage());
    http_response_code(500);
    exit('Không thể tải danh sách danh mục. Vui lòng thử lại sau.');
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý danh mục | 寿司 博多魚がし</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .category-admin {
            width: min(100% - 32px, 1100px);
            margin: 30px auto;
        }

        .category-form {
            margin-bottom: 28px;
        }

        .category-form-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .category-form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .category-name {
            font-weight: bold;
        }

        .action-cell {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .inline-form {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .inline-form input {
            width: 160px;
        }

        .category-count {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 16px;
            background: #eee8dc;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .category-form-row {
                flex-direction: column;
                align-items: stretch;
            }

            .inline-form {
                flex-wrap: wrap;
            }

            .inline-form input {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <header class="admin-header">
        <div class="container">
            <strong>寿司 博多魚がし — 管理画面</strong>

            <nav class="site-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="orders.php">Đơn hàng</a>
                <a href="foods.php">Món ăn</a>
                <a href="categories.php">Danh mục</a>
                <a href="login.php">Đăng nhập</a>
            </nav>
        </div>
    </header>

    <main class="category-admin">

        <div class="page-header">
            <h1>Quản lý danh mục món ăn</h1>
            <p>Thêm, chỉnh sửa và quản lý các danh mục trong menu.</p>
        </div>

        <?php if ($message !== ''): ?>
            <div class="alert alert-success" role="status">
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <section class="card category-form">
            <h2>Thêm danh mục mới</h2>

            <form method="POST" action="categories.php">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="create">

                <div class="category-form-row">
                    <div class="form-group">
                        <label class="form-label" for="category-name">
                            Tên danh mục
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            id="category-name"
                            name="name"
                            maxlength="100"
                            required
                            placeholder="Ví dụ: Sushi, Sashimi, Đồ uống">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        + Thêm danh mục
                    </button>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Danh sách danh mục</h2>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Số món</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    Chưa có danh mục nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= (int) $category['id'] ?></td>

                                    <td>
                                        <span class="category-name">
                                            <?= e($category['name']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="category-count">
                                            <?= (int) $category['food_count'] ?> món
                                        </span>
                                    </td>

                                    <td>
                                        <?= e($category['created_at'] ?? '') ?>
                                    </td>

                                    <td>
                                        <div class="action-cell">
                                            <form
                                                method="POST"
                                                action="categories.php"
                                                class="inline-form">
                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e($csrfToken) ?>">

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="update">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $category['id'] ?>">

                                                <input
                                                    class="form-control"
                                                    type="text"
                                                    name="name"
                                                    maxlength="100"
                                                    required
                                                    aria-label="Tên mới của danh mục <?= e($category['name']) ?>"
                                                    value="<?= e($category['name']) ?>">

                                                <button
                                                    type="submit"
                                                    class="btn btn-secondary">
                                                    Sửa
                                                </button>
                                            </form>

                                            <?php if ((int) $category['food_count'] === 0): ?>
                                                <form
                                                    method="POST"
                                                    action="categories.php"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?= e($csrfToken) ?>">

                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="delete">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $category['id'] ?>">

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger">
                                                        Xóa
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    disabled
                                                    title="Hãy chuyển món ăn sang danh mục khác trước">
                                                    Xóa
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>

</html>