<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>留言板</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        label, input, textarea, input[type="submit"], input[type="button"] {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        input[type="submit"], input[type="button"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
p {
    background-color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    /* 限制宽度并启用换行 */
    word-wrap: break-word; /* 添加断行以防止溢出 */
    max-width: 100%; /* 根据需要调整此值以适应所需空间 */
    overflow: hidden; /* 隐藏超出定义宽度的文本 */
    text-overflow: ellipsis; /* 对超出部分显示省略号 (...) */
}

        hr {
            border: none;
            border-top: 1px solid #ccc;
        }
        body {
            padding-bottom: 100px; /* 为了给底部表单留出空间 */
        }
.fixed-bottom {
            /* 改为相对定位 */
            position: relative;
            width: 100%;
            margin-top: 20px; /* 为了增加内容和底部表单的间距 */
            background-color: white;
            border-top: 1px solid #ccc;
        }

    </style>
</head>
<body>
    <h1>留言板</h1>
<?php
    // 数据库连接配置
    $servername = "localhost";
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

 // 处理提交的留言
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['message'])) {
        $username = isset($_POST['username']) ? $_POST['username'] : 'Anonymous';
        $message = $_POST['message'];

        // 记录当前时间为创建时间
        $created_at = date("Y-m-d H:i:s");

        $sql = "INSERT INTO messages (username, message, created_at) VALUES ('$username', '$message', '$created_at')";

        if ($conn->query($sql) === TRUE) {
            echo "<p>留言已提交成功</p>";
            // 自动重定向回根目录
            header("Location: /");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    // 处理删除全部留言请求
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_password']) && $_POST['delete_password'] == '666') {
        // 验证密码是否匹配
        $delete_password = $_POST['delete_password']; // 用于验证的密码

        // 检查密码是否正确
        if ($delete_password === '666') {
            // 删除全部留言的 SQL 查询
            $sql = "DELETE FROM messages";

            if ($conn->query($sql) === TRUE) {
                echo "<p>留言已全部删除</p>";
            } else {
                echo "Error deleting messages: " . $conn->error;
            }
        } else {
            echo "<p>密码错误，无法删除留言</p>";
        }
    }
    // 显示留言
    $sql = "SELECT * FROM messages ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // 对留言内容进行 htmlspecialchars 转义
            $escaped_message = htmlspecialchars($row['message']);
            echo "<p>" . $escaped_message . "</p><hr>";
        }
    } else {
        echo "<p>暂无留言</p>";
    }

    $conn->close();
    ?>

    <div class="fixed-bottom">
        <h2>留言提交：</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <textarea id="message" name="message" rows="4" placeholder="在这里输入您的留言" required></textarea><br>
        <input type="submit" value="提交留言">
    </form>

        <hr>

        <h2>留言操作：</h2>
        <!-- 删除全部留言表单 -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
            <input type="password" id="delete_password" name="delete_password"  placeholder="输入密码" required>
            <input type="submit" value="删除全部留言">
        </form>
    </div>

  <script>
    const messages = document.querySelectorAll('p');
        let startClickTime = 0;
        const CLICK_THRESHOLD = 200; // 阈值：用于判断是否为点击操作的时间阈值（毫秒）

        messages.forEach(message => {
            message.addEventListener('mousedown', (event) => {
                startClickTime = new Date().getTime(); // 记录鼠标按下时的时间戳
            });

            message.addEventListener('click', (event) => {
                const clickDuration = new Date().getTime() - startClickTime;

                // 判断是否为点击操作
                if (clickDuration < CLICK_THRESHOLD) {
                    const textArea = document.createElement('textarea');
                    textArea.value = message.textContent.trim();
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);

                    const alertMessage = document.createElement('div');
                    alertMessage.textContent = '消息内容已复制！';
                    alertMessage.style.cssText = 'position: fixed; bottom: 50%; left: 50%; transform: translateX(-50%); background-color: #ff80ff; color: white; padding: 10px; z-index: 9999;';

                    document.body.appendChild(alertMessage);

                    setTimeout(() => {
                        alertMessage.style.display = 'none';
                    }, 2000);
                }
            });
        });

    </script>

</body>
</html>
