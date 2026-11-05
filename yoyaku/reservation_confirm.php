<?php
// Chỉ cho phép truy cập bằng POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("不正なアクセスです。");
}

// Nhận dữ liệu từ form
$customer_name = $_POST["customer_name"] ?? "";
$customer_phone = $_POST["customer_phone"] ?? "";
$reservation_date = $_POST["reservation_date"] ?? "";
$reservation_time = $_POST["reservation_time"] ?? "";
$party_size = $_POST["party_size"] ?? "";
$note = $_POST["note"] ?? "";

function h($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reservation_confirm</title>
    <link rel="stylesheet" href="assets/css/reservation_confirm.css">
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
                    <div class="reservation__title">
                        <h2>ご予約案内の確認</h2>
                    </div>

                    <section class="reservation__steps">
                        <div class="reservation__step-active">
                            <div class="step">
                                <span class="step__number">1</span>
                                <span class="step__label">入力</span>
                            </div>
                            <div class="step__active">
                                <span class="step__number">2</span>
                                <span class="step__label-active">確認</span>
                            </div>
                            <div class="step">
                                <span class="step__number">3</span>
                                <span class="step__label">完了</span>
                            </div>
                        </div>
                    </section>
                </div>

                <form action="reservation_insert.php" method="POST" class="reservation__form-input">

                    <input type="hidden" name="reservation_date"
                        value="<?= h($reservation_date) ?>">

                    <input type="hidden" name="reservation_time"
                        value="<?= h($reservation_time) ?>">

                    <input type="hidden" name="customer_name"
                        value="<?= h($customer_name) ?>">

                    <input type="hidden" name="customer_phone"
                        value="<?= h($customer_phone) ?>">

                    <input type="hidden" name="party_size"
                        value="<?= h($party_size) ?>">

                    <input type="hidden" name="note"
                        value="<?= h($note) ?>">

                    <div class="main__form-group">
                        <div class="reservation-form__column">
                            <div class="form__customer-name">
                                <div class="main__form-label">お名前</div>
                                <h2 class="main__form-input"><?= h($customer_name) ?></h2>

                            </div>

                            <div class="form__phone-number">
                                <div class="main__form-label">電話番号 </div>
                                <h2 class="main__form-input"><?= h($customer_phone) ?></h2>
                            </div>

                            <div class="form__date">
                                <div class="main__form-label">ご来店日</div>
                                <h2 class="main__form-input"><?= h($reservation_date) ?></h2>
                            </div>

                            <div class="form__arrival-time">
                                <div class="main__form-label">ご来店時間</div>
                                <h2 class="main__form-input"><?= h($reservation_time) ?></h2>
                            </div>

                            <div class="form__number-of-guests">
                                <div for="number-of-guests" class="main__form-label">
                                    人数
                                </div>
                                <h2 class="main__form-input"><?= h($party_size) ?></h2>
                            </div>

                            <div class="form__special-requests">
                                <div class="main__form-label">ご要望（任意）</div>
                                <h2 class="main__form-input"><?= h($note) ?></h2>
                            </div>

                        </div>
                    </div>

                    <section class="form__footer">
                        <a href="reservation.php" class="reservation__form-back">戻る</a>

                        <button type="submit" class="reservation__form-submit">予約を確定する</button>
                    </section>
                </form>

            </div>

            <div class="reservation__sidebar">
                <div action="" class="restaurant-info">
                    <div class="restaurant-info-img">
                        <img src="assets/images/ban.png" alt="" class="">
                    </div>

                    <div class="restaurant-info-description">
                        <h3>店舗情報</h3><br>
                        <span>営業時間 <br>
                            11:00〜21:30 <br>
                            電話番号 <br>
                            092-XXX-XXXX</span>
                    </div>
                </div>
            </div>
        </main>

        <footer class="footer">レストラン彩</footer>
    </div>
</body>

</html>