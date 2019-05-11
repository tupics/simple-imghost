<?php
require_once "../status/DatabasrCon.php";
require "./verify.php";
require "../scripts/LookupUserLevel.php";
$AccessLevel = LookupUserLevel($_SESSION['user']);
?>
<html>
<head>
<title>VERY SIMPLE IMG HOST</title>
</head>
<body>
    <a href="./mgr_pic.php">我的图片[管理员可管理全服图片]</a>
    </br>
    <a href="./logout.php">登出</a>
    </br>
    <?php
    if ($AccessLevel)
    {
        echo "<a href='IvCode.php'>邀请码管理</a>";
    }
    ?>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="Uploadimg" />
        <input type="submit" value="Upload" />
    </form>
</body>
</html>