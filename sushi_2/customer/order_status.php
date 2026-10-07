<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// Ngôn ngữ được hỗ trợ
$allowedLanguages = ['ja', 'en', 'zh-CN', 'zh-TW', 'ko', 'vi'];
$lang = $_GET['lang'] ?? 'ja';

if (!in_array($lang, $allowedLanguages, true)) {
    $lang = 'ja';
}

// Lấy ID đơn hàng từ URL
$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Chỉ cho phép khách xem đơn hàng đã được lưu trong session của họ
$lastOrderId = $_SESSION['last_order_id'] ?? null;

if (
    !$orderId ||
    !$lastOrderId ||
    (int) $orderId !== (int) $lastOrderId
) {
    http_response_code(404);
    exit('注文が見つかりません。注文履歴をご確認ください。');
}

// Kiểm tra kết nối database
if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    exit('データベースに接続できません。スタッフにお声がけください。');
}

try {
    // Lấy thông tin đơn hàng
    $stmt = $pdo->prepare(
        'SELECT id, table_number, language, total_amount, status, created_at
         FROM orders
         WHERE id = :id
         LIMIT 1'
    );

    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        exit('注文が見つかりません。');
    }

    // Lấy chi tiết món ăn trong đơn hàng
    $itemStmt = $pdo->prepare(
        'SELECT food_name, unit_price, quantity, subtotal
         FROM order_items
         WHERE order_id = :order_id
         ORDER BY id ASC'
    );

    $itemStmt->execute([':order_id' => $orderId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Order status error: ' . $e->getMessage());

    http_response_code(500);
    exit('注文情報を取得できませんでした。スタッフにお声がけください。');
}

// Hàm escape dữ liệu trước khi hiển thị HTML
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Chuyển trạng thái sang tiếng Nhật để hiển thị
$statusLabels = [
    'pending' => '注文受付',
    'preparing' => '調理中',
    'completed' => '提供済み',
    'cancelled' => 'キャンセル',
];

$status = $order['status'];
$statusLabel = $statusLabels[$status] ?? '確認中';

// Ngày giờ tạo đơn hàng
$createdAt = date(
    'Y/m/d H:i',
    strtotime($order['created_at'])
);
?>

<!DOCTYPE html>
<html lang="<?= e($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>注文状況 | 寿司 博多魚がし</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .status-container {
            max-width: 760px;
            margin: 30px auto;
            padding: 20px;
        }

        .status-card {
            padding: 24px;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .status-heading {
            text-align: center;
            margin-bottom: 24px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 20px;
            background: #fff0d9;
            color: #804b00;
            font-weight: bold;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid #eee;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .back-menu {
            display: block;
            text-align: center;
            padding: 14px;
            background: #b22222;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <main class="status-container">

        <section class="status-card status-heading">
            <h1>ご注文ありがとうございます</h1>

            <p>ご注文を受け付けました。</p>

            <p>
                注文番号：
                <strong>#<?= e($order['id']) ?></strong>
            </p>

            <p>
                <span class="status-badge">
                    <?= e($statusLabel) ?>
                </span>
            </p>
        </section>

        <section class="status-card">
            <h2>注文情報</h2>

            <p>
                <strong>テーブル：</strong>
                <?= $order['table_number'] !== null
                    ? e($order['table_number'])
                    : '未設定' ?>
            </p>

            <p>
                <strong>注文日時：</strong>
                <?= e($createdAt) ?>
            </p>
        </section>

        <section class="status-card">
            <h2>注文内容</h2>

            <?php if (empty($items)): ?>
                <p>注文商品がありません。</p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div>
                            <strong><?= e($item['food_name']) ?></strong>
                            <div>
                                <?= number_format((int) $item['unit_price']) ?>円
                                × <?= e($item['quantity']) ?>
                            </div>
                        </div>

                        <div>
                            <?= number_format((int) $item['subtotal']) ?>円
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="order-total">
                    <span>合計金額</span>
                    <span>
                        <?= number_format((int) $order['total_amount']) ?>円
                    </span>
                </div>
            <?php endif; ?>
        </section>

        <a class="back-menu" href="menu.php?lang=<?= urlencode($lang) ?>">
            メニューに戻る
        </a>

    </main>
</body>

</html>