<?php
ini_set('session.cookie_httponly', true);
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    ini_set('session.cookie_secure', true);
}

session_start();

$sessionLifetime = 12 * 60 * 60;  // 12 hours in seconds

// ログインの確認およびセッションの有効期限をチェック
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (time() - $_SESSION["login_time"] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header("Location: ../signIn/exhibitorSignin.php");
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" href="../images/staff.png" type="image/png">
    <link rel="manifest" href="./manifest.json">
    <link rel="stylesheet" href="./styles.css">
    <script src="./index.js"></script>
    <script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('./sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}



    </script>
    <title>Mainstreet HOME</title>
</head>
<body>
    
    <div id="splash-image">
        <img src="../images/logo.jpg" alt="Splash Image">
<script>
    setTimeout(function() {
        var splashImage = document.getElementById("splash-image");
        splashImage.remove();
    }, 2000);  // 1秒後
    </script>

    </div>

    <div class="container">
        <div class="user-container">
            <h2>第59回白山祭入構管理</h2>
            <div id="qrCode">
                <script>
                    var uniqueId = "<?php echo htmlspecialchars($_SESSION["unique_id"]); ?>";
                </script>
                <script src="https://unpkg.com/qr-code-styling@1.6.0-rc.1/lib/qr-code-styling.js"></script>
                <script src="./qrGenerate.js"></script> <br>
            </div>

            <div id="greeting">こんにちは！<?php echo htmlspecialchars($_SESSION["name"]); ?>さん！！</div>

            <div id="current-time">
                <script src="../Staff/currentTime.js"></script>
            </div>

            <div id="userInfo">
                <div id="user-column">
                <li><?php echo htmlspecialchars($_SESSION["team"]); ?></li>
                    <li><?php echo htmlspecialchars($_SESSION["name"]); ?></li>
                    <li><?php echo htmlspecialchars($_SESSION["student_id"]); ?></li>
                    <li><?php echo htmlspecialchars($_SESSION["unique_id"]); ?></li>
                </div>
            </div>
            <form action="../signIn/exhibitorSignout.php">
                <input type="submit" value="Logout">
            </form>
            <p>第59回白山祭入構管理システム</p>
            <img id="logo-image" src="../images/logo.jpg" alt="Logo">
            <style>
                /* #logo-imageに関するスタイル */
                #logo-image {
                    position: absolute; /* 絶対位置を設定 */
                    width: 80%; /* 幅を100%に設定 */
                    left: 50%; /* 左端から50%の位置に配置 */
                    transform: translateX(-50%); /* 水平方向に中央寄せ */
                    z-index: 1; /* インデックスを設定して他の要素の上に表示 */
                }
            </style>
        </div>

    </div>
</body>
</html>
