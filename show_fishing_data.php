<?php
include 'db_connect.php';

$sql = "SELECT fd.Catch_ID, fv.Vessel_Name, fd.Fishing_Date, fd.Location, fd.Fish_Type, fd.Catch_Amount, bt.Transaction_Time 
        FROM Fishing_Data fd
        JOIN Fishing_Vessel fv ON fd.Vessel_ID = fv.Vessel_ID
        LEFT JOIN Blockchain_Transaction bt ON fd.Catch_ID = bt.Catch_ID";

$result = $conn->query($sql);

// 檢查是否有錯誤
if ($result === false) {
    echo "SQL 查詢錯誤: " . $conn->error;
} else {
    echo "結果數量: " . $result->num_rows . "<br>";
}

echo "<h2>捕撈數據與交易紀錄</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>捕撈編號</th>
                <th>漁船名稱</th>
                <th>捕撈時間</th>
                <th>捕撈地點</th>
                <th>漁獲種類</th>
                <th>捕撈量</th>
                <th>交易時間</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        // 檢查捕撈量與捕撈地點是否為空，並顯示提示文字
        $catch_weight = !empty($row['Catch_Amount']) ? $row['Catch_Amount'] . ' kg' : '無資料';
        $catch_location = !empty($row['Location']) ? $row['Location'] : '無資料';

        // 格式化日期
        $catch_time = date("Y-m-d H:i:s", strtotime($row['Fishing_Date']));
        $transaction_time = date("Y-m-d H:i:s", strtotime($row['Transaction_Time']));

        // 顯示資料表格
        echo "<tr>
                <td>{$row['Catch_ID']}</td>
                <td>{$row['Vessel_Name']}</td>
                <td>{$catch_time}</td>
                <td>{$catch_location}</td>
                <td>{$row['Fish_Type']}</td>
                <td>{$catch_weight}</td>
                <td>{$transaction_time}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "無數據";
}

$conn->close();
?>
