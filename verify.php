<?php
require "./sqlite_dnb.php";
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
    $password = hash("sha256", md5($_POST['password']));
    $lookupinfosql = 'SELECT PASSWORD FROM SYSTEM_LOGIF WHERE NAME = ?';
    $lookupinfo = $xlink->prepare($lookupinfosql);
    $lookupinfo->execute(array($user));
    $reinsqlpass = $lookupinfo->fetchColumn();
    if ($password == $reinsqlpass)
    {
        $recond_cookie = hash("sha256", md5($password . time()+rand() . rand()));
        $recondtosql = $xlink->prepare("INSERT INTO SYSTEM_LOGREC (ID,RECKE,USER,TIME) VALUES (?,?,?,?);");
        $recondtosql->execute(array(hash("crc32", $recond_cookie . time() . rand()), $recond_cookie, $user, time()));
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