<?php
require "./sqlite_dnb.php";
require "./verify.php";
$xlink=null;
?>
<html>
<head>
<title>VERY SIMPLE IMG HOST</title>
</head>
<body>
    <a href="./mgr_pic.php">我的图片[管理员可管理全服图片]</a>
    </br>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="Uploadimg" />
        <input type="submit" value="Upload" />
    </form>
</body>
</html>