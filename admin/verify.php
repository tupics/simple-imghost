<?php
require_once "../status/DatabaseCon.php";
require "../scripts/PasswordWays.php";
require_once "../scripts/Users.php";
session_start(array("cookie_lifetime" => "8400", "cookie_domain" => $_SERVER["HTTP_HOST"], "cookie_secure" => true, "cookie_httponly" => true));
if (!isset($_SESSION['login']))
{
    $_SESSION['login'] = false;
}
if (!$_SESSION['login']) {
    if (isset($_POST['user']) && isset($_POST['password'])) {
        $user = $_POST['user'];
        $password = $_POST['password'];
        if (PasswordWaysH::VerifyPassword($password, $user, $xlink)) {
            $_SESSION['user'] = $user;
            $_SESSION['login'] = true;
            $UAC = new Users;
            $_SESSION['Level'] = $UAC->LookupUserLevel($user);
            echo "<script>location.replace('./index.php');</script>";
        } else {
            echo "Password Check Failed, Don't try again in 1 day";
            die;
        }
    } else {
        echo "<script>location.replace('./login.html');</script>";
        die;
    }
}
