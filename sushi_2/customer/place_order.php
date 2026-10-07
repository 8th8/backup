<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menu.php');
    exit;
}

// Lấy ngôn ngữ
$allowedLanguages = ['ja', 'en', 'zh-CN', 'zh-TW', 'ko', 'vi'];
$lang = $_GET['lang'] ?? 'ja';

if (!in_array($lang, $allowedLanguages, true)) {
    $lang = 'ja';
}

// Kiểm tra CSRF token
$sessionToken = $_SESSION['csrf_token'] ?? '';
$postedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($sessionToken) ||
    !is_string($postedToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $postedToken)
) {
    http_response_code(403);
    exit('Invalid request. Please return to the order page and try again.');
}

// Kiểm tra giỏ hàng
$cart = $_SESSION['cart'] ?? [];

if (!is_array($cart) || empty($cart)) {
    header('Location: cart.php?lang=' . urlencode($lang));
    exit;
}

// Danh sách món và giá tạm thời.
// Sau này sẽ lấy dữ liệu trực tiếp từ database.
$foods = [
    1 => ['name' => 'とんだえセット', 'price' => 2343],
    2 => ['name' => 'えび', 'price' => 407],
    3 => ['name' => 'たい', 'price' => 451],
    4 => ['name' => 'ちょっと一杯セット', 'price' => 1969],
    5 => ['name' => 'まぐろづくし', 'price' => 2376],
    6 => ['name' => '赤えび', 'price' => 506],
    7 => ['name' => '焼きあなご', 'price' => 451],
    8 => ['name' => '刺身5点盛り', 'price' => 2376],
];

// Xác thực số lượng và tính tổng tiền ở máy chủ
$items = [];
$total = 0;

foreach ($cart as $foodId => $quantity) {
    $foodId = filter_var($foodId, FILTER_VALIDATE_INT);
    $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

    if (
        $foodId === false ||
        $quantity === false ||
        !isset($foods[$foodId]) ||
        $quantity < 1 ||
        $quantity > 99
    ) {
        http_response_code(400);
        exit('Invalid cart data. Please return to your cart and try again.');
    }

    $food = $foods[$foodId];
    $subtotal = $food['price'] * $quantity;

    $items[] = [
        'food_id' => $foodId,
        'food_name' => $food['name'],
        'unit_price' => $food['price'],
        'quantity' => $quantity,
        'subtotal' => $subtotal,
    ];

    $total += $subtotal;
}

// Lấy mã bàn từ session nếu có
$tableNumber = $_SESSION['table_id'] ?? null;

try {
    /*
     * File database.php cần tạo biến PDO tên $pdo.
     * Các bảng orders và order_items sẽ được tạo trong database.sql.
     */
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new RuntimeException('Database connection is not configured.');
    }

    $pdo->beginTransaction();

    // Lưu đơn hàng
    $stmt = $pdo->prepare(
        'INSERT INTO orders
            (table_number, language, total_amount, status, created_at)
         VALUES
            (:table_number, :language, :total_amount, :status, NOW())'
    );

    $stmt->execute([
        ':table_number' => $tableNumber,
        ':language' => $lang,
        ':total_amount' => $total,
        ':status' => 'pending',
    ]);

    $orderId = (int) $pdo->lastInsertId();

    // Lưu từng món trong đơn hàng
    $itemStmt = $pdo->prepare(
        'INSERT INTO order_items
            (order_id, food_id, food_name, unit_price, quantity, subtotal)
         VALUES
            (:order_id, :food_id, :food_name, :unit_price, :quantity, :subtotal)'
    );

    foreach ($items as $item) {
        $itemStmt->execute([
            ':order_id' => $orderId,
            ':food_id' => $item['food_id'],
            ':food_name' => $item['food_name'],
            ':unit_price' => $item['unit_price'],
            ':quantity' => $item['quantity'],
            ':subtotal' => $item['subtotal'],
        ]);
    }

    $pdo->commit();

    // Ghi nhớ đơn hàng gần nhất cho trang theo dõi
    $_SESSION['last_order_id'] = $orderId;

    // Xóa giỏ hàng và đổi token để tránh gửi lại form cũ
    unset($_SESSION['cart']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Chuyển sang trang trạng thái đơn hàng
    header(
        'Location: order_status.php?id=' . $orderId
            . '&lang=' . urlencode($lang)
    );
    exit;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Ghi lỗi vào log máy chủ, không hiển thị chi tiết cho khách
    error_log('Order placement failed: ' . $e->getMessage());

    http_response_code(500);
?>
    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Error</title>
    </head>

    <body>
        <h1>注文を処理できませんでした</h1>
        <p>申し訳ありません。注文を保存できませんでした。</p>
        <p>お手数ですが、スタッフにお声がけください。</p>
        <a href="cart.php?lang=<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
            カートに戻る
        </a>
    </body>

    </html>
<?php
    exit;
}
