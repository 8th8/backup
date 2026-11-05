<?php

require_once __DIR__ . "/config/dbconnect.php";

// Nhận ID từ URL bằng GET
$id = $_GET["id"] ?? "";

// Kiểm tra ID
if (!ctype_digit((string)$id) || $id <= 0) {
    exit("予約情報が見つかりません。");
}

$sql = "SELECT * FROM reservations WHERE id =  :id ";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":id" => $id
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra kết quả
if (!$row) {
    exit("予約情報が見つかりません。");
}
// Lấy dữ liệu
$reservation_code = $row["reservation_code"];
$reservation_date = $row["reservation_date"];
$reservation_time = $row["reservation_time"];
$party_size       = $row["party_size"];
$status           = $row["status"];

$date = new DateTime($reservation_date);

// Định dạng tháng/ngày
$formatted_date = $date->format('m/d');

// Lấy thứ trong tuần bằng tiếng Nhật
$weekdays = ['日', '月', '火', '水', '木', '金', '土'];

$weekday = $weekdays[(int)$date->format('w')];

// Định dạng giờ
$formatted_time = date('H:i', strtotime($reservation_time));

// Hàm bảo vệ dữ liệu HTML
function h($value)
{
    return htmlspecialchars((string)$value,ENT_QUOTES,"UTF-8");
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reservation_complete</title>
    <link rel="stylesheet" href="assets/css/reservation_complete.css">
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header__container">
                <a href="index.html" class="header__logo">
                    <h2>レストラン安</h2>
                </a>

                <nav class="header__nav">
                    <ul class="header__nav-list">
                        <li class="header__nav-item">
                            <a href="index.html" class="header__nav-link">トップ</a>
                        </li>
                        <li class="header__nav-item">
                            <a href="reservation.html" class="header__nav-link">ご予約</a>
                        </li>
                        <li class="header__nav-item">
                            <a href="" class="header__nav-link">予約照会</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="main">
            <div class="reservation__content">
                <div class="reservation__form">
                    <section class="reservation__steps">
                        <div class="reservation__step-active">
                            <div class="step">
                                <span class="step__number">1</span>
                                <span class="step__label">入力</span>
                            </div>
                            <div class="step">
                                <span class="step__number">2</span>
                                <span class="step__label">確認</span>
                            </div>
                            <div class="step__complete">
                                <span class="step__number">3</span>
                                <span class="step__label-complete">完了</span>
                            </div>
                        </div>
                    </section>
                </div>

                <div action="" class="reservation__form-complete">
                    <div class="resevation_complete-group">
                        <div class="resevation_complete-logo">V</div>
                        <div class="resevation_complete-title">ご予約を承りました</div>
                    </div>

                    <div class="resevation_complete-code">
                        <div class="resevation_code-title">ご予約番号</div>
                        <div class="resevation_code"><?= h($reservation_code) ?></div>
                    </div>

                    <div class="resevation_complete-info">
                        <div class="resevation_day">
                            <label class="resevation_complete-lable">日時</label>

                            <h2 class="main__form-input">
                                <?= h($formatted_date) ?>(<?= h($weekday) ?>)
                                <?= h($formatted_time) ?>
                            </h2>
                        </div>

                        <div class="paty_size">
                            <label for="" class="resevation_complete-lable">人数</label>
                            <h2 class="main__form-input"><?= h($party_size) ?></h2>
                        </div>

                        <div class="status">
                            <label for="" class="resevation_complete-lable">状態</label>
                            <h2 class="main__form-input"><?= h($status) ?></h2>
                        </div>
                    </div>
                    <div class="about">
                        <h4>予約番号と電話番号で、照会・取消ができます。</h4>
                    </div>



                    <section class="form__footer">

                        <a href="reservation.php"
                            class="reservation__form-back">
                            予約を照会する
                        </a>

                        <a href="index.html"
                            class="reservation__form-submit">
                            トップへ
                        </a>

                    </section>
                </div>

            </div>
        </main>

        <footer class="footer">レストラン彩</footer>
    </div>
</body>

</html>