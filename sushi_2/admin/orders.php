<?php
session_start();

require_once "../config/database.php";

// Kiểm tra đăng nhập admin
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $order_id = filter_input(
        INPUT_POST,
        "order_id",
        FILTER_VALIDATE_INT
    );

    $status = $_POST["status"] ?? "";

    $allowed_statuses = [
        "pending",
        "preparing",
        "completed",
        "cancelled"
    ];

    if ($order_id && in_array($status, $allowed_statuses, true)) {

        $sql = "UPDATE orders
                SET status = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":status" => $status,
            ":id" => $order_id
        ]);

        header("Location: orders.php?updated=1");
        exit;
    }
}

// Lấy danh sách đơn hàng
$sql = "SELECT *
        FROM orders
        ORDER BY created_at DESC";

$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm số lượng đơn theo trạng thái
$total_orders = count($orders);
$pending_orders = 0;
$preparing_orders = 0;
$completed_orders = 0;

foreach ($orders as $order) {
    if ($order["status"] === "pending") {
        $pending_orders++;
    } elseif ($order["status"] === "preparing") {
        $preparing_orders++;
    } elseif ($order["status"] === "completed") {
        $completed_orders++;
    }
}

// Hàm hiển thị trạng thái bằng tiếng Nhật
function getOrderStatusLabel($status)
{
    $labels = [
        "pending" => "注文受付",
        "preparing" => "調理中",
        "completed" => "提供完了",
        "cancelled" => "キャンセル"
    ];

    return $labels[$status] ?? "不明";
}

// Escape dữ liệu trước khi hiển thị HTML
function e($value)
{
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>注文管理 | Sushi Order</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="admin-layout">

        <aside class="admin-sidebar">
            <h2>🍣 Sushi Order</h2>

            <nav>
                <a href="dashboard.php">ダッシュボード</a>
                <a href="orders.php" class="active">注文管理</a>
                <a href="foods.php">商品管理</a>
                <a href="categories.php">カテゴリー管理</a>
                <a href="logout.php">ログアウト</a>
            </nav>
        </aside>

        <main class="admin-main">

            <header class="admin-header">
                <div>
                    <h1>注文管理</h1>
                    <p>注文内容と注文状況を管理します。</p>
                </div>

                <div class="admin-user">
                    管理者：
                    <?= e($_SESSION["admin_username"] ?? "Admin") ?>
                </div>
            </header>

            <?php if (isset($_GET["updated"])): ?>
                <div class="success-message">
                    注文ステータスを更新しました。
                </div>
            <?php endif; ?>

            <!-- Thống kê đơn hàng -->
            <section class="order-statistics">

                <div class="stat-card">
                    <h3>総注文数</h3>
                    <p><?= $total_orders ?></p>
                </div>

                <div class="stat-card">
                    <h3>受付中</h3>
                    <p><?= $pending_orders ?></p>
                </div>

                <div class="stat-card">
                    <h3>調理中</h3>
                    <p><?= $preparing_orders ?></p>
                </div>

                <div class="stat-card">
                    <h3>提供完了</h3>
                    <p><?= $completed_orders ?></p>
                </div>

            </section>

            <!-- Danh sách đơn hàng -->
            <section class="admin-section">

                <div class="section-heading">
                    <h2>注文一覧</h2>
                    <span><?= $total_orders ?> 件</span>
                </div>

                <div class="table-responsive">

                    <table class="admin-table">

                        <thead>
                            <tr>
                                <th>注文番号</th>
                                <th>テーブル</th>
                                <th>注文内容</th>
                                <th>合計金額</th>
                                <th>注文日時</th>
                                <th>注文状況</th>
                                <th>更新</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($orders)): ?>

                                <tr>
                                    <td colspan="7" class="empty-state">
                                        注文データがありません。
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($orders as $order): ?>

                                    <tr>
                                        <td>
                                            #<?= e($order["id"]) ?>
                                        </td>

                                        <td>
                                            <?= e($order["table_number"] ?? "-") ?>
                                        </td>

                                        <td>
                                            <?= nl2br(e($order["order_details"] ?? "-")) ?>
                                        </td>

                                        <td>
                                            ¥<?= number_format(
                                                    (float)($order["total_amount"] ?? 0)
                                                ) ?>
                                        </td>

                                        <td>
                                            <?= e($order["created_at"] ?? "-") ?>
                                        </td>

                                        <td>
                                            <span class="order-status status-<?= e($order["status"]) ?>">
                                                <?= e(getOrderStatusLabel($order["status"])) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <form
                                                method="POST"
                                                action="orders.php"
                                                class="status-form">
                                                <input
                                                    type="hidden"
                                                    name="order_id"
                                                    value="<?= e($order["id"]) ?>">

                                                <select name="status" required>
                                                    <option
                                                        value="pending"
                                                        <?= $order["status"] === "pending" ? "selected" : "" ?>>
                                                        注文受付
                                                    </option>

                                                    <option
                                                        value="preparing"
                                                        <?= $order["status"] === "preparing" ? "selected" : "" ?>>
                                                        調理中
                                                    </option>

                                                    <option
                                                        value="completed"
                                                        <?= $order["status"] === "completed" ? "selected" : "" ?>>
                                                        提供完了
                                                    </option>

                                                    <option
                                                        value="cancelled"
                                                        <?= $order["status"] === "cancelled" ? "selected" : "" ?>>
                                                        キャンセル
                                                    </option>
                                                </select>

                                                <button
                                                    type="submit"
                                                    class="btn btn-primary">
                                                    更新
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

</body>

</html>