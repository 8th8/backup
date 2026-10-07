<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
*/

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

$allowedActions = ['create', 'update', 'delete'];
$errors = [];
$success = '';

/*
|--------------------------------------------------------------------------
| CSRF token
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| Get categories
|--------------------------------------------------------------------------
*/

$categoryStmt = $pdo->query(
    'SELECT id, name FROM categories ORDER BY name ASC'
);

$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Handle form submissions
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $errors[] = 'セキュリティトークンが無効です。ページを再読み込みしてください。';
    } else {
        $action = $_POST['action'] ?? '';

        if (!in_array($action, $allowedActions, true)) {
            $errors[] = '不正な操作です。';
        } else {
            try {
                /*
                |------------------------------------------------------------------
                | Create food
                |------------------------------------------------------------------
                */

                if ($action === 'create') {
                    $name = trim($_POST['name'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $categoryId = (int) ($_POST['category_id'] ?? 0);
                    $priceInput = $_POST['price'] ?? '';
                    $image = trim($_POST['image'] ?? '');
                    $isAvailable = isset($_POST['is_available']) ? 1 : 0;

                    if ($name === '' || mb_strlen($name) > 255) {
                        $errors[] = '料理名を入力してください（255文字以内）。';
                    }

                    if ($categoryId <= 0) {
                        $errors[] = 'カテゴリーを選択してください。';
                    }

                    if (
                        !is_numeric($priceInput) ||
                        (float) $priceInput < 0 ||
                        (float) $priceInput > 99999999 ||
                        floor((float) $priceInput) !== (float) $priceInput
                    ) {
                        $errors[] = '価格は0以上の整数で入力してください。';
                    }

                    if (mb_strlen($description) > 2000) {
                        $errors[] = '説明は2000文字以内で入力してください。';
                    }

                    if (mb_strlen($image) > 500) {
                        $errors[] = '画像パスは500文字以内で入力してください。';
                    }

                    $categoryCheck = $pdo->prepare(
                        'SELECT COUNT(*) FROM categories WHERE id = ?'
                    );
                    $categoryCheck->execute([$categoryId]);

                    if (!$categoryCheck->fetchColumn()) {
                        $errors[] = '選択したカテゴリーが存在しません。';
                    }

                    if (empty($errors)) {
                        $stmt = $pdo->prepare(
                            'INSERT INTO foods
                                (category_id, name, description, price, image, is_available)
                             VALUES
                                (:category_id, :name, :description, :price, :image, :is_available)'
                        );

                        $stmt->execute([
                            ':category_id' => $categoryId,
                            ':name' => $name,
                            ':description' => $description,
                            ':price' => (int) $priceInput,
                            ':image' => $image !== '' ? $image : null,
                            ':is_available' => $isAvailable,
                        ]);

                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                        header('Location: foods.php?success=created');
                        exit;
                    }
                }

                /*
                |------------------------------------------------------------------
                | Update food
                |------------------------------------------------------------------
                */

                if ($action === 'update') {
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    $name = trim($_POST['name'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $categoryId = (int) ($_POST['category_id'] ?? 0);
                    $priceInput = $_POST['price'] ?? '';
                    $image = trim($_POST['image'] ?? '');
                    $isAvailable = isset($_POST['is_available']) ? 1 : 0;

                    if (!$id || $id <= 0) {
                        $errors[] = '料理IDが無効です。';
                    }

                    if ($name === '' || mb_strlen($name) > 255) {
                        $errors[] = '料理名を入力してください（255文字以内）。';
                    }

                    if ($categoryId <= 0) {
                        $errors[] = 'カテゴリーを選択してください。';
                    }

                    if (
                        !is_numeric($priceInput) ||
                        (float) $priceInput < 0 ||
                        (float) $priceInput > 99999999 ||
                        floor((float) $priceInput) !== (float) $priceInput
                    ) {
                        $errors[] = '価格は0以上の整数で入力してください。';
                    }

                    if (mb_strlen($description) > 2000) {
                        $errors[] = '説明は2000文字以内で入力してください。';
                    }

                    if (mb_strlen($image) > 500) {
                        $errors[] = '画像パスは500文字以内で入力してください。';
                    }

                    $categoryCheck = $pdo->prepare(
                        'SELECT COUNT(*) FROM categories WHERE id = ?'
                    );
                    $categoryCheck->execute([$categoryId]);

                    if (!$categoryCheck->fetchColumn()) {
                        $errors[] = '選択したカテゴリーが存在しません。';
                    }

                    $foodCheck = $pdo->prepare(
                        'SELECT COUNT(*) FROM foods WHERE id = ?'
                    );
                    $foodCheck->execute([$id]);

                    if (!$foodCheck->fetchColumn()) {
                        $errors[] = '編集する料理が見つかりません。';
                    }

                    if (empty($errors)) {
                        $stmt = $pdo->prepare(
                            'UPDATE foods
                             SET category_id = :category_id,
                                 name = :name,
                                 description = :description,
                                 price = :price,
                                 image = :image,
                                 is_available = :is_available
                             WHERE id = :id'
                        );

                        $stmt->execute([
                            ':category_id' => $categoryId,
                            ':name' => $name,
                            ':description' => $description,
                            ':price' => (int) $priceInput,
                            ':image' => $image !== '' ? $image : null,
                            ':is_available' => $isAvailable,
                            ':id' => $id,
                        ]);

                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                        header('Location: foods.php?success=updated');
                        exit;
                    }
                }

                /*
                |------------------------------------------------------------------
                | Delete food
                |------------------------------------------------------------------
                */

                if ($action === 'delete') {
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

                    if (!$id || $id <= 0) {
                        $errors[] = '料理IDが無効です。';
                    } else {
                        $stmt = $pdo->prepare(
                            'DELETE FROM foods WHERE id = ?'
                        );
                        $stmt->execute([$id]);

                        if ($stmt->rowCount() === 0) {
                            $errors[] = '削除する料理が見つかりません。';
                        } else {
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                            header('Location: foods.php?success=deleted');
                            exit;
                        }
                    }
                }
            } catch (PDOException $exception) {
                error_log($exception->getMessage());
                $errors[] = 'データベースエラーが発生しました。データベース構造を確認してください。';
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Success messages
|--------------------------------------------------------------------------
*/

$successMessages = [
    'created' => '料理を登録しました。',
    'updated' => '料理情報を更新しました。',
    'deleted' => '料理を削除しました。',
];

if (isset($_GET['success'])) {
    $success = $successMessages[$_GET['success']] ?? '';
}

/*
|--------------------------------------------------------------------------
| Get food for editing
|--------------------------------------------------------------------------
*/

$editFood = null;

if (isset($_GET['edit'])) {
    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT);

    if ($editId && $editId > 0) {
        $stmt = $pdo->prepare(
            'SELECT * FROM foods WHERE id = ?'
        );
        $stmt->execute([$editId]);
        $editFood = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

/*
|--------------------------------------------------------------------------
| Get food list
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$categoryFilter = filter_var(
    $_GET['category'] ?? '',
    FILTER_VALIDATE_INT
);

$sql = '
    SELECT
        f.id,
        f.name,
        f.description,
        f.price,
        f.image,
        f.is_available,
        f.category_id,
        c.name AS category_name
    FROM foods f
    LEFT JOIN categories c ON f.category_id = c.id
';

$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = '(f.name LIKE :search OR f.description LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

if ($categoryFilter && $categoryFilter > 0) {
    $conditions[] = 'f.category_id = :category_id';
    $params[':category_id'] = $categoryFilter;
}

if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY f.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Form defaults
|--------------------------------------------------------------------------
*/

$formFood = $editFood ?: [
    'id' => '',
    'name' => '',
    'description' => '',
    'category_id' => '',
    'price' => '',
    'image' => '',
    'is_available' => 1,
];

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>料理管理 | 寿司 博多魚がし</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .admin-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .admin-nav a {
            padding: 10px 15px;
            background: #f1f1f1;
            color: #222;
            text-decoration: none;
            border-radius: 6px;
        }

        .admin-nav a.active {
            background: #b91c1c;
            color: white;
        }

        .admin-panel {
            background: white;
            padding: 22px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .food-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 9px 13px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #b91c1c;
            color: white;
        }

        .btn-edit {
            background: #2563eb;
            color: white;
        }

        .btn-delete {
            background: #991b1b;
            color: white;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .notice {
            padding: 12px 15px;
            margin-bottom: 18px;
            border-radius: 6px;
        }

        .notice-success {
            background: #dcfce7;
            color: #166534;
        }

        .notice-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .filter-form input,
        .filter-form select {
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .food-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .food-table th,
        .food-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: middle;
        }

        .food-table th {
            background: #f9fafb;
        }

        .food-thumb {
            width: 65px;
            height: 55px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-available {
            color: #15803d;
            font-weight: bold;
        }

        .status-unavailable {
            color: #b91c1c;
            font-weight: bold;
        }

        .row-actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .row-actions form {
            margin: 0;
        }

        @media (max-width: 650px) {
            .admin-container {
                padding: 12px;
            }

            .food-form {
                grid-template-columns: 1fr;
            }

            .form-group.full,
            .form-actions {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">

        <header>
            <h1>料理管理</h1>
            <p>寿司 博多魚がし — 管理画面</p>
        </header>

        <nav class="admin-nav">
            <a href="dashboard.php">ダッシュボード</a>
            <a href="orders.php">注文管理</a>
            <a href="foods.php" class="active">料理管理</a>
            <a href="categories.php">カテゴリー管理</a>
        </nav>

        <?php if ($success !== ''): ?>
            <div class="notice notice-success">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="notice notice-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="admin-panel">
            <h2>
                <?= $editFood ? '料理情報を編集' : '新しい料理を登録' ?>
            </h2>

            <?php if (empty($categories)): ?>
                <p>カテゴリーがありません。先にカテゴリーを登録してください。</p>
                <a href="categories.php" class="btn btn-primary">
                    カテゴリー管理へ
                </a>
            <?php else: ?>
                <form method="post" action="foods.php<?= $editFood ? '?edit=' . (int) $editFood['id'] : '' ?>">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                    <input
                        type="hidden"
                        name="action"
                        value="<?= $editFood ? 'update' : 'create' ?>">

                    <?php if ($editFood): ?>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $editFood['id'] ?>">
                    <?php endif; ?>

                    <div class="food-form">
                        <div class="form-group">
                            <label for="name">料理名 *</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                maxlength="255"
                                required
                                value="<?= e($formFood['name']) ?>"
                                placeholder="例：まぐろづくし">
                        </div>

                        <div class="form-group">
                            <label for="category_id">カテゴリー *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">選択してください</option>
                                <?php foreach ($categories as $category): ?>
                                    <option
                                        value="<?= (int) $category['id'] ?>"
                                        <?= (string) $formFood['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">価格（円） *</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                min="0"
                                max="99999999"
                                step="1"
                                required
                                value="<?= e($formFood['price']) ?>"
                                placeholder="例：1200">
                        </div>

                        <div class="form-group">
                            <label for="image">画像パス</label>
                            <input
                                type="text"
                                id="image"
                                name="image"
                                maxlength="500"
                                value="<?= e($formFood['image'] ?? '') ?>"
                                placeholder="例：assets/images/maguro.png">
                        </div>

                        <div class="form-group full">
                            <label for="description">料理の説明</label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                maxlength="2000"
                                placeholder="料理についての説明を入力してください"><?= e($formFood['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group full">
                            <label>
                                <input
                                    type="checkbox"
                                    name="is_available"
                                    value="1"
                                    <?= (int) $formFood['is_available'] === 1 ? 'checked' : '' ?>
                                    style="width:auto;">
                                注文可能にする
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?= $editFood ? '変更を保存' : '料理を登録' ?>
                            </button>

                            <?php if ($editFood): ?>
                                <a href="foods.php" class="btn btn-secondary">
                                    キャンセル
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </section>

        <section class="admin-panel">
            <h2>料理一覧（<?= count($foods) ?>件）</h2>

            <form method="get" action="foods.php" class="filter-form">
                <input
                    type="search"
                    name="search"
                    value="<?= e($search) ?>"
                    placeholder="料理名・説明で検索">

                <select name="category">
                    <option value="">すべてのカテゴリー</option>
                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= (string) $categoryFilter === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary">検索</button>
                <a href="foods.php" class="btn btn-secondary">リセット</a>
            </form>

            <div class="table-wrap">
                <table class="food-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>画像</th>
                            <th>料理名</th>
                            <th>カテゴリー</th>
                            <th>価格</th>
                            <th>注文状態</th>
                            <th>操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($foods)): ?>
                            <tr>
                                <td colspan="7">料理が登録されていません。</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($foods as $food): ?>
                                <tr>
                                    <td><?= (int) $food['id'] ?></td>

                                    <td>
                                        <?php if (!empty($food['image'])): ?>
                                            <img
                                                class="food-thumb"
                                                src="../<?= e(ltrim($food['image'], '/')) ?>"
                                                alt="<?= e($food['name']) ?>"
                                                loading="lazy">
                                        <?php else: ?>
                                            <span>画像なし</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <strong><?= e($food['name']) ?></strong>
                                        <?php if (!empty($food['description'])): ?>
                                            <br>
                                            <small><?= e($food['description']) ?></small>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= e($food['category_name'] ?? '未分類') ?></td>

                                    <td>¥<?= number_format((int) $food['price']) ?></td>

                                    <td>
                                        <?php if ((int) $food['is_available'] === 1): ?>
                                            <span class="status-available">注文可能</span>
                                        <?php else: ?>
                                            <span class="status-unavailable">販売停止</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="row-actions">
                                            <a
                                                href="foods.php?edit=<?= (int) $food['id'] ?>"
                                                class="btn btn-edit">
                                                編集
                                            </a>

                                            <form
                                                method="post"
                                                action="foods.php"
                                                onsubmit="return confirm('この料理を削除しますか？');">
                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e($_SESSION['csrf_token']) ?>">
                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="delete">
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $food['id'] ?>">

                                                <button type="submit" class="btn btn-delete">
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</body>

</html>