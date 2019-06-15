<?php
require_once "../status/DatabaseCon.php";
require "./verify.php";
require_once "../scripts/Users.php";
$UAC = new Users;
function APIGEN()
{
    $BaseKey = str_split(bin2hex(openssl_random_pseudo_bytes(32)), 8);
    $BigDic = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    $SmallDic = array("HE#LOW", "CL^FL", "N-A", "N_A", "P*&", "1/2", "@CCopymon");
    for ($i=1;$i<=8;$i++)
    {
        $LuckyNum = mt_rand(0,7);
        $BaseKey[$LuckyNum] = $BaseKey[$LuckyNum] . $BigDic[mt_rand(0,25)];
    }
    for ($i=1;$i<=8;$i++)
    {
        $LuckyNum = mt_rand(0,7);
        $BaseKey[$LuckyNum] = $BaseKey[$LuckyNum] . $SmallDic[mt_rand(0,6)];
    }
    $KEY = implode("",$BaseKey);
    return $KEY;
}
?>
<html>
    <head>
        <title>系统管理</title>
    </head>
    <body>
        <?php
        if (!isset($_GET['type'])){$type = 1;}else{$type = $_GET['type'];}
        if ($type == 2)
        {
            $fetchKeys = $xlink->prepare("SELECT count(`KEY`) FROM `SYSTEM_APIKEY` WHERE `USER` = ?;");
            $fetchKeys->execute(array($_SESSION['user']));
            $NumOfUserKey = $fetchKeys->fetchColumn();
            echo "<h2>API Key:</h2>";
            if ($NumOfUserKey > 0)
            {
                echo "你已存在API KEY，请询问数据库管理员获得";
            }
            elseif ($NumOfUserKey === 0)
            {
                $genkey = APIGEN();
                echo "你的KEY为:" . $genkey;
                $Insertsql = $xlink->prepare("INSERT INTO `SYSTEM_APIKEY` (`USER`,`KEY`) VALUES (?,?);");
                $Insertsql->execute(array($_SESSION['user'], $genkey));
                $Insertsql->closeCursor();
            }
        }
        else
        {
            echo "<h2>用户列:</h2>
                <code>从左到右依次为ID,用户名,管理员属性,注册IP</code>
                <form id='users' method='POST'>";
            $UserList = $UAC->LookupAllUser($_SESSION['Level'], $xlink);
            foreach ($UserList as $User) {
                $UserCheckbox = "<input name='users[]' type='checkbox' value='%d'/>";
                printf($UserCheckbox, $User['ID']);
                $UserInfo = "<p>ID: %d -- UN: %s -- ADMIN: %b -- RegIP: %s";
                printf($UserInfo, $User['ID'], $User['NAME'], $User['ADMIN'], $User['IP']);
            }
            echo '</form>';
        }
        ?>
        </body>
    </html>
