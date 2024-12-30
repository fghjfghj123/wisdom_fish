<?php
// 資料庫配置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fishing_db";

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 獲取表單數據
    $fishing_date = $_POST['fishing_date'];
    $location = $_POST['location'];
    $catch_amount = $_POST['catch_amount'];
    $vessel_name = $_POST['vessel_name'];  // 漁船名稱
    $fish_type = $_POST['fish_type'];  // 漁獲種類
    $transaction_time = $_POST['transaction_time'];  // 交易時間

    // 數據驗證
    if (empty($fishing_date) || empty($location) || empty($catch_amount) || empty($vessel_name) || empty($fish_type) || empty($transaction_time)) {
        echo "所有欄位都是必填的！";
        exit;
    }

    // 插入漁船資料
    $stmt_vessel = $conn->prepare("INSERT INTO fishing_vessel (Vessel_Name) VALUES (?)");
    $stmt_vessel->bind_param("s", $vessel_name);
    $stmt_vessel->execute();
    $vessel_id = $stmt_vessel->insert_id; // 獲取插入的漁船ID
    $stmt_vessel->close();

    // 使用準備語句插入數據到 fishing_data 表
    $stmt_fishing_data = $conn->prepare("INSERT INTO fishing_data (Fishing_Date, Location, Catch_Amount, Vessel_ID, Fish_Type) VALUES (?, ?, ?, ?, ?)");
    $stmt_fishing_data->bind_param("ssdis", $fishing_date, $location, $catch_amount, $vessel_id, $fish_type);

    if ($stmt_fishing_data->execute()) {
        // 插入成功後獲取新插入的 Catch_ID
        $catch_id = $stmt_fishing_data->insert_id;
        echo "<p style='color: green;'>新記錄插入成功！</p>";
    } else {
        echo "<p style='color: red;'>錯誤: " . $stmt_fishing_data->error . "</p>";
    }

    // 插入區塊鏈交易資料（不再包含哈希值）
    $stmt_blockchain = $conn->prepare("INSERT INTO blockchain_transaction (Catch_ID, Transaction_Time) VALUES (?, ?)");
    $stmt_blockchain->bind_param("is", $catch_id, $transaction_time);
    $stmt_blockchain->execute();
    $stmt_blockchain->close();

    // 關閉 fishing_data 準備語句
    $stmt_fishing_data->close();
}

?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加漁業數據</title>
</head>
<body>
    <h2>添加漁業數據</h2>
    <form method="POST" action="add_fishing_data.php">
        <label for="fishing_date">捕撈日期:</label><br>
        <input type="date" id="fishing_date" name="fishing_date" required><br><br>
        
        <label for="location">捕撈地點:</label><br>
        <input type="text" id="location" name="location" required><br><br>
        
        <label for="catch_amount">捕撈量 (kg):</label><br>
        <input type="number" id="catch_amount" name="catch_amount" step="0.1" required><br><br>

        <label for="vessel_name">漁船名稱:</label><br>
        <input type="text" id="vessel_name" name="vessel_name" required><br><br>
        
        <label for="fish_type">漁獲種類:</label><br>
        <input type="text" id="fish_type" name="fish_type" required><br><br>
        
        <label for="transaction_time">交易時間:</label><br>
        <input type="datetime-local" id="transaction_time" name="transaction_time" required><br><br>
        
        <input type="submit" value="提交">
    </form>
</body>
</html>
