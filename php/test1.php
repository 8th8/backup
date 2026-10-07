<?php
require_once 'dbconnect.php';

$teacher_id = $_POST['teacher_id']; //nhan du lieu tu Form
$name = $_POST['name']; //nhan du lieu tu Form
$password  = $_POST['password'];

echo "<h2>登録確認フォーム</h2><br>";
echo "teacher_id: " . $teacher_id  . "<br>"; //hien thi du lieu
echo "name:" . $name  . "<br>";
echo "password:" . $password  . "<br>";


$sql = "INSERT INTO teacher (teacher_id, name, password)  
                VALUES      (:teacher_id, :name, :password)"; // thêm dữ liệu vào bảng address 
$stmt = $dbh->prepare($sql); //$stmt(statement) nghĩa là câu lệnh SQL đã được chuẩn bị.  //prepare() là hàm của PDO dùng để chuẩn bị câu SQL trước khi chạy.
$params = [    //Đây là mảng chứa giá trị sẽ thay thế cho các dấu : trong SQL.
    ':teacher_id' => $teacher_id,
    ':name' => $name,
    ':password' => $password,
];

$stmt->execute($params);

?>