<?php
require '../Scanner/php/database.php';

function insertIntoLog($location, $unique_id, $created_at, $pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO db_04_log_master (`location`, `unique_id`, `created_at`) VALUES (?, ?, ?)");
        $stmt->execute([$location, $unique_id, $created_at]);

        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
        echo "エラー: $errorMessage\n";
        return null;
    }
}

function processCsvFile($filePath, $pdo) {
    $fileData = file_get_contents($filePath);
    $lines = explode(PHP_EOL, $fileData);

    // ヘッダーを除いて処理
    $headerSkipped = false;

    foreach ($lines as $line) {
        if (!$headerSkipped) {
            $headerSkipped = true;
            continue;
        }

        $data = str_getcsv($line);
        if (count($data) === 3) {
            $unique_id = $data[0];
            $location = $data[1];
            $created_at = $data[2];

            $lastInsertId = insertIntoLog($location, $unique_id, $created_at, $pdo);
            if ($lastInsertId) {
                echo "データの登録に成功しました。ID: $lastInsertId\n";
            } else {
                echo "データの登録に失敗しました。\n";
                // データベースへの挿入ができなかった場合、ファイルを移動
                $sourcePath = $filePath;
                $destinationPath = './false/' . basename($filePath);
                rename($sourcePath, $destinationPath);
            }
        }
    }
}

try {
    $pdo = connect();
    $pdo->beginTransaction();

    // ディレクトリ内のcsvファイルを処理
    $directory = __DIR__;
    $files = scandir($directory);

    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
            $filePath = $directory . '/' . $file;
            processCsvFile($filePath, $pdo);
            // ファイルを処理した後に削除する場合は以下の行を有効化
            // unlink($filePath);
            $donePath = './done/' . basename($filePath);
            rename($filePath, $donePath);
        }
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "エラー: " . $e->getMessage();
}
?>
