<?php
// 資料庫配置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fishing_db";  // 使用的資料庫名稱

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 檢查是否有捕撈編號提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $catch_id = $_POST['catch_id'];  // 捕撈編號

    // 確保捕撈編號存在並且為數字
    if (!empty($catch_id) && is_numeric($catch_id)) {
        // 開始事務處理
        $conn->begin_transaction();

        try {
            // 刪除 blockchain_transaction 表中的相關記錄
            $sql_blockchain = "DELETE FROM blockchain_transaction WHERE Catch_ID = ?";
            if ($stmt_blockchain = $conn->prepare($sql_blockchain)) {
                $stmt_blockchain->bind_param("i", $catch_id);
                $stmt_blockchain->execute();
                $stmt_blockchain->close();
            }

            // 刪除 fishing_data 表中的記錄
            $sql_fishing_data = "DELETE FROM Fishing_Data WHERE Catch_ID = ?";
            if ($stmt_fishing_data = $conn->prepare($sql_fishing_data)) {
                $stmt_fishing_data->bind_param("i", $catch_id);
                if ($stmt_fishing_data->execute()) {
                    echo "捕撈編號為 $catch_id 的資料已成功刪除！";
                } else {
                    echo "刪除失敗: " . $stmt_fishing_data->error;
                }
                $stmt_fishing_data->close();
            }

            // 提交事務
            $conn->commit();
        } catch (Exception $e) {
            // 如果有錯誤，回滾事務
            $conn->rollback();
            echo "錯誤: " . $e->getMessage();
        }
    } else {
        echo "請輸入有效的捕撈編號！";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>刪除捕撈數據</title>
</head>
<body>
    <h2>刪除捕撈數據</h2>
    <form method="POST" action="delete_fishing_data.php">
        <label for="catch_id">捕撈編號:</label><br>
        <input type="number" id="catch_id" name="catch_id" required><br><br>
        <input type="submit" value="刪除">
    </form>
</body>
</html>
