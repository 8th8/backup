<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="a1_dknd.css">

</head>

<body>
    <div class="background">
        <video id="bgVideo" autoplay loop muted playsinline class="rain-bg">
            <source src="./image/life-of-deer-moewalls-com.mp4" type="video/mp4">
        </video>
    </div>


    <?php

    require_once 'dbconnect.php'; //nap file ket noi DB

    $department = $_POST['department']; //nhan du lieu tu Form
    $year = $_POST['year']; //nhan du lieu tu Form
    $student_no  = $_POST['student_no']; //nhan du lieu tu Form
    $name = $_POST['name'];
    $pass = $_POST['password'];
    
    echo "<h2 style='color:while; font-weight:bold;'>
        ユーザー登録が正常に完了しました。
      </h2>";
    ?>

    <div class="form">
        <?php
        echo "<h2>登録確認フォーム</h2><br>";
        echo "学科: " . $department  . "<br>"; //hien thi du lieu
        echo "学年: " . $year  . "<br>"; //hien thi du lieu
        echo "学籍番号: " . $student_no  . "<br>"; //hien thi du lieu
        echo "氏名: " . $name . "<br>";
        echo "パスワード: " . $pass . "<br>";

        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT); //Ma hoa mat khau

        $sql = "INSERT INTO address (department, year, student_no , name, password)  
                VALUES              (:department, :year, :student_no, :name, :password)"; // thêm dữ liệu vào bảng address 
        $stmt = $dbh->prepare($sql);//$stmt(statement) nghĩa là câu lệnh SQL đã được chuẩn bị.  //prepare() là hàm của PDO dùng để chuẩn bị câu SQL trước khi chạy.
        $params = [    //Đây là mảng chứa giá trị sẽ thay thế cho các dấu : trong SQL.
            ':department' => $department,
            ':year' => $year,
            ':student_no' => $student_no,
            ':name' => $name,
            ':password' => $hashedPassword,
        ];

        $stmt->execute($params); //execute : thực thi (chạy) câu lệnh SQL đã được chuẩn bị trước.

        ?>
    </div>

    <button type="button"><a href="./a2_login.php">履修科目登録</a></button>
    <button type="button"><a href="./a4_user_list.php">ホームへ戻る</a></button>
</body>

</html>