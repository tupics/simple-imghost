<?php
function LookupUserLevel($user)
{
    global $xlink;
    $SearchUserAdmin = $xlink->prepare("SELECT ADMIN FROM SYSTEM_LOGIF WHERE NAME = ?;");
    $SearchUserAdmin->execute(array($user));
    $AdminLevel = $SearchUserAdmin->fetchColumn();
    return $AdminLevel;
}