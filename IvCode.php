<?php
require "./sqlite_dnb.php";
require "./verify.php";
require "./scripts/LookupUserLevel.php";
$user = $_SESSION['user'];
$Level = LookupUserLevel($user);
if (!$Level)
{
    die;
}
if (!empty($_POST['piece']))
{
    for ($realp = 0;$realp<=$_POST['piece'];$realp++)
    {
        $Codegen = uniqid("",true) . hash("crc32", date("YmdsH"));
        $AddCodeSql = 'INSERT INTO SYSTEM_IVCODE (ID,CODE,TIME,USER) VALUES (:id,:code,:time,:user);';
        $AddCodeExec = $AddCodeSql->prepare($AddCodeSql);
        $AddCodeExec->execute(array(':id' => hash("crc32", $Codegen), ':code' => $Codegen, ':time' => time(), ':user' => $user));
    }
    echo '<script>location.reload()</script>';
    die;
}
$LookupCodeSql = "SELECT ID,CODE,TIME,USER FROM SYSTEM_IVCODE;";
$LookupCodePrepare = $xlink->prepare($LookupCodeSql);
$LookupCodePrepare->execute();
$Codes = $LookupCodePrepare->fetchAll();
?>
<html>
    <head>
        <title>邀请码管理</title>
    </head>
    <body>
    <h2>当前活跃的邀请码</h2></br>
<?php
if (empty($Codes))
{
    echo "<p>当前没有邀请码</p></br>";
}
else
{
    foreach ($Codes as $Code)
    {
        echo "ID: " . $Code['ID'] . " -- " . "Code: " . $Code['CODE'] . " -- " . "TIME: " . date("Y-m-d H:i:s", $Code['TIME']) . " -- " . "OWNER：" . $Codes['USER'] . "</br>";
    }
}
?>
    <h2>产生新的邀请码</h2></br>
    <form action="" method="POST">
    个数:<input type='text' autocomplete='off' name='piece' required />
    <input type='submit' value='Submit' />
    </form>
    </body>
</html>
