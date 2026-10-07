<?php

$host = "localhost";
$dbname = "webpg2";
$user = "root";
$pass = "";

$conn = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $user,
    $pass
);

$id = $_POST["id"] ?? "";

$sql = "SELECT COUNT(*) FROM address WHERE student_no = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);

$count = $stmt->fetchColumn();

if ($count > 0) {
    echo "既に登録されている学籍番号です！";
} else {
    echo "この学籍番号は登録できます。";
}
?>