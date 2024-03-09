<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

require 'database.php';

$err = [];

// セッションを開始
session_start();

// データ登録日時を記録したいのでここでタイムゾーンを定義。下のcreated_at用。
date_default_timezone_set('Asia/Tokyo');

function generateUniqueId() {
    $pdo = connect();
    
    // 最新のレコードのidカラムの値を取得
    $stmt = $pdo->query('SELECT id FROM db_01_guest_master ORDER BY id DESC LIMIT 1');
    $latestId = $stmt->fetchColumn();

    // idPartの生成
    // idはG-,idPart(lastId+ランダムなアルファベット文字列=always5),day,ランダムなアルファベット一文字で生成
    // それぞれ定義
    $idPartLength = 5 - max(strlen($latestId), 1);  
    $idPart = '';
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $idPartLength; $i++) {
        $idPart .= $characters[rand(0, $charactersLength - 1)];
    }

    $md = date("d");
    $randomAlphabet = $characters[rand(0, $charactersLength - 1)];

    // lastIdが5桁未満の場合はアルファベット文字列を生成
    if ($idPartLength > 0) {
        $uniqueId = 'G-' . ($latestId + 1) . $idPart . $md . $randomAlphabet;
    } else {
        // lastIdが5ケタ（アルファベット文字列なし）
        $uniqueId = 'G-' . ($latestId + 1) . $md . $randomAlphabet;
    }
    
    return $uniqueId;
}

// 送信ボタンが押されたら
// nameは必ず入力する必要有。jsが動かなくてもサーバー側で対応
// SQL文準備、postされた内容をセット、実行
// その後はQRコード生成のための準備
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $name = filter_input(INPUT_POST, 'name');
    $age = filter_input(INPUT_POST, 'age');
    $gender = filter_input(INPUT_POST, 'gender');
    $created_at = date("Y-m-d H:i:s"); // タイムスタンプ形式で取得

    if (empty($name)) {
        $err[] = "氏名(カタカナ)は必須です。";
    }

    if (empty($err)) {
        $uniqueId = generateUniqueId();
        $pdo = connect();

        $stmt = $pdo->prepare('INSERT INTO `db_01_guest_master` (`id`,`name`,`unique_id`,`created_at`,`age`,`gender`) VALUES (null, ?, ?, ?, ?, ?)');

        // パラメータ設定
        $params = [];
        $params[] = $name;
        $params[] = $uniqueId;
        $params[] = $created_at;
        $params[] = $age;
        $params[] = $gender;

        $stmt->execute($params);

        // 最後に挿入された、自分が挿入したレコードのidを取得
        $newId = $pdo->lastInsertId();

        // セッションにデータを保存
        $_SESSION['user_data'] = [
            'id' => $newId,
            'name' => $name,
            'unique_id' => $uniqueId,
            'created_at' => $created_at,
            'age' => $age,
            'gender' => $gender,
        ];

        // リダイレクト
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// GETリクエストの処理
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    if (isset($_SESSION['user_data'])) {
        // セッションにデータがある場合はそれを表示
        $recordObject = (object) $_SESSION['user_data'];

        $ageLabels = [
            "highschool" => "高校生",
            "college" => "大学生",
            "teen-notStudent" => "10代学生以外",
            "20s-notStudent" => "20代学生以外",
            "30s-40s" => "30〜40代",
            "50s-60s" => "50〜60代",
            "70s" => "70代",
            "other" => "その他"
        ];
        $genderLabels = [
            "woman" => "女性",
            "man" => "男性",
            "noanswer" => "無回答"
        ];
        $recordObject->age = $ageLabels[$recordObject->age];
        $recordObject->gender = $genderLabels[$recordObject->gender];
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/staff.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <title>第59回白山祭 来場登録</title>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        form.addEventListener("submit", function (event) {
        const nameInput = document.querySelector("input[name='name']");
        const nameValue = nameInput.value;

        // 正規表現パターンを定義
        // 使用できるのは全角カタカナ、半角数字、半角アルファベット、半角スペース
        const pattern = /^[\p{Script=Katakana}\dA-Za-z\s]+$/u; 

        if (!pattern.test(nameValue) || nameValue.length > 40) {
            event.preventDefault(); 

            if (!pattern.test(nameValue)) {
                alert("氏名は全角カタカナ、半角数字、半角アルファベット、半角スペースのみで入力してください。");
            } else {
                alert("氏名は40文字以下で入力してください。");
            }
        }
    });
});
</script>

</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo-pic"> <img src="../images/staff.png" alt="Logo"> </div>
            <div class="header-logo"> 第59回白山祭</div>
        </div>

        <div class="main">
            <div class="catch-copy">
                <h2>来場登録</h2>
            </div>

           
            <form action="" method="post" <?php if (isset($recordObject)) echo 'style="display: none;"'; ?>>
                <p>以下の項目を入力してください。</p>
                <ul class="form-list">
                    <li>
                        <label>氏名(カタカナ)</label> <br>
                        <input type="text" name="name" placeholder="例:シロヤマタロウ" required>
                    </li>
                    
                    <li>
                        <label>年代</label> <br>
                        <select name="age" required>
                            <option value="highschool">高校生</option>
                            <option value="college">大学生</option>
                            <option value="teen-notStudent">10代学生以外</option>
                            <option value="20s-notStudent">20代学生以外</option>
                            <option value="30s-40s">30〜40代</option>
                            <option value="50s-60s">50〜60代</option>
                            <option value="70s">70代</option>
                            <option value="other">その他</option>
                        </select>
                    </li>
                    
                    <li>
                        <label>性別</label> <br>
                        <select name="gender" required>
                            <option value="woman">女性</option>
                            <option value="man">男性</option>
                            <option value="noanswer">無回答</option>
                        </select>
                    </li>

                    <li>
                        <input type="submit" value="送信">
                    </li>
                </ul>
                <?php if (!empty($err)): ?>
                    <ul class="error-list">
                        <?php foreach ($err as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </form>

            <div class="qrcode" id="qrCode" <?php if (!isset($recordObject)) echo 'style="display: none;"'; ?>></div>

            <div class="readme" <?php if (!isset($recordObject)) echo 'style="display: none;"'; ?>>
                <?php 
                //$recordObjectがセットされているときに実行
                if (isset($recordObject)): 
                // JavaScriptでアラートを表示
                echo "<script> alert('登録に成功しました！\\nQRコードをスクリーンショットで保存してください。\\nご来場心よりお待ちしております!');</script>";
                ?>
                <p>ID: <?php echo $recordObject->unique_id; ?></p>
                <p>氏名(カタカナ): <?php echo $recordObject->name; ?></p>
                <p>年代: <?php echo $recordObject->age; ?></p>
                <p>性別: <?php echo $recordObject->gender; ?></p>
                <p>登録日時: <?php echo $recordObject->created_at; ?></p>

                <script src="https://unpkg.com/qr-code-styling@1.6.0-rc.1/lib/qr-code-styling.js"></script>
                <script>//qrCode生成部分
                    const qrCode = new QRCodeStyling({
                        width: 300,
                        height: 300,
                        type: "svg",
                        data: "<?php echo urlencode($recordObject->unique_id); ?>",
                        //来場者用画像の設定場所
                        image: "../images/register.jpg",
                        qrOptions: {
                            errorCorrectionLevel: 'H'
                        },
                        dotsOptions: {
                            color: "#4267b2",
                            type: "square"
                        },
                        cornersSquareOptions:{
                            type: "square"
                        },
                        cornersDotOptions: {
                            type: "square"
                        },
                        backgroundOptions: {
                            color: "#fff",
                        },
                        imageOptions: {
                            crossOrigin: "anonymous",
                            margin: 0,
                        }
                    });

                    qrCode.append(document.getElementById('qrCode'));
                </script>
                <?php endif; ?>
                <p>登録に成功しました！<br>QRコードをスクリーンショットで保存してください。<br>実行委員に提示を求められた際は<br>このQRコードを提示してください。</p>

                <p>Thank You!</p>
            </div>
        </div>

        <footer>
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
        </footer>
    </div>
</body>
</html>
