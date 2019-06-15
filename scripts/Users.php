<?php
class Users
{
    public function LookupUserLevel($user)
    {
        global $xlink;
        $SearchUserAdmin = $xlink->prepare("SELECT `ADMIN` FROM `SYSTEM_LOGIF` WHERE `NAME` = ?;");
        $SearchUserAdmin->execute(array($user));
        $AdminLevel = $SearchUserAdmin->fetchColumn();
        $SearchUserAdmin->closeCursor();
        return (int)$AdminLevel;
    }
    public function LookupAllUser($UserLevel, $SqlLink)
    {
        if ($UserLevel === 1) {
            $LookupInfoSql = $SqlLink->prepare("SELECT `ID`,`NAME`,`ADMIN`,`IP` FROM `SYSTEM_LOGIF`;");
            $LookupInfoSql->execute();
            $UserList = $LookupInfoSql->fetchAll(PDO::FETCH_ASSOC);
            $LookupInfoSql->closeCursor();
            return $UserList;
        } else {
            echo "你没有权限";
        }
    }
}
