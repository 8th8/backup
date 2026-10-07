<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Segoe UI", sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* ================= Background ================= */
        .background {
            position: fixed;
            inset: 0;
            z-index: -1;

        }
        .background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.65);
        }

        /* ================= Form ================= */
        .form {
            width: 380px;
            padding: 25px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .form h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 26px;
        }

        /* label */
        .form label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        /* input select */
        .form input,
        .form select {

            width: 100%;
            padding: 9px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            outline: none;

        }
        /* 学籍番号 + Confirm */
        .id-area {
            display: flex;
            gap: 10px;
        }

        .id-area input {
            flex: 1;
        }

        .id-area button {
            width: 100px;
            border: none;
            border-radius: 8px;
            background: #2196f3;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .id-area button:hover {
            background: #1976d2;
        }

        /* Check result */

        #result {

            margin-top: 5px;
            font-size: 14px;
            min-height: 18px;
        }

        /* submit */
        .form input[type="submit"] {
            margin-top: 15px;
            background: #ff9800;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: .3s;
        }

        .form input[type="submit"]:hover {
            background: #f57c00;
            transform: translateY(-2px);
        }

        /* focus */
        .form input:focus,
        .form select:focus {
            box-shadow: 0 0 8px rgba(255, 255, 255, .8);
        }
    </style>
</head>

<body>

    <!----------- Background ----------->
    <div class="background">
        <video id="bgVideo" autoplay loop muted playsinline class="rain-bg">
            <source src="./image/life-of-deer-moewalls-com.mp4" type="video/mp4">
        </video>
    </div>

    <!----------- Register Form ----------->
    <form method="POST" class="form" action="a1_address_input1.php">
        <h2>ユーザー登録</h2>

        <br>
        <label>学年</label><br>
        <select name="department" required>
            <option value="">選択してください</option>
            <option value="ネットワーク学科">ネットワーク学科</option>
            <option value="経営学科">経営学科</option>
        </select>

        <label>学年</label><br>
        <select name="year" required>
            <option value="">選択してください</option>
            <option value="1">1年</option>
            <option value="2">2年</option>
            <option value="3">3年</option>
            <option value="4">4年</option>
        </select>
        <br><br>

        <label>学籍番号</label><br>
        <input type="number" id="userID" name="student_no" required>
        <button type="button" onclick="CheckID()">Confirm</button>
        <p id="result"></p>
        

        <label>氏名</label><br>
        <input type="text" name="name" required>
        <br>

        <label>パスワード</label><br>
        <input type="password" name="password" required>
        <br><br>

        <input type="submit" value="登録">

    </form>

    <script>
        async function CheckID() {

            const id = document.getElementById("userID").value;

            const response = await fetch("a1_address_input_check.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + encodeURIComponent(id)
            });

            const result = await response.text();

            document.getElementById("result").textContent = result;
        }
    </script>

</body>

</html>