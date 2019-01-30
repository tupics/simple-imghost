<?php
function flush_cookie($xlink)
{
$getsql = 'SELECT TIME FROM SYSTEM_LOGREC ORDER BY TIME;';
$execsql = $xlink->prepare($getsql);
$execsql->execute();
$savetime = $execsql->fetchAll(PDO::FETCH_COLUMN,0);
$time = time();
$xlink->beginTransaction();
foreach ($savetime as $times)
{
    if ($times+7200<=$time)
    {
        $removesql = 'DELETE FROM SYSTEM_LOGREC WHERE TIME = ' . $times;
        $row = $xlink->exec($removesql);
        echo $row;
    }
}
$xlink->commit();
$xlink=null;
}