
<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate");

// Chỉ cho phép GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    header("Allow: GET");

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Kiểm tra mã đơn hàng
$order_id = filter_input(
    INPUT_GET,
    "order_id",
    FILTER_VALIDATE_INT
);

if (!$order_id || $order_id < 1) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID"
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

require_once "../config/database.php";

try {
    /*
     * Chỉ cho phép khách kiểm tra đơn hàng đã được
     * tạo trong session hiện tại.
     *
     * Cấu trúc giả định:
     * $_SESSION["order_ids"] = [1, 2, 3];
     */
    $session_order_ids = $_SESSION["order_ids"] ?? [];

    if (!in_array($order_id, $session_order_ids, true)) {
        http_response_code(403);

        echo json_encode([
            "success" => false,
            "message" => "You are not authorized to view this order"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Lấy trạng thái đơn hàng
    $sql = "
        SELECT
            id,
            status,
            created_at
        FROM orders
        WHERE id = :order_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":order_id" => $order_id
    ]);

    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Order not found"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Chuyển trạng thái thành thông báo dễ hiểu
    $status_messages = [
        "pending" => [
            "ja" => "ご注文を受け付けました。",
            "en" => "Your order has been received.",
            "vi" => "Nhà hàng đã tiếp nhận đơn hàng."
        ],
        "preparing" => [
            "ja" => "ただいま調理中です。",
            "en" => "Your order is being prepared.",
            "vi" => "Món ăn đang được chế biến."
        ],
        "completed" => [
            "ja" => "お料理の提供が完了しました。",
            "en" => "Your order has been served.",
            "vi" => "Đơn hàng đã được phục vụ."
        ],
        "cancelled" => [
            "ja" => "ご注文はキャンセルされました。",
            "en" => "Your order has been cancelled.",
            "vi" => "Đơn hàng đã bị hủy."
        ]
    ];

    $status = $order["status"];

    echo json_encode([
        "success" => true,
        "data" => [
            "order_id" => (int) $order["id"],
            "status" => $status,
            "status_messages" => $status_messages[$status] ?? [
                "ja" => "状況を確認できません。",
                "en" => "Status unavailable.",
                "vi" => "Không xác định được trạng thái."
            ],
            "created_at" => $order["created_at"]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve order status"
    ], JSON_UNESCAPED_UNICODE);
}
