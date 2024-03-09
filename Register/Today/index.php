<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

require './database.php';

$err = [];

// セッションを開始
session_start();

// データ登録日時を記録したいのでここでタイムゾーンを定義。下のcreated_at用。
date_default_timezone_set('Asia/Tokyo');

// フォームデータを取得
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr = $_POST['qr'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

// データベースに接続
$pdo = connect();

// 既存の unique_id の存在を確認
$checkSql = "SELECT COUNT(*) as count FROM db_01_1_GuestToday_master WHERE unique_id = :qr";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->bindParam(':qr', $qr, PDO::PARAM_STR);
$checkStmt->execute();
$checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($checkResult['count'] > 0) {
    echo "<script>alert('既に登録済みです。他の\"T-\"で始まるコードで登録してください。');
    window.location.reload();</script>";
} else {
    // 重複がない場合、データを挿入
    $sql = "INSERT INTO db_01_1_GuestToday_master (unique_id, age, gender) VALUES (:qr, :age, :gender)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':qr', $qr, PDO::PARAM_STR);
    $stmt->bindParam(':age', $age, PDO::PARAM_STR);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "<script>alert('登録に成功しました！ 対応ありがとう!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('登録に失敗しました。 もう一度やり直してください.'); window.location.reload();</script>";
    }
  }
}

?>



<!DOCTYPE html>
<html>
<head>
  <title>白山祭当日登録専用ページ</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="msapplication-tap-highlight" content="no">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <form id="myForm" action="" enctype="multipart/form-data" method="POST">
    <div>
      <div id="video-input">
        <h2>当日登録専用ページ</h2>
        <video id="video" autoplay playsinline style="width: 100%; height: 100%;"></video>
        <div style="display: none;"><canvas id="canvas" width="320" height="320"></canvas></div>

          <label>読み取ったQRコード(T-以外は無効)</label><br>
          <input type="text" name="qr" id="qr" size="30" readonly><br>
          
          <select name="age" id="age">
            <option value="highschool">高校生</option>
            <option value="college">大学生</option>
            <option value="teen-notStudent">10代学生以外</option>
            <option value="20s-notStudent">20代学生以外</option>
            <option value="30s-40s">30〜40代</option>
            <option value="50s-60s">50〜60代</option>
            <option value="70s">70代</option>
            <option value="other">その他</option>
</select>

          <select name="gender" id="gender">
            <option value="man">男性</option>
            <option value="woman">女性</option>
            <option value="noanswer">無回答</option>
          </select>
          <button id="startButton">カメラを起動する</button>
          <button id="submitButton" disabled>送信</button>
      </div>

    </div>
  </form>
  <footer>
    <p>第59回白山祭入構管理システム</p>
    <img id="logo-image" src="./logo.jpg" alt="Logo">
    <style>
        /* #logo-imageに関するスタイル */
        #logo-image {
            position: absolute; /* 絶対位置を設定 */
            width: 80%; /* 幅を100%に設定 */
            left: 50%; /* 左端から50%の位置に配置 */
            transform: translateX(-50%); /* 水平方向に中央寄せ */
            z-index: 1; /* インデックスを設定して他の要素の上に表示 */
            max-width: 400px;
        }
    </style>
</footer>


  <script src="./qrJs/jsQR.js" charset="UTF-8" type="text/javascript"></script>
  <script src="./qr.js" charset="UTF-8" type="text/javascript"></script>
</body>
</html>
