<?php
class PasswordWaysH
{
    public function MakeHash($Password)
    {
        return password_hash($Password, PASSWORD_DEFAULT);
    }
    public static function VerifyPassword($Password, $UserName, $dblink)
    {
        $LookupHashSql = 'SELECT `PASSWORD` FROM `SYSTEM_LOGIF` WHERE `NAME` = ?';
        $RunLookupHash = $dblink->prepare($LookupHashSql);
        $RunLookupHash->execute(array($UserName));
        $InBaseHash = $RunLookupHash->fetchColumn();
        $RunLookupHash->closeCursor();
        $TrueAndFalse = password_verify($Password, $InBaseHash);
        return $TrueAndFalse;
    }
}