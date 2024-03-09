<?php
session_start();

// セッション変数を全て解除
$_SESSION = array();

// セッションを破棄
session_destroy();

// セッションクッキーを削除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// キャッシュを消去
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// 出展者用ログインページへリダイレクト
header("Location: ./exhibitorSignin.php");
exit;
?>
