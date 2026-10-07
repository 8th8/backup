<?php

// ==============================
// Database configuration
// ==============================

$host = "localhost";
$dbname = "sushi_order";
$username = "root";
$password = "";

// ==============================
// Create PDO connection
// ==============================

try {

    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password
    );

    // Hiển thị lỗi dưới dạng Exception
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Trả về dữ liệu dạng associative array
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    // Tắt emulate prepared statements
    $pdo->setAttribute(
        PDO::ATTR_EMULATE_PREPARES,
        false
    );
} catch (PDOException $e) {

    // Không hiển thị thông tin database ra cho người dùng
    http_response_code(500);

    exit("Database connection failed.");
}
