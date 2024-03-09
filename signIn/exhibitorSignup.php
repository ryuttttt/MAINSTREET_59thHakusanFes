<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

require './database.php';
$err = [];

session_start();

date_default_timezone_set('Asia/Tokyo');

function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$success = false; // success変数を初期化

// 団体名を格納する変数を初期化
$teamName = "";

// チームIDをクエリパラメータから取得
if (isset($_GET['team_id'])) {
    $teamId = $_GET['team_id'];

    // チームIDに対応するチーム名を取得
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT name FROM db_05_team_master WHERE team_id = ?");
    $stmt->execute([$teamId]);
    $teamData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teamData) {
        $teamName = $teamData['name'];
    } else {
        // チームIDに対応するチーム名が見つからない場合の処理
        // 例えばエラーメッセージを設定するなど
        $err[] = "指定されたチームIDが存在しません。";
    }
}

// 最新のレコードのidカラムの値を取得
$pdo = connect();
$stmt = $pdo->query('SELECT id FROM db_03_exhibitor_master ORDER BY id DESC LIMIT 1');
$latestId = $stmt->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $name = $_POST["name"];
    $rawPassword = $_POST["password"];
    $password = password_hash($rawPassword, PASSWORD_DEFAULT);
    $student_id = $_POST["student_id"];

    // チームIDが指定されていない場合の処理
    if (empty($teamName)) {
        $err[] = "チームIDが指定されていません。";
    } else {
        // チーム名が取得できた場合にsuccessをtrueに設定
        $success = true;

        //Unique Idを生成
        $uniqueId = generateUniqueId($latestId, $teamId); // $teamId を渡す

        // データベースにデータを挿入するSQLクエリを作成
        $pdo = connect();
        $stmt = $pdo->prepare("INSERT INTO db_03_exhibitor_master (unique_id, name, password, student_id, team) VALUES (?, ?, ?, ?, ?)");
        
        // フォームからの値をバインドしてSQLクエリを実行
        $stmt->execute([$uniqueId, $name, $password, $student_id, $teamName]);
    }
}

// ユニークIDを作成する関数
function generateUniqueId($latestId, $teamId) {
    $idPartLength = 4 - max(strlen((string)$latestId), 1);
    $idPart = '';
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $idPartLength; $i++) {
        $idPart .= $characters[rand(0, $charactersLength - 1)];
    }

    $randomAlphabet = $characters[rand(0, $charactersLength - 1)];

    // lastIdが4桁未満の場合はアルファベット文字列を生成
    if ($idPartLength > 0) {
        $uniqueId = 'E-' . ($latestId + 1) . $idPart .'!'. $teamId . $randomAlphabet;
    } else {
        // lastIdが4ケタ（アルファベット文字列なし）
        $uniqueId = 'E-' . ($latestId + 1) . '!'.$teamId . $randomAlphabet;
    }

    return $uniqueId;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>出展者登録</title>
    <style>
        html,body{
            height: 100%;
        }
        h2,p{
            text-align:center;
        }
        body {
            font-family: 'Roboto', sans-serif; 
            overflow-x: hidden;
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
    <script>
  // URLからクエリパラメータを取得する関数
  function getQueryParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }

  // ページロード時にteam_idクエリパラメータを取得してコンソールに表示
  const teamId = getQueryParam("team_id");
  console.log("team_id:", teamId);


    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        form.addEventListener("submit", function (event) {
            const nameInput = document.querySelector("input[name='name']");
            const nameValue = nameInput.value;
            const studentIdInput = document.querySelector("input[name='student_id']");
            const studentIdValue = studentIdInput.value;
            
            // エラーメッセージを格納する変数
            let errorMessage = "";

            // 氏名の正規表現パターンを定義
            // 使用できるのは全角カタカナ、半角数字、半角アルファベット、半角スペース
            const namePattern = /^[\p{Script=Katakana}\dA-Za-z\s]+$/u;
            if (!namePattern.test(nameValue) || nameValue.length > 40) {
                event.preventDefault();

                if (!namePattern.test(nameValue)) {
                    errorMessage += "氏名は全角カタカナ、半角数字、半角アルファベット、半角スペースのみで入力してください。\n";
                }

                if (nameValue.length > 40) {
                    errorMessage += "氏名は40文字以下で入力してください。\n";
                }
            }

            // 学籍番号の正規表現パターンを定義
            // 使用できるのは12文字の半角英数字または「学外」
            const studentIdPattern = /^(?:[0-9A-Za-z]{10}|学外)$/;
            if (!studentIdPattern.test(studentIdValue)) {
                event.preventDefault();
                errorMessage += "学籍番号は10文字の半角英数字または「学外」の文字のみを入力してください。\n";
            }

            // エラーメッセージがある場合にalertを表示
            if (errorMessage) {
                alert(errorMessage);
            }
        });
    });
</script>




</script>
</head>
<body>
    <div class="register-container">
        <h2>出展者登録ページ</h2>

        <?php if (!$success): ?>
        <form action="" method="POST">
        <label for="team">団体名:</label>
            <input type="text" id="team" placeholder="団体名" name="team" required readonly value="<?php echo htmlspecialchars($teamName); ?>">
        <label for="name">氏名 (カタカナ):</label>
            <input type="text" id="name" placeholder="トウヨウタロウ" name="name" required>

        <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

        <label for="student_id">学籍番号:</label>
            <input type="text" id="student_id" placeholder="半角英数字または`学外`" name="student_id" required>

            <button type="submit" name="register">登録</button>
        </form>
        <?php else: ?>
            <!--アラートメッセージを表示できるようにする -->
            <?php if ($success): ?>
    <script>
        alert("登録に成功しました！\nスクリーンショットを撮るなど内容を保存してください。\nご協力ありがとうございます！");
    </script>
    <?php endif; ?>
        <p>生成されたユニークID: <?php echo h($uniqueId); ?></p>
        <p>入力されたパスワード: <?php echo h($rawPassword); ?></p>
        <?php endif; ?>

        <p>
        <a href="./exhibitorSignin.php">ログインページへ移動する。</a>
        </p>
        
        <hr>

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
