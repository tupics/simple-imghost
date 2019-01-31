<?php
require "./sqlite_dnb.php";
require "./verify.php";
require "./scripts/client_ip.php";
if (empty($_FILES))
{
    header("Status: 403");
}
elseif ($_FILES['Uploadimg']['error'] !== 0)
{
    var_dump($_FILES['Uploadimg']);
    echo "Upload error. I want <a href='" . $_SERVER["HTTP_HOST"] . "/index.php'>try again</a>.";
}
elseif ($_FILES['Uploadimg']['error'] == 0)
{
    $tempfilename = $_FILES['Uploadimg']['tmp_name'];
    $imgrealtype = mime_content_type($tempfilename);
    switch ($imgrealtype)
    {
        case "image/jpeg":
            echo "<p>" . $imgrealtype . "</p>";
            $imgext = "jpeg";
            break;
        case "image/png":
            echo "<p>" . $imgrealtype . "</p>";
            $imgext = "png";
            break;
        case "image/webp":
            echo "<p>" . $imgrealtype . "</p>";
            $imgext = "webp";
            break;
        default:
            header("Status: 403");
            die;
            break;
    }
    $tempfile = fopen($tempfilename, "r");
    $tempdata = fread($tempfile,filesize($tempfilename));
    fclose($tempfile);
    $imgcrc32 = hash("crc32", $tempdata);
    $lastimgname = "/uploads/" . $imgcrc32 . '.' . $imgext;
    if (!file_exists('.' . $lastimgname))
    {
        move_uploaded_file($tempfilename, '.' . $lastimgname);
        $networkurl = 'https://' . $_SERVER['HTTP_HOST'] . $lastimgname;
        echo '<a href="' . $networkurl . '">' . $networkurl . '</a>';
        $xlink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $startrecond = $xlink->prepare('INSERT INTO pictures (ID,LOCATION,USER,TIME,IP) VALUES (:crc32, :imgname, :user, :time, :clientip);');
            $startrecond->execute(array('crc32' => $imgcrc32, ':imgname' => $lastimgname, ':user' => $_SESSION['user'], ':time' => time(), ':clientip' => clientIP()));
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    else
    {
        echo $_SERVER['HTTP_HOST'] . $lastimgname;
    }
}