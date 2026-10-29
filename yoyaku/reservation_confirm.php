<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reservation_confirm</title>
    <link rel="stylesheet" href="reservation_confirm.css">
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

                <form action="" class="reservation__form-input">
                    <div class="main__form-group">
                        <div class="reservation-form__column">
                            <div class="form__customer-name">
                                <div class="main__form-label">お名前</div>
                                <input type="text" class="main__form-input" placeholder="田中">
                            </div>

                            <div class="form__phone-number">
                                <div class="main__form-label">電話番号 </div>
                                <input type="tel" id="phone-number" name="phone_number" class="main__form-input"
                                    placeholder="090-0000-0000" required>
                            </div>

                            <div class="form__date">
                                <div class="main__form-label">ご来店日</div>
                                <input type="date" lang="ja" class="main__form-input">
                            </div>

                            <div class="form__arrival-time">
                                <div class="main__form-label">ご来店時間</div>
                                <input type="time" class="main__form-input">
                            </div>

                            <div class="form__number-of-guests">
                                <label for="number-of-guests" class="main__form-label">
                                    人数
                                </label>
                                <input type="number" class="main__form-input" placeholder="">

                                <div class="form__special-requests">
                                    <div class="main__form-label">ご要望（任意）</div>
                                    <input type="text" class="main__form-input" placeholder="窓側の席を希望します。">
                                </div>
                            </div>
                        </div>
                    </div>


                    <section class="form__footer">
                        <button type="button" class="reservation__form-back">戻る</button>
                        <button type="submit" class="reservation__form-submit">予約を確定する</button>
                    </section>
                </form>

            </div>

            <div class="reservation__sidebar">
                <div action="" class="restaurant-info">
                    <div class="restaurant-info-img">
                        <img src="image/ban.png" alt="" class="">
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