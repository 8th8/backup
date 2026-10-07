<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    >

    <title>寿司 博多魚がし | Order</title>

    <!-- Japanese / English / Korean / Chinese fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Noto+Sans+KR:wght@400;500;700&family=Noto+Sans+SC:wght@400;500;700&family=Noto+Serif+JP:wght@500;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<main class="language-page">

    <!-- Background image -->
    <div class="language-background"></div>

    <!-- Brand overlay -->
    <div class="language-overlay"></div>


    <section class="language-content">


        <!-- Fish seal -->
        <div class="seal">
            魚
        </div>


        <!-- Restaurant name -->
        <h1 class="restaurant-name">
            寿司 博多魚がし
        </h1>


        <p class="restaurant-name-en">
            SUSHI HAKATA UOGASHI
        </p>


        <!-- Decoration -->
        <div class="title-decoration">

            <span></span>

            <b>鮨</b>

            <span></span>

        </div>


        <!-- Language title -->
        <div class="language-title">

            <p>
                言語を選択
            </p>

            <span>
                Select your language
            </span>

        </div>


        <!-- Language buttons -->
        <div class="language-buttons">


            <!-- Japanese -->
            <a
                href="customer/language.php?lang=ja"
                class="language-button"
            >

                <span class="language-flag">
                    🇯🇵
                </span>

                <div class="language-text">

                    <div class="language-main">
                        日本語
                    </div>

                    <div class="language-sub">
                        Japanese
                    </div>

                </div>

                <span class="language-arrow">
                    →
                </span>

            </a>


            <!-- English -->
            <a
                href="customer/language.php?lang=en"
                class="language-button"
            >

                <span class="language-flag">
                    🇺🇸
                </span>

                <div class="language-text">

                    <div class="language-main">
                        English
                    </div>

                    <div class="language-sub">
                        英語
                    </div>

                </div>

                <span class="language-arrow">
                    →
                </span>

            </a>


            <!-- Korean -->
            <a
                href="customer/language.php?lang=ko"
                class="language-button"
            >

                <span class="language-flag">
                    🇰🇷
                </span>

                <div class="language-text">

                    <div class="language-main">
                        한국어
                    </div>

                    <div class="language-sub">
                        Korean
                    </div>

                </div>

                <span class="language-arrow">
                    →
                </span>

            </a>


            <!-- Chinese -->
            <a
                href="customer/language.php?lang=zh"
                class="language-button"
            >

                <span class="language-flag">
                    🇨🇳
                </span>

                <div class="language-text">

                    <div class="language-main">
                        中文
                    </div>

                    <div class="language-sub">
                        Chinese
                    </div>

                </div>

                <span class="language-arrow">
                    →
                </span>

            </a>


        </div>


        <!-- Restaurant information -->
        <div class="restaurant-info">

            <div class="info-line">

                <span class="info-icon">
                    TEL
                </span>

                <span>
                    092-413-5223
                </span>

            </div>


            <div class="info-line">

                <span class="info-icon">
                    OPEN
                </span>

                <span>
                    11:00 - 21:00
                </span>

            </div>

        </div>


        <p class="copyright">
            © SUSHI HAKATA UOGASHI
        </p>


    </section>

</main>


</body>

</html>