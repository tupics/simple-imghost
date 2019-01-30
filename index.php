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
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="Uploadimg" />
        <input type="submit" value="Upload" />
    </form>
</body>
</html>