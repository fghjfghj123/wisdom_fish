<?php
$servername = "localhost";
$username = "root";
$password = "";  // 通常XAMPP預設密碼為空
$dbname = "fishing_db";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
echo "連線成功";
?>
