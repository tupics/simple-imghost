<?php
if (!empty($_COOKIE['login_recond']))
{
    setcookie("login_recond", "", -3600);
    echo "<script>location.replace('./index.php')</script>";
}