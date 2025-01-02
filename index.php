<?php
include 'db_connect.php';

// 處理表單提交 (新增數據)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $fishing_date = $_POST['fishing_date'];
    $location = $_POST['location'];
    $catch_amount = $_POST['catch_amount'];
    $vessel_name = $_POST['vessel_name'];
    $fish_type = $_POST['fish_type'];
    $transaction_time = $_POST['transaction_time'];

    if (empty($fishing_date) || empty($location) || empty($catch_amount) || empty($vessel_name) || empty($fish_type) || empty($transaction_time)) {
        echo "所有欄位都是必填的！<br>";
    } else {
        $stmt_vessel = $conn->prepare("INSERT INTO fishing_vessel (Vessel_Name) VALUES (?) ON DUPLICATE KEY UPDATE Vessel_ID = LAST_INSERT_ID(Vessel_ID)");
        $stmt_vessel->bind_param("s", $vessel_name);
        $stmt_vessel->execute();
        $vessel_id = $stmt_vessel->insert_id;
        $stmt_vessel->close();

        $stmt_fishing_data = $conn->prepare("INSERT INTO fishing_data (Fishing_Date, Location, Catch_Amount, Vessel_ID, Fish_Type) VALUES (?, ?, ?, ?, ?)");
        $stmt_fishing_data->bind_param("ssdis", $fishing_date, $location, $catch_amount, $vessel_id, $fish_type);
        
        if ($stmt_fishing_data->execute()) {
            $catch_id = $stmt_fishing_data->insert_id;
            echo "新記錄插入成功！<br>";
        } else {
            echo "插入錯誤: " . $stmt_fishing_data->error . "<br>";
        }

        $stmt_blockchain = $conn->prepare("INSERT INTO blockchain_transaction (Catch_ID, Transaction_Time) VALUES (?, ?)");
        $stmt_blockchain->bind_param("is", $catch_id, $transaction_time);
        $stmt_blockchain->execute();
        $stmt_blockchain->close();
        $stmt_fishing_data->close();
    }
}

// 處理數據刪除
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $catch_id = $_POST['catch_id'];
    if (!empty($catch_id) && is_numeric($catch_id)) {
        $conn->begin_transaction();
        try {
            $stmt_blockchain = $conn->prepare("DELETE FROM blockchain_transaction WHERE Catch_ID = ?");
            $stmt_blockchain->bind_param("i", $catch_id);
            $stmt_blockchain->execute();
            $stmt_blockchain->close();

            $stmt_fishing_data = $conn->prepare("DELETE FROM fishing_data WHERE Catch_ID = ?");
            $stmt_fishing_data->bind_param("i", $catch_id);
            $stmt_fishing_data->execute();
            $stmt_fishing_data->close();
            
            $conn->commit();
            echo "捕撈編號 $catch_id 的數據已成功刪除！<br>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "刪除失敗: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "請輸入有效的捕撈編號！<br>";
    }
}

// 顯示捕撈數據
$sql = "SELECT fd.Catch_ID, fv.Vessel_Name, fd.Fishing_Date, fd.Location, fd.Fish_Type, fd.Catch_Amount, bt.Transaction_Time FROM Fishing_Data fd JOIN Fishing_Vessel fv ON fd.Vessel_ID = fv.Vessel_ID LEFT JOIN Blockchain_Transaction bt ON fd.Catch_ID = bt.Catch_ID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>漁業數據管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            font-weight: bold;
        }
        input, button {
            margin: 10px 0;
            padding: 10px;
            width: 90%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>漁業數據管理</h1>
    
    <h2>新增捕撈數據</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add">
        <label>捕撈日期:</label>
        <input type="date" name="fishing_date" required><br>
        <label>捕撈地點:</label>
        <input type="text" name="location" required><br>
        <label>捕撈量 (kg):</label>
        <input type="number" name="catch_amount" step="0.1" required><br>
        <label>漁船名稱:</label>
        <input type="text" name="vessel_name" required><br>
        <label>漁獲種類:</label>
        <input type="text" name="fish_type" required><br>
        <label>交易時間:</label>
        <input type="datetime-local" name="transaction_time" required><br>
        <button type="submit">新增數據</button>
    </form>

    <h2>刪除捕撈數據</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="delete">
        <label>捕撈編號:</label>
        <input type="number" name="catch_id" required><br>
        <button type="submit">刪除數據</button>
    </form>

    <h2>捕撈數據列表</h2>
    <table>
        <tr>
            <th>捕撈編號</th>
            <th>漁船名稱</th>
            <th>捕撈時間</th>
            <th>捕撈地點</th>
            <th>漁獲種類</th>
            <th>捕撈量</th>
            <th>交易時間</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['Catch_ID']; ?></td>
                <td><?php echo $row['Vessel_Name']; ?></td>
                <td><?php echo date("Y-m-d H:i:s", strtotime($row['Fishing_Date'])); ?></td>
                <td><?php echo $row['Location']; ?></td>
                <td><?php echo $row['Fish_Type']; ?></td>
                <td><?php echo $row['Catch_Amount']; ?> kg</td>
                <td><?php echo date("Y-m-d H:i:s", strtotime($row['Transaction_Time'])); ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
