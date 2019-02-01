<?php
if (!empty($_POST['nuser']) && !empty($_POST['npassword']) && !empty($_POST['IvCode']))
{
    require "./sqlite_dnb.php";
    require "./scripts/client_ip.php";
    require "./scripts/PasswordWays.php";
    $nuser = $_POST['nuser'];
    $npassword = $PasswordWaysH->MakeHash($_POST['npassword']);
    $IvCode = $_POST['IvCode'];
    $CheckIvCodeSql = "SELECT ID FROM SYSTEM_IVCODE WHERE CODE = ?;";
    $RunCheckIv = $xlink->prepare($CheckIvCodeSql);
    $RunCheckIv->execute(array($IvCode));
    $CheckingIV = $RunCheckIv->fetchAll(PDO::FETCH_COLUMN, 0);
    if (empty($CheckingIV))
    {
        echo "GG！错误的邀请码";
        echo "<script>location.replace('https://cn.bing.com')</script>";
    }
    else
    {
        $CheckUserExistsSql = "SELECT ID FROM SYSTEM_LOGIF WHERE NAME =?;";
        $RunCheckUserName = $xlink->prepare($CheckUserExistsSql);
        $RunCheckUserName->execute(array($nuser));
        $CheckingUN = $RunCheckUserName->fetchAll(PDO::FETCH_COLUMN, 0);
        if (!empty($CheckingUN))
        {
            echo "更换用户名后重试";
            echo "<script>location.reload()</script>";
        }
        else
        {
            $AddUserSql = 'INSERT INTO SYSTEM_LOGIF (NAME,PASSWORD,ADMIN,IP) VALUES (?,?,0,?);';
            $RunAddUser = $xlink->prepare($AddUserSql);
            $RunAddUser->execute(array($nuser,$npassword,clientIP()));
            echo "Enjoy it!";
        }
    }
    exit;
}
?>
<html>
<head>
    <title>加入我们！</title>
</head>
<body>
    <form action="" method='POST'>
        用户名:<input type='text' name='nuser' required /></br>
        密码:<input type='password' name='npassword' required /></br>
        邀请码:<input type='password' name='IvCode' required /></br>
        <input type='submit' value='Submit' />
    </form>
</body>
</html>
