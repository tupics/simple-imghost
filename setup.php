<?php
if (file_exists("./status/DatabaseCon.php"))
{
    var_dump(file_exists("./status/DatabaseCon.php"));
    echo "Setup can not run here";
    die;
}
?>
<form method="POST">
    <p>Admin Account:</p>
    <input type="text" name="info[user]" required/>
    <p>Password:</p>
    <input type="password" name="info[password]" required/>
    <p>Mysql:</p>
    <h6>Address:</h6>
    <input type="text" name="mysql[address]" required/>
    <h6>Port:</h6>
    <input type="text" name="mysql[port]" required/>
    <h6>User:</h6>
    <input type="text" name="mysql[user]" required/>
    <h6>Password:</h6>
    <input type="password" name="mysql[password]" required/>
    <h6>Database:</h6>
    <input type="text" name="mysql[database]" required/>
    <input type="submit" name="Submit" />
</form>
<?php
if (isset($_POST['info']) && isset($_POST['mysql']))
{
    $info = $_POST['info'];
    $mysql = $_POST['mysql'];
    require "./scripts/PasswordWays.php";
    $PWH = new PasswordWaysH;
    $spassword = $PWH->MakeHash($info['password']);
    $MysqlDSNFormat = "mysql:host=%s;port=%s;dbname=%s";
    $MysqlDSN = sprintf($MysqlDSNFormat, $mysql['address'], $mysql['port'], $mysql['database']);
    $rlink = new PDO($MysqlDSN, $mysql['user'], $mysql['password']);
    $rlink->beginTransaction();
    $rlink->exec("CREATE TABLE `SYSTEM_LOGIF`(
                    `ID` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                    `NAME` CHAR(10) NOT NULL,
                    `PASSWORD` TEXT NOT NULL,
                    `ADMIN` INTEGER NOT NULL,
                    `IP` TEXT NOT NULL);");
    $rlink->exec("CREATE TABLE `pictures`(
                    `ID` CHAR(36) PRIMARY KEY NOT NULL,
                    `LOCATION` TEXT NOT NULL,
                    `USER` TEXT NOT NULL,
                    `TIME` TIMESTAMP NOT NULL,
                    `IP` TEXT NOT NULL);");
    $rlink->exec("CREATE TABLE `SYSTEM_IVCODE`(
                    `ID` CHAR(8) PRIMARY KEY NOT NULL,
                    `CODE` TEXT NOT NULL,
                    `TIME` INTEGER NOT NULL,
                    `USER` CHAR(10) NOT NULL);");
    $rlink->exec("CREATE TABLE `SYSTEM_APIKEY`(
                    `USER` CHAR(10) PRIMARY KEY NOT NULL,
                    `KEY` TEXT NOT NULL);");
    $rlink->commit();
    $initsql = "INSERT INTO SYSTEM_LOGIF (NAME,PASSWORD,ADMIN,IP) VALUES (?,?,1,?);";//预准备
    $initdb = $rlink->prepare($initsql);
    require "./scripts/client_ip.php";
    $initdb->execute(array($info['user'],$spassword,clientIP()));//执行
    $dbconfigF = '<?php $xlink = new PDO("%s", "%s", "%s", array(PDO::ATTR_PERSISTENT => true));';
    $dbconfig = sprintf($dbconfigF, $MysqlDSN, $mysql['user'], $mysql['password']);
    if (!file_exists("./status/DatabaseCon.php") && !file_exists("./status"))
    {
        mkdir("./status", 0755);
        $dnbconfig = fopen("./status/DatabaseCon.php", "w");
        fwrite($dnbconfig, $dbconfig);
        fclose($dnbconfig);
        is_dir("./uploads") or mkdir("./upload", 0755);
        is_dir("./uploads_resize") or mkdir("./upload_resize", 0755);
    }
    else
    {
        echo "Cloud not create dbconfig, Plese Check it.";
    }
    $rlink = null;
}