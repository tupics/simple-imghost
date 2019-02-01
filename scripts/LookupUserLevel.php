<?php
function LookupUserLevel($user)
{
    global $xlink;
    $SearchUserAdmin = $xlink->prepare("SELECT ADMIN FROM SYSTEM_LOGIF WHERE USER = ?;");
    $SearchUserAdmin->execute(array($user));
    $AdminLevel = $SearchUserAdmin->fetchColumn();
    return $AdminLevel;
}