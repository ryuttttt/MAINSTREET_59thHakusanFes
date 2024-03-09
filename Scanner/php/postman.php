<?php
// リクエストがPOSTメソッドでない場合、無効なリクエストとして終了
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // メソッド許可エラー
    die("無効なリクエストメソッドです。");
}

// CSVデータを受け取る
$csvData = file_get_contents("php://input");

// ファイル名を一意に生成 (UUIDを使用)
$uuid = uniqid(); // ユニークなIDを生成
$fileName = "CSV_" . date("Ymd_His") . "_" . $uuid . ".csv";


// 保存ディレクトリのパスを指定
$uploadDir = "../../upload/"; // ドキュメントルートからの相対パス

// ファイルのフルパス
$filePath = $uploadDir . $fileName;

// CSVデータをファイルに書き込み
if (file_put_contents($filePath, $csvData) !== false) {
    http_response_code(200); // 成功
    echo "CSVファイルが正常に保存されました。";
} else {
    http_response_code(500); // サーバーエラー
    echo "CSVファイルの保存中にエラーが発生しました。";
}
?>
