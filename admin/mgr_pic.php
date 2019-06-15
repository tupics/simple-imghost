<?php
require_once '../status/DatabaseCon.php';
require './verify.php';
$AdminLevel = $_SESSION['Level'];
function GetStart($pcode)
{
    return --$pcode * 10;
}
if (!empty($_GET['page']))
{
    $page = $_GET['page'];
    $Offset = GetStart($page);
}
else
{
    $page = 1;
    $Offset = 0;
}
if (!empty($_POST['picx']) && !empty($_POST['action']) && !empty($_POST['cuser']))
{
    $picx = $_POST['picx'];
    $action = $_POST['action'];
    $cuser = $_POST['cuser'];
    function actionRun($action)
    {
        global $xlink;
        global $picx;
        switch ($action)
        {
            case "delete":
                foreach ($picx as $picid)
                {
                    $lookupfilenamesql = 'SELECT `LOCATION` FROM `pictures` WHERE `ID` = ?;';
                    $lookupfilename = $xlink->prepare($lookupfilenamesql);
                    $lookupfilename->execute(array($picid));
                    $filename = $lookupfilename->fetchColumn();
                    unlink('..' . $filename);
                }
                //文件级
                $xlink->beginTransaction();
                foreach ($picx as $picid)
                {
                    echo $xlink->exec("DELETE FROM pictures WHERE ID = '" . $picid . "';") or die(print_r($xlink->errorInfo(), true));;
                }
                //数据库级
                $xlink->commit();
                break;
        }
    }
    if ($AdminLevel)
    {
        actionRun($action);
    }
    else
    {
        foreach ($picx as $picid)
        {
            $checksql = 'SELECT USER FROM pictures WHERE ID = ?';
            $precheck = $xlink->prepare($checksql);
            $precheck->execute(array($picid));
            $thispicuser = $precheck->fetchColumn();
            if ($thispicuser !== $_SESSION['user'] || $thispicuser !== $_POST['cuser'] || $_POST['cuser'] !== $_SESSION['user'])
            {
                die("NP, DA");
            }
        }
        actionRun($action);
    }
    echo "<a href='./mgr_pic.php'>Return</a>";
    exit;
}
?>
<html>
    <head>
    <title>ADMIN --SIHT</title>
    </head>
    <body>
        <form action="" id='pictures' method='POST'>
        <h2>PICTURES:</h2>
    <?php
    if ($AdminLevel)
    {
        $lookuppic = $xlink->prepare('SELECT `ID`,`LOCATION`,`USER`,`TIME`,`IP` FROM `pictures` ORDER BY `TIME` DESC LIMIT 10 OFFSET ?;');
        $lookuppic->bindParam(1, $Offset, PDO::PARAM_INT);
        $lookuppic->execute();
        $pics = $lookuppic->fetchAll();
        $lookuppic->closeCursor();
        foreach ($pics as $pic)
        {
            $PicCheckbox = "<input name='picx[]' type='checkbox' value='%s' />";
            printf($PicCheckbox, $pic['ID']);
            $PicInfo = "<p>%s -- %s -- %s -- %s -- %s</p>";
            printf($PicInfo, $pic['ID'], $pic['LOCATION'], $pic['USER'], $pic['TIME'], $pic['IP']);
        }
    }
    else
    {
        $lookuppic = $xlink->prepare("SELECT `ID`,`LOCATION`,`TIME` FROM `pictures` ORDER BY `TIME` DESC LIMIT 10 OFFSET ?;");
        $lookuppic->bindParam(1, $Offset, PDO::PARAM_INT);
        $lookuppic->execute();
        $pics = $lookuppic->fetchAll();
        $lookuppic->closeCursor();
        foreach ($pics as $pic)
        {
            $PicCheckbox = "<input name='picx[]' type='checkbox' value='%s' />";
            printf($PicCheckbox, $pic['ID']);
            $PicInfo = "<p>%s -- %s -- %s</p>";
            printf($PicInfo, $pic['ID'], $pic['LOCATION'], $pic['TIME']);
        }
    }
    ?>
        </br>
        <?php
        if ($page != 1)
        {
            echo "<a href='./mgr_pic.php?page=" . --$page . "'><strong><i>上一页</i></strong></a>";
        }
        echo "<a href='./mgr_pic.php?page=" . ++$page . "'><strong><i>下一页</i></strong></a></br>";
        $AvaCount = $xlink->prepare('SELECT count(*) from pictures;');
        $AvaCount->execute();
        $Ava = $AvaCount->fetchColumn();
        $AvaCount->closeCursor();
        function GetMathR($intvalue)
        {
            $a = $intvalue / 10;
            if (is_float($a))
            {
                $b = (int)(ceil($a));
            }
            elseif (is_int($a))
            {
                $b = $a;
            }
            else
            {
                die;
            }
            return $b;
        }
        $PageCanBeCreate = GetMathR($Ava);
        echo "<strong>共" . $PageCanBeCreate . "页</strong></br>";
        ?>
        跳转至<input type='text' name='page' form='pcode'/><input type='submit' value="跳转" form='pcode'/></br>
        <h2>Action:</h2>
        <input type='radio' name='action' value='delete' checked>Delete
        </br>
        <h2>CUSER:</h2>
        <input type='text' name='cuser' value='<?php echo $_SESSION['user']; ?>' readonly />
        </br>
        <input type='submit' value='Submit'/>
        </form>
        <form action="" method="GET" id='pcode'></form>
    </body>
</html>