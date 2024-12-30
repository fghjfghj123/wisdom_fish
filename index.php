<?php
include 'db_connect.php';

// SQL 查詢：將捕撈數據、漁船資訊和交易記錄結合
$sql = "SELECT fd.Catch_ID, fv.Vessel_Name, fd.Catch_Time, fd.Catch_Location, fd.Fish_Type, fd.Catch_Weight, bt.Transaction_Time, bt.Hash_Value
        FROM Fishing_Data fd
        JOIN Fishing_Vessel fv ON fd.Vessel_ID = fv.Vessel_ID
        LEFT JOIN Blockchain_Transaction bt ON fd.Catch_ID = bt.Catch_ID";

$result = $conn->query($sql);

// 檢查查詢是否成功
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
                <th>捕撈重量</th>
                <th>交易時間</th>
                
            </tr>";

    while ($row = $result->fetch_assoc()) {
        // 格式化捕撈時間和交易時間
        $catch_time = ($row['Catch_Time'] === NULL) ? '未知' : date("Y-m-d H:i:s", strtotime($row['Catch_Time']));
        $transaction_time = ($row['Transaction_Time'] === NULL) ? '未知' : date("Y-m-d H:i:s", strtotime($row['Transaction_Time']));

        // 檢查捕撈重量和捕撈地點是否為NULL
        $catch_weight = ($row['Catch_Weight'] === NULL) ? '未知' : $row['Catch_Weight'] . ' kg';
        $catch_location = ($row['Catch_Location'] === NULL) ? '未知' : $row['Catch_Location'];

        // 顯示每一行數據
        echo "<tr>
                <td>{$row['Catch_ID']}</td>
                <td>{$row['Vessel_Name']}</td>
                <td>{$catch_time}</td>
                <td>{$catch_location}</td>
                <td>{$row['Fish_Type']}</td>
                <td>{$catch_weight}</td>
                <td>{$transaction_time}</td>
                <td>{$row['Hash_Value']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "無數據";
}

$conn->close();
?>
