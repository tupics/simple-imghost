<?php
require_once "./status/DatabaseCon.php";
require "./scripts/UploadProgress.php";
require "./scripts/client_ip.php";
$DirPrefix = ".";
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (!isset($_POST['user']) || !isset($_POST['sin']) || !isset($_FILES['picture']) || !isset($_POST['timestamp'])) {die;}
        $User = $_POST['user'];
        $Sin = $_POST['sin'];
        $timestamp = $_POST['timestamp'];
        $FetchNumOfUsersKey = $xlink->prepare("SELECT count(`KEY`) FROM `SYSTEM_APIKEY` WHERE USER = ?;");
        $FetchNumOfUsersKeyE = $FetchNumOfUsersKey->execute(array($User));
        $NumOfUsersKey = $FetchNumOfUsersKeyE->fetchComlumn();
        if ($NumOfUsersKey == 1) {
            $FetchUsersKey = $xlink->prepare("SELECT `KEY` FROM SYSTEM_APIKEY WHERE USER = ?;");
            $FetchUsersKeyE = $FetchUsersKey->execute(array($User));
            $UserKey = $FetchUsersKeyE->fetchComlumn();
        } else {
            die;
        }
        is_uploaded_file($_FILES['pictures']['tmp_name']) or die;
        $SinString = hash("sha512", md5($User . $timestamp . base64_encode(file_get_contents($_FILES['pictures']['tmp_name']))));
        if (hash_equals($SinString, $Sin))
        {
            $Rec = new UploadFile;
            $Rec->Upload($_FILE['picture'], $xlink, $DirPrefix, clientIP());
            $Imgsize = getimagesize($Rec->Locations['Real']);
            $Result = array("location" => $Rec->Locations['Fake'], "width" => $Imgsize[0], "height" => $Imgsize['1']);
            echo json_encode($Result);
        }
        else{
            header("Status: 403");
            die;
        }
        break;
    
    case 'GET':
        require $DirPrefix . "/scripts/PICProgress.php";
        if (isset($_GET['q']))
        {
            $FileName = $_SERVER['PATH_INFO'];
            if (!file_exists($FileName))
            {
                header("Status: 404");
                exit;
            }
            $Qua = isset($_GET['q']) ? (int) $_GET['q'] : null or $Qua = 75;
            $CutName = explode("/", $FileName);
            $NewFileNameF = "%s/upload_resize/%s/%s-%d";
            $NewFileName = sprintf($NewFileNameF, $DirPrefix, $CutName[-2], $CutName[-1], $Qua);
            $OriFileNameF = "%s/upload/%s/%s";
            $OriFileName = sprintf($OriFileNameF, $DirPrefix, $CutName[-2], $CutName[-1]);
            if (file_exists($NewFileName))
            {
                PhotoMod::Display($NewFileName);
            }
            else
            {
                PhotoMod::CheckAndCompress($NewFileName, $OriFileName, $Qua, $xlink);
                PhotoMod::Display($NewFileName);
            }
        }
        break;
    
    default:
        die;
        break;
}
