<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="background">
        <video id="bgVideo" autoplay loop muted playsinline class="rain-bg">
            <source src="./image/life-of-deer-moewalls-com.mp4" type="video/mp4">
        </video>
    </div>
    <div class="conten">
        <h1>Webプログラミング 2</h1>
        <p>氏 名 : グェン　アン　トゥアン ( 2242505 )</p>

        <div class="list-box">
            <button type="button" class="box one" onclick="location.href='./a1_address_input.php'">
                1.ユーザー登録
            </button>

            <button type="button" class="box one" onclick="location.href='./a2_login.php'">
                2.ユーザーログイン
            </button>

            <button type="button" class="box one" onclick="location.href='./a3_course_register.php'">
                3.科目登録システム
            </button>

            <button type="button" class="box one" onclick="location.href='./a4_user_list.php'">
                4.ユーザー管理
            </button>

            <button type="button" class="box one" onclick="location.href='./a5_user_list.php'">
                5.ユーザ別履修科目管理
            </button>
            
        </div>
    </div>
</body>

</html>