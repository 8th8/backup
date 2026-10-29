
<?php

require_once __DIR__ . "/config/dbconnect.php";
date_default_timezone_set('Asia/Tokyo');


// 1. Chi cho phep gui du lieu bang POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("不正なアクセスです。");
}

// 2. Nhan du lieu tu form
$reservation_date = trim($_POST["reservation_date"] ?? "");
$reservation_time = trim($_POST["reservation_time"] ?? "");
$customer_phone = trim($_POST["customer_phone"] ?? "");
$customer_name = trim($_POST["customer_name"] ?? "");
$note = trim($_POST["note"] ?? "");

$party_size = filter_input(
    INPUT_POST,
    "party_size",
    FILTER_VALIDATE_INT
);

// 3. Kiem tra so nguoi
if (
    $party_size === false ||
    $party_size === null ||
    $party_size < 1 ||
    $party_size > 10
) {
    exit("人数を正しく選択してください。");
}

// 4. Kiem tra cac truong bat buoc
$missing = [];
if ($reservation_date === "") $missing[] = "ご来店日 (reservation_date)";
if ($reservation_time === "") $missing[] = "ご来店時間 (reservation_time)";
if ($customer_phone === "")   $missing[] = "電話番号 (customer_phone)";
if ($customer_name === "")    $missing[] = "お名前 (customer_name)";

if ($missing) {
    exit("必須項目を入力してください。未入力: " . implode("、", $missing));
}

// 5. Kiem tra dinh dang ngay
$date = DateTime::createFromFormat(
    "!Y-m-d",
    $reservation_date
);

if (
    !$date ||
    $date->format("Y-m-d") !== $reservation_date ||
    $date < new DateTime("today")
) {
    exit("ご来店日を正しく入力してください。");
}

// 6. Kiem tra dinh dang thoi gian HH:MM
if (
    !preg_match(
        '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
        $reservation_time
    )
) {
    exit("ご来店時間を正しく入力してください。");
}

// 7. Kiem tra so dien thoai Nhat Ban
if (!preg_match('/^[0-9+\-\s()]{9,20}$/', $customer_phone)) {
    exit("電話番号を正しく入力してください。");
}

try {
    // 8. Tao ma dat ban ngau nhien
    $reservation_code = bin2hex(random_bytes(8));

    // 9. Cau lenh SQL
    $sql = "
        INSERT INTO reservations (
            reservation_code,
            customer_name,
            customer_phone,
            reservation_date,
            reservation_time,
            party_size,
            note
        ) VALUES (
            :reservation_code,
            :customer_name,
            :customer_phone,
            :reservation_date,
            :reservation_time,
            :party_size,
            :note
        )
    ";

    // 10. Chuan bi va thuc thi SQL
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":reservation_code" => $reservation_code,
        ":customer_name" => $customer_name,
        ":customer_phone" => $customer_phone,
        ":reservation_date" => $reservation_date,
        ":reservation_time" => $reservation_time,
        ":party_size" => $party_size,
        ":note" => $note !== "" ? $note : null
    ]);

    // 11. Chuyen sang trang hoan tat
    header(
        "Location: reservation_complete.php?code="
        . urlencode($reservation_code)
    );
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    exit("予約の登録に失敗しました。");
}
?>
