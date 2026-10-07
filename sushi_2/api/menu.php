
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../config/database.php";

// Chỉ cho phép GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    // Lấy danh sách danh mục
    $category_sql = "
        SELECT
            id,
            name_ja,
            name_en,
            name_vi
        FROM categories
        ORDER BY id ASC
    ";

    $category_stmt = $pdo->query($category_sql);
    $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách món đang được bán
    $food_sql = "
        SELECT
            id,
            category_id,
            name_ja,
            name_en,
            name_vi,
            description,
            price,
            image
        FROM foods
        WHERE is_available = 1
        ORDER BY category_id ASC, id ASC
    ";

    $food_stmt = $pdo->query($food_sql);
    $foods = $food_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn hóa dữ liệu trả về
    foreach ($foods as &$food) {
        $food["id"] = (int) $food["id"];
        $food["category_id"] = (int) $food["category_id"];
        $food["price"] = (float) $food["price"];
    }

    unset($food);

    // Trả dữ liệu JSON
    echo json_encode([
        "success" => true,
        "message" => "Menu retrieved successfully",
        "data" => [
            "categories" => $categories,
            "foods" => $foods
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve menu"
    ], JSON_UNESCAPED_UNICODE);
}