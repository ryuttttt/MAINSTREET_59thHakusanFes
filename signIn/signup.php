<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

if (!function_exists('h')) {
    function h($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
    }
}


include 'database.php';
$pdo = connect();

$success = false;
$uniqueId = "";
$rawPassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {

    // フォームからのデータを受け取り。
    $username = $_POST["username"];
    $rawPassword = $_POST["password"];
    $password = password_hash($rawPassword, PASSWORD_DEFAULT);
    $student_id = $_POST["student_id"];
    $team = $_POST["team"];
    $section = $_POST["section"];
    $grade = $_POST["grade"];

    // INSERT文を準備
    $stmt = $pdo->prepare("INSERT INTO test_02_staff_master (unique_id, name, password, student_id, team, section, grade) VALUES (null, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $password, $student_id, $team, $section, $grade]);

    $lastId = $pdo->lastInsertId();

    // unique_idの値を生成
    $prefix = "S-";
    switch ($team) {
        case "総務":
            $prefix .= "G-";
            break;
        case "企画":
            $prefix .= "P-";
            break;
        case "広報":
            $prefix .= "R-";
            break;
        case "渉外":
            $prefix .= "E-";
            break;
        case "会計":
            $prefix .= "A-";
            break;
    }
    $uniqueId = $prefix . $lastId;

    // UPDATE文でunique_idを更新
    $updateStmt = $pdo->prepare("UPDATE test_02_staff_master SET unique_id = ? WHERE id = ?");
    $updateStmt->execute([$uniqueId, $lastId]);

    $success = true;  // 登録成功フラグをtrueに設定
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="manifest" href="../pwa/manifest.json">
    <title>ユーザー登録</title>
    <style>
        html,body{
            height: 100%;
        }
        body {
            font-family: 'Roboto', sans-serif; 
            background-color: #ECEFF1;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            width: 90%;
            height:80%;
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        form {
            display: grid;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-left: 5px;
        }

        input[type="text"], input[type="password"], select {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            border-color: #2196f3;
            outline: none;
        }

        button {
            width: 100%;
        }

        .title{
           text-align:center; 
        }

        @media (max-width: 600px) {
            .register-container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- ユーザー登録フォーム -->
    <div class="register-container">
        <h2>ユーザー登録</h2>
        <p>11/3付で登録ページを閉鎖しました。ログインページからログインしてください。</p>
        <a href="./login.php">ログインページへ移動する。</a>
        
        <img id="logo-image" src="../images/logo.jpg" alt="Logo">
    <style>
        /* #logo-imageに関するスタイル */
#logo-image {
    position: absolute; /* 絶対位置を設定 */
    width:100%; /* 幅を100%に設定 */
    bottom: 0%; /* 下端を基準に位置を設定 */
    left: 50%; /* 左端から50%の位置に配置 */
    transform: translateX(-50%); /* 水平方向に中央寄せ */
    z-index: 1; /* インデックスを設定して他の要素の上に表示 */
}
    </style>    </div>

    <script>
        // スクロールの禁止
        window.addEventListener('touchmove', function (e) {
            e.preventDefault();
        }, { passive: false });

        window.addEventListener('wheel', function (e) {
            e.preventDefault();
        }, { passive: false });
    </script>
    
</body>
</html>
