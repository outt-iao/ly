<?php
// 数据库连接配置
$servername = "120.76.41.136";
$username = "admin";
$password = "outt@123";
$dbname = "test";
$port = 3306;

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// 检查连接是否成功
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 删除所有留言
$sql = "TRUNCATE TABLE messages";
if ($conn->query($sql) === TRUE) {
    echo "<p>已删除所有留言</p>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
