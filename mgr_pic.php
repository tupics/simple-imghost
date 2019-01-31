<?php
require './sqlite_dnb.php';
require './verify.php';
$user = $_SESSION['user'];
$searchuseradmin = $xlink->prepare("SELECT ADMIN FROM SYSTEM_LOGIF WHERE USER = ?;");
$searchuseradmin->execute(array($user));
$adminlevel = $searchuseradmin->fetchColumn();
if (!empty($POST['picx']) && !empty($_POST['action']) && !empty($cuser))
{
    $picx = $_POST['picx'];
    $action = $_POST['action'];
    $cuser = $_POST['cuser'];
    function actionRun($inaction)
    {
        global $xlink;
        switch ($action)
        {
            case "delete":
                foreach ($pix as $picid)
                {
                    $lookupfilenamesql = 'SELECT LOCATION FROM picture WHERE ID = ?;';
                    $lookupfilename = $xlink->prepare($lookupfilenamesql);
                    $lookupfilename->execute(array($picid));
                    $filename = $lookupfilename->fetchColumn();
                    unlink('.' . $filename);
                }
                //文件级
                $xlink->beginTransaction();
                foreach ($picx as $picid)
                {
                    echo $xlink->exec('DELETE FROM pictures WHERE ID = ' . $picid . ';');
                }
                //数据库级
                $xlink->commit();
                break;
        }
    }
    if ($adminlevel)
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
    exit;
}
?>
<html>
    <head>
    <title>ADMIN --SIHT</title>
    </head>
    <body>
        <form action="" method='POST'>
        <h2>PICTURES:</h2>
    <?php
    if ($adminlevel)
    {
        $lookuppic = $xlink->prepare("SELECT ID,LOCATION,USER,TIME,IP FROM pictures;");
        $lookuppic->execute();
        $pics = $lookuppic->fetchAll();
        foreach ($pics as $pic)
        {
            echo "<input name='picx[]'" . " type='checkbox' value='" . $pic['ID'] . "'/>";
            echo "<p>" . $pic['ID'] . " -- " . $pic['LOCATION'] . " -- " . $pic['USER'] . " -- " . date("Y-m-d H:i:s", $pic['TIME']) . " -- " . $pic['IP'] . "</p>";
        }
    }
    else
    {
        $lookuppic = $xlink->prepare("SELECT ID,LOCATION,TIME FROM pictures;");
        $lookuppic->execute();
        $pics = $lookuppic->fetchAll();
        foreach ($pics as $pic)
        {
            echo "<input name='picx[]'" . " type='checkbox' value='" . $pic['ID'] . "'/>";
            echo "<p>" . $pic['ID'] . " -- " . $pic['LOCATION'] . " -- " . date("Y-m-d H:i:s", $pic['TIME']) . "</p>";
        }
    }
    ?>
        </br>
        <h2>Action:</h2>
        <input type='radio' name='action' value='delete' checked>Delete
        </br>
        <h2>CUSER:</h2>
        <input type='text' name='cuser' value='<?php echo $_SESSION['user']; ?>' readonly />
        </br>
        <input type='submit' value='Submit'/>
        </form>
    </body>
</html>