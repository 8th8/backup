<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/database.php";

try {

    // Lấy các menu đang được bán
    $sql = "
        SELECT
            id,
            name,
            description,
            price,
            category,
            image,
            status
        FROM menus
        WHERE status = 'available'
        ORDER BY category, id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $menus = $stmt->fetchAll();

    echo json_encode(
        [
            "success" => true,
            "menus" => $menus
        ],
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode(
        [
            "success" => false,
            "message" => "メニューの取得に失敗しました。"
        ],
        JSON_UNESCAPED_UNICODE
    );
}