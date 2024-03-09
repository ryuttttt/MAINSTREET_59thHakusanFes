<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../signIn/login.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=1.0">
    <title>QR Scanner</title>
    <link rel="stylesheet" href="./css/app.css" />
    <script
        async
        src="https://cdn.jsdelivr.net/npm/pwacompat@2.0.9/pwacompat.min.js"
        integrity="sha384-VcI6S+HIsE80FVM1jgbd6WDFhzKYA0PecD/LcIyMQpT4fMJdijBh0I7Iblaacawc"
        crossorigin="anonymous"
    ></script>
</head>
<body>
    <!-- QRコードを表示するためのビデオエレメント -->
    <div class="reader">
        <video
            id="js-video"
            class="reader-video"
            autoplay
            playsinline
        ></video>


         <!-- 画像を表示するための要素 -->
         <a href="../staff/index.php"></a>
         <img id="logo-image" src="../images/logo.jpg" alt="Logo">
         <script>
//    function redirectToStaffPage() {
  //      window.location.href = '../staff/index.php';
    //}
</script>

<div class="return-button">
    <a href="../Staff/index.php">戻る</a>
</div>

    <!-- select要素を画面下から4割の位置に配置 -->
    <select id="location" class="select-overlay">
        <option value="in-8">8号館入口</option>
        <option value="out-8">8号館出口</option>
        <option value="in-6">6号館入口</option>
        <option value="out-6">6号館出口</option>
        <option value="enryo">円了ホール</option>        
        <option value="obake">お化け屋敷</option>
        <option value="PR">東洋PR</option>
        <option value="music">TOYO MUSIC FES</option>
        <option value="lottery">福引</option>
        <option value="stamp">スタンプラリー</option>
    </select>

    <form id="qr-form" action="./php/postman.php" method="POST">
        <input type="hidden" id="qr-value" name="qr" value="">
        <input type="hidden" id="location-value" name="location" value="">
    </form>
    </div>

    <!-- QRコードを検出するための表示 -->
    <div class="reticle">
        <div class="reticle-box"></div>
    </div>

    <!-- キャンバス要素（QRコード画像を描画するために一時的に使用） -->
    <div style="display: none">
        <canvas id="js-canvas"></canvas>
    </div>

<!-- 0.5秒間だけ表示するモーダル -->
    <div id="js-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-cnt">
                <span class="modal-title"></span>
                <textarea id="js-result" class="modal-result" value="" readonly></textarea>
            </div>
        </div>
    </div>

    <!-- サポートされていないブラウザの表示 -->
    <div id="js-unsupported" class="unsupported">
        <p class="unsupported-title">Sorry!</p>
        <p>Unsupported browser</p>
        <div class="return-button">
    <a href="../Staff/index.php">戻る</a>
    </div>

    <!-- JavaScriptファイルを読み込む -->
    <script src="./js/jsQR.js"></script>
    <script src="./js/qr.js"></script>
</body>
</html>
