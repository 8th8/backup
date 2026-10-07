<?php

session_start();

require_once 'dbconnect.php';

// Nhận dữ liệu từ form login
$student_no = $_POST["student_no"];
$pass = $_POST["password"];

// Tìm tài khoản
$sql = "SELECT * FROM address WHERE student_no = ?";

$stmt = $dbh->prepare($sql);
$stmt->execute([$student_no]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra đăng nhập
if ($user && password_verify($pass, $user["password"])) {

    // Lưu thông tin người đăng nhập vào session
    $_SESSION["student_no"] = $user["student_no"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["department"] = $user["department"];
    $_SESSION["year"] = $user["year"];

    // Chuyển sang trang đăng ký môn
    header("Location: a3_course_register.php");
    exit();

} else {

    // Đăng nhập sai
    header("Location: a2_login_Error.php");
    exit();
}