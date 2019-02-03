<?php
if (file_exists("sqlite_dnb.php"))
{
    echo "Setup can not run here";
    die;
}
?>
<form method="POST">
    <p>Admin Account:</p>
    <input type="text" name="suser" required/>
    <p>Password:</p>
    <input type="password" name="spassword" required/>
    <input type="submit" name="Submit" />
</form>
<?php
if (isset($_POST['suser']) && isset($_POST['spassword']))
{
    $suser = $_POST['suser'];
    require "./scripts/PasswordWays.php";
    $spassword = $PasswordWaysH->MakeHash($_POST['spassword']);
    mkdir("./db", 755);
    $dbname = hash("crc32", md5(rand())) . "db" . rand(0, 9);
    $rlink = new PDO("sqlite:./db/" . $dbname . '.' . 'db');
    $rlink->beginTransaction();
    $rlink->exec("CREATE TABLE SYSTEM_LOGIF(
                    ID INTEGER PRIMARY KEY AUTOINCREMENT,
                    NAME TEXT NOT NULL,
                    PASSWORD TEXT NOT NULL,
                    ADMIN INTEGER NOT NULL,
                    IP TEXT NOT NULL);");
    $rlink->exec("CREATE TABLE SYSTEM_LOGREC(
                    ID TEXT PRIMARY KEY NOT NULL,
                    RECKE TEXT NOT NULL,
                    USER TEXT NOT NULL,
                    TIME INTEGER NOT NULL);");
    $rlink->exec("CREATE TABLE pictures(
                    ID TEXT PRIMARY KEY NOT NULL,
                    LOCATION TEXT NOT NULL,
                    USER TEXT NOT NULL,
                    TIME INTEGER NOT NULL,
                    IP TEXT NOT NULL);");
    $rlink->exec("CREATE TABLE SYSTEM_IVCODE(
                    ID TEXT PRIMARY KEY NOT NULL,
                    CODE TEXT NOT NULL,
                    TIME INTEGER NOT NULL,
                    USER TEXT NOT NULL);");
    $rlink->commit();
    $initsql = "INSERT INTO SYSTEM_LOGIF (NAME,PASSWORD,ADMIN,IP) VALUES (?,?,1,?);";//预准备
    $initdb = $rlink->prepare($initsql);
    require "./scripts/client_ip.php";
    $initdb->execute(array($suser,$spassword,clientIP()));//执行
    $dbconfig = '<?php if (!isset($xlink)) {$xlink = new PDO("sqlite:./db/' . $dbname . '.' . 'db' . '");}';
    if (!file_exists("./sqlite_dnb.php"))
    {
        $dnbconfig = fopen("./sqlite_dnb.php", "w");
        fwrite($dnbconfig, $dbconfig);
    }
    else
    {
        echo "Cloud not create dbconfig, Plese Check it.";
    }
    $rlink = null;
}