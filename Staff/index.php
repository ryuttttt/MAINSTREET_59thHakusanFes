<?php
ini_set('session.cookie_httponly', true);
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    ini_set('session.cookie_secure', true);
}

session_start();

$sessionLifetime = 12 * 60 * 60;  // 12 hours in seconds

if (!isset($_SESSION["loggedin"]) || (time() - $_SESSION["login_time"] > $sessionLifetime)) {
    session_unset();  // セッション変数を全て解除
    session_destroy();  // セッションを破壊
    header("Location: ../signIn/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <script src="./index.js"></script>
    <title>Mainstreet HOME</title>
</head>
<body>
    
    <div id="splash-image">
        <img src="../images/logo.svg" alt="Splash Image">
    </div>
    <script>
    setTimeout(function() {
        var splashImage = document.getElementById("splash-image");
        splashImage.remove();
    }, 2000);  // 1秒後
    </script>

    <div class="container">
        <header>
            <h1>第59回白山祭 入構管理</h1> <br>
        </header>


        <div id="greeting">こんにちは！<?php echo htmlspecialchars($_SESSION["name"]); ?>さん！！ <br>
            <div id="displayMenu" style="display:none">
            <button>メニューを表示する</button>
            </div>
        </div>

        <div id="select" >
        <button id="qrScanner" >Scannerを起動</button>
        <button id="displayQR">QRコードを表示</button>
        <img src="../images/staff.jpg" alt="" id="icon" style="display:none;">
        </div>



        <div class="user-container" style="display:none;">
        
            <div id="qrCode">
                <script>
                    var uniqueId = "<?php echo isset($_SESSION["unique_id"]) ? $_SESSION["unique_id"] : 'default_value'; ?>";
                </script>
                <script src="https://unpkg.com/qr-code-styling@1.6.0-rc.1/lib/qr-code-styling.js"></script>
                <script src="./qrGenerate.js"></script> <br>
            </div>
            <div id="current-time">
                <script src="./currentTime.js"></script>
            </div>

            <div id="userInfo">
                    <div id="user-column">
                        <li><?php echo htmlspecialchars($_SESSION["student_id"]); ?></li>
                        <li><?php echo htmlspecialchars($_SESSION["name"]); ?></li>
                        <li><?php echo htmlspecialchars($_SESSION["team"]); ?></li>
                        <li><?php echo htmlspecialchars($_SESSION["section"]); ?></li>
                        <li><?php echo htmlspecialchars($_SESSION["grade"]); ?>年生</li>
                        <li><?php echo htmlspecialchars($_SESSION["unique_id"]); ?></li>
                    </div>
            </div>
        </div>

        <footer>
            <form action="../signIn/logout.php">
                <input type="submit" value="Logout"><br>
            </form>

            <p>ver.1.0.0<br>
            2日目もホントにお疲れ様!<br>素敵な白山祭をありがとう!<br>整理日もファイト〇</p>
           <img class="logo" src="../images/logo.svg" alt="logo">
        </footer>

    </div>
</body>
</html>
