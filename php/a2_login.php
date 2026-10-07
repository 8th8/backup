<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーログイン</title>
    
<style>
    * {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

body{
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  overflow: hidden;
  font-family:Inter,ui-sans-serif,system-ui,Segoe UI,Roboto,'Helvetica Neue',Arial; 
  color:#fff
}

a{
    color:inherit;
    text-decoration:none
}

/* ================================================= background ========================================= */
.background {
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    width: 100vw;
    height: 100vh;
    z-index: -1;
    overflow: hidden;
}

.background video {
    width:100%; 
    height:100%; 
    object-fit:cover; 
    filter: brightness(0.7); 
    pointer-events:none;
}

.form {
    border: 1px solid white;
    width: 450px;
    justify-items: center;
    border-radius: 20px;
    padding: 30px;
    background-color: rgba(0,0,0,0.3);
    backdrop-filter: blur(5px);
    display: flex;
    flex-direction: column;
}
.form input{
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
}
.form button[type="submit"]{
    background: orange;
    color: white;
    font-weight: bold;
    cursor: pointer;
}
h2{
    margin-bottom: 20px;
}
.option {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
}
button {
    margin: 10px;
    width: 150px;
    height: 50px;
    background: green;
    color: #fff;
    border-radius: 20px;
}
button:hover {
    background: orange;
    color: #fff;
}

</style>
</head>

<body>
    <div class="container">
         <!----------- background ----------->
        <div class="background">
            <video id="bgVideo" autoplay loop muted playsinline class="rain-bg">
                <source src="./image/life-of-deer-moewalls-com.mp4" type="video/mp4">
            </video>
        </div>

        <!----------- form ----------->
        <form method="POST" class="form" action="a2_login1.php">
            <h2>ユーザーログイン</h2>
            学生ID: <input type="number" name="student_no" required><br><br>
            パスワード: <input type="password" name="password" required><br><br>

            <input type="submit">
        </form>
        
        <button><a href="a4_user_list.php">ホーム画面に戻る</a></button>
    </div>
   

</body>

</html>