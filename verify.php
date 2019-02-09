<?php
require_once "./sqlite_dnb.php";
require "./scripts/PasswordWays.php";
if (isset($_COOKIE['login_recond']))
{
    $lookupsql = 'SELECT ID FROM SYSTEM_LOGREC WHERE RECKE = ?';
    $lookuprec = $xlink->prepare($lookupsql);
    $lookuprec->execute(array($_COOKIE['login_recond']));
    $lookuprecrefult = $lookuprec->fetchColumn();
    if (empty($lookuprecrefult))
    {
        setcookie("login_recond", "", -3600);
        echo "<script>location.replace(location.href);</script>";
        die;
    }
    session_start(array("cookie_lifetime"=>"8400", "cookie_domain"=>$_SERVER["HTTP_HOST"], "cookie_secure"=>true, "cookie_httponly"=>true));
}
elseif (isset($_POST['user']) && isset($_POST['password']))
{
    $user = $_POST['user'];
    $password = $_POST['password'];

    if ($PasswordWaysH->VerifyPassword($password, $user, $xlink))
    {
        $recond_cookie = hash("sha256", md5($PasswordWaysH->MakeHash($password) . time() . bin2hex(openssl_random_pseudo_bytes(20))));
        $searchuser = 'SELECT count(ID) FROM SYSTEM_LOGREC WHERE USER = ?';
        $SqlRun = $xlink->prepare($searchuser);
        $SqlRun->execute(array($user));
        $SqlData = $SqlRun->fetchColumn();
        if (!$SqlData)
        {
            $recondtosql = $xlink->prepare("INSERT INTO SYSTEM_LOGREC (ID,RECKE,USER,TIME) VALUES (:id,:recke,:user,:time);");
        }
        else
        {
            $recondtosql = $xlink->prepare("UPDATE SYSTEM_LOGREC set ID = :id, RECKE = :recke, TIME = :time WHERE USER = :user");
        }
        $recondtosql->execute(array(':id' => hash("crc32", $recond_cookie . time() . rand()), ':recke' => $recond_cookie, ':user' => $user, ':time' => time()));
        setcookie("login_recond", $recond_cookie, time()+7200, "/", $_SERVER["HTTP_HOST"], true, true);

        session_start(array("cookie_lifetime"=>"8400", "cookie_domain"=>$_SERVER["HTTP_HOST"], "cookie_secure"=>true, "cookie_httponly"=>true));
        $_SESSION['user'] = $user;

        echo "<script>location.replace('./index.php');</script>";
    }
    else
    {
        echo "Password Check Failed, Don't try again in 1 day";
        $xlink = null;
        die;
    }
}
else
{
    echo "<script>location.replace('./login.html');</script>";
    $xlink = null;
    die;
}