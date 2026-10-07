
<?php

session_start();


// =========================================================
// LANGUAGE SETTING
// =========================================================

// Ngôn ngữ được phép sử dụng
$allowed_languages = [
    "ja",
    "en",
    "ko",
    "zh"
];


// Lấy ngôn ngữ từ URL
$lang = $_GET["lang"] ?? "ja";


// Nếu ngôn ngữ không hợp lệ
if (!in_array($lang, $allowed_languages, true)) {
    $lang = "ja";
}


// Lưu ngôn ngữ vào SESSION
$_SESSION["language"] = $lang;


// Giỏ hàng
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


// Chuyển đến trang menu
header("Location: menu.php");

exit;
