<?php
require_once "../status/DatabaseCon.php";
require "./verify.php";
require_once "../scripts/client_ip.php";
require "../scripts/UploadProgress.php";
if (empty($_FILES))
{
    header("Status: 403");
}
elseif ($_FILES['Uploadimg']['error'] !== 0)
{
    var_dump($_FILES['Uploadimg']);
    echo "Upload error. I want <a href='./index.php'>try again</a>.";
}
elseif ($_FILES['Uploadimg']['error'] == 0)
{
    $DirPrefix = "..";
    $UploadF = new UploadFile;
    $UploadF->Upload($_FILES['Uploadimg'], $xlink, $DirPrefix, clientIP(), $_SESSION['user']);
    $OutputF = "%s</br><a href='..%2\$s'>%2\$s</a></br>";
    printf($OutputF, $UploadF->FileType['MIME'], $UploadF->Locations['Fake']);
}
?>
<a href="./index.php">Return</a>
