<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// セッションのセキュリティ強化
ini_set('session.cookie_httponly', true);
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    ini_set('session.cookie_secure', true);
}

session_start();

if (!function_exists('h')) {
    function h($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
    }
}

include './database.php';
$pdo = connect();

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $uniqueId = $_POST["unique_id"];
    $password = $_POST["password"];

    // unique_idでユーザーを検索
    $stmt = $pdo->prepare("SELECT * FROM db_02_staff_master WHERE unique_id = ?");
    $stmt->execute([$uniqueId]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // ハッシュ化されたパスワードと入力されたパスワードを比較
        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);  // セッション固定攻撃を防ぐためのセキュリティ強化

            $_SESSION["loggedin"] = true;
            $_SESSION["unique_id"] = $user["unique_id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["student_id"] = $user["student_id"]; 
            $_SESSION["team"] = $user["team"];
            $_SESSION["section"] = $user["section"];
            $_SESSION["grade"] = $user["grade"];
            $_SESSION["login_time"] = time();

            header("Location: ../Staff/index.php"); 
            exit;
        } else {
            $errorMessage = "パスワードが間違っています。";
        }
    } else {
        $errorMessage = "指定されたunique_idのユーザーが見つかりません。";
    }
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="manifest" href="../pwa/manifest.json">
    <script>
    // サービスワーカーの登録
    if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('../sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
    }

    sessionStorage.clear();


    document.cookie.split(";").forEach((c) => {
  document.cookie = c
    .replace(/^ +/, "")
    .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
    });



        // スクロールの禁止
        window.addEventListener('touchmove', function (e) {
            e.preventDefault();
        }, { passive: false });

        window.addEventListener('wheel', function (e) {
            e.preventDefault();
        }, { passive: false });
    </script>
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

        .login-container {
            width: 90%;
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-top: 0;
            color: #333;
            font-size:3vh;
        }

        form {
            display: grid;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-left: 5px;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #2196f3;
            outline: none;
        }

        button {
            width: 100%;
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 10px;
            }
        }
    </style>

    <title>ログイン</title>
</head>
<body>
    <!-- ログインフォーム -->
    <div class="login-container">
        <h2>第59回白山祭入構管理システム -MAINSTREET-</h2> <br>
        <h2>白祭実ログイン(SafariかChromeで開いてください。)</h2>
        <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo h($errorMessage); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="unique_id">unique_id:</label>
            <input type="text" id="unique_id" name="unique_id" required>
            
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" name="login">ログイン</button>
        </form>
    </div>

    
    <img id="logo-image" src="../images/logo.jpg" alt="Logo">
    <style>
    body{
        width:100%;
    }
        /* #logo-imageに関するスタイル */
#logo-image {
    position: absolute; /* 絶対位置を設定 */
    width:100%; /* 幅を100%に設定 */
    bottom: 0%; /* 下端を基準に位置を設定 */
    left: 50%; /* 左端から50%の位置に配置 */
    transform: translateX(-50%); /* 水平方向に中央寄せ */
    z-index: 1; /* インデックスを設定して他の要素の上に表示 */
}
    </style>
    
</body>
</html>
