<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once "../config/database.php";

// Chỉ cho phép POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Lấy dữ liệu JSON từ request
$input = json_decode(
    file_get_contents("php://input"),
    true
);

// Nếu không gửi JSON thì thử lấy POST thông thường
if (!is_array($input)) {
    $input = $_POST;
}

// Lấy thông tin bàn
$table_number = trim(
    $input["table_number"] ?? ""
);

// Lấy ghi chú
$note = trim(
    $input["note"] ?? ""
);

// Kiểm tra số bàn
if ($table_number === "") {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Table number is required"
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Kiểm tra giỏ hàng
$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Cart is empty"
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    /*
     * Bắt đầu transaction.
     *
     * Nếu một bước xảy ra lỗi,
     * toàn bộ đơn hàng sẽ được rollback.
     */
    $pdo->beginTransaction();

    $food_ids = array_keys($cart);

    $placeholders = implode(
        ",",
        array_fill(0, count($food_ids), "?")
    );

    // Lấy thông tin món từ database
    $sql = "
        SELECT
            id,
            name_ja,
            name_en,
            name_vi,
            price,
            is_available
        FROM foods
        WHERE id IN ($placeholders)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($food_ids);

    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($foods)) {
        throw new Exception("No valid products found");
    }

    // Tạo danh sách món hợp lệ
    $valid_foods = [];

    foreach ($foods as $food) {

        if ((int)$food["is_available"] !== 1) {
            continue;
        }

        $valid_foods[(int)$food["id"]] = $food;
    }

    if (empty($valid_foods)) {
        throw new Exception("No available products found");
    }

    // Tính tổng tiền
    $total_amount = 0;

    foreach ($cart as $food_id => $quantity) {

        $food_id = (int)$food_id;
        $quantity = (int)$quantity;

        if (
            $quantity < 1 ||
            $quantity > 99 ||
            !isset($valid_foods[$food_id])
        ) {
            continue;
        }

        $price = (float)$valid_foods[$food_id]["price"];

        $total_amount += $price * $quantity;
    }

    if ($total_amount <= 0) {
        throw new Exception("Invalid order amount");
    }

    /*
     * Tạo order.
     *
     * Quan trọng:
     * total_amount được tính từ database,
     * không lấy giá từ trình duyệt.
     */

    $order_sql = "
        INSERT INTO orders (
            table_number,
            total_amount,
            status,
            note,
            created_at
        )
        VALUES (
            :table_number,
            :total_amount,
            'pending',
            :note,
            NOW()
        )
    ";

    $order_stmt = $pdo->prepare($order_sql);

    $order_stmt->execute([
        ":table_number" => $table_number,
        ":total_amount" => $total_amount,
        ":note" => $note
    ]);

    // Lấy ID đơn hàng vừa tạo
    $order_id = (int)$pdo->lastInsertId();

    /*
     * Tạo các order_items
     */
    $item_sql = "
        INSERT INTO order_items (
            order_id,
            food_id,
            quantity,
            price
        )
        VALUES (
            :order_id,
            :food_id,
            :quantity,
            :price
        )
    ";

    $item_stmt = $pdo->prepare($item_sql);

    foreach ($cart as $food_id => $quantity) {

        $food_id = (int)$food_id;
        $quantity = (int)$quantity;

        if (
            $quantity < 1 ||
            $quantity > 99 ||
            !isset($valid_foods[$food_id])
        ) {
            continue;
        }

        $price = (float)$valid_foods[$food_id]["price"];

        $item_stmt->execute([
            ":order_id" => $order_id,
            ":food_id" => $food_id,
            ":quantity" => $quantity,
            ":price" => $price
        ]);
    }

    /*
     * Hoàn tất transaction
     */
    $pdo->commit();

    /*
     * Lưu order ID vào session.
     *
     * api/order_status.php sẽ sử dụng
     * thông tin này để xác thực khách hàng.
     */
    if (!isset($_SESSION["order_ids"])) {
        $_SESSION["order_ids"] = [];
    }

    $_SESSION["order_ids"][] = $order_id;

    // Xóa giỏ hàng sau khi đặt thành công
    $_SESSION["cart"] = [];

    // Trả kết quả
    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully",
        "data" => [
            "order_id" => $order_id,
            "table_number" => $table_number,
            "total_amount" => $total_amount,
            "status" => "pending"
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {

    // Nếu transaction đang chạy thì rollback
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to place order"
    ], JSON_UNESCAPED_UNICODE);
}
