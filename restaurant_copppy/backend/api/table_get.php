<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/database.php";

try {

    // Lấy tất cả bàn
    $sql = "
        SELECT
            id,
            table_number,
            capacity,
            status
        FROM tables
        WHERE status != 'maintenance'
        ORDER BY table_number
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $tables = $stmt->fetchAll();

    echo json_encode(
        [
            "success" => true,
            "tables" => $tables
        ],
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode(
        [
            "success" => false,
            "message" => "テーブル情報の取得に失敗しました。"
        ],
        JSON_UNESCAPED_UNICODE
    );
}