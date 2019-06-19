<?php
require_once "./status/DatabaseCon.php";
require "./scripts/UploadProgress.php";
require "./scripts/client_ip.php";
$DirPrefix = ".";
require $DirPrefix . "/scripts/PICProgress.php";
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
        $SinString = hash("sha512", md5($UserKey . $timestamp . base64_encode(file_get_contents($_FILES['pictures']['tmp_name']))));
        if (hash_equals($SinString, $Sin))
        {
            $Rec = new UploadFile;
            $Rec->Upload($_FILE['picture'], $xlink, $DirPrefix, clientIP());
            $Imgsize = getimagesize($Rec->Locations['Real']);
            $Result = array("location" => $Rec->Locations['Fake'], "width" => $Imgsize[0], "height" => $Imgsize['1']);
            echo json_encode($Result);
            //生成一张75%的图片
            $CFileName = substr_replace($Rec->Locations['Real'], "uploads_resize", 2, 7);
            PhotoMod::CheckAndCompress($CFileName, $Rec->Locations['Real'], $Rec->Locations['Fake'], 75, $xlink);
        }
        else{
            header("Status: 403");
            die;
        }
        break;
    
    case 'GET':
        if (isset($_GET['q']))
        {
            $FileName = $_SERVER['PATH_INFO'];
            $FFileName = $DirPrefix . $_SERVER['PATH_INFO'];
            if (!file_exists($FFileName))
            {
                header("Status: 404");
                exit;
            }
            $Qua = isset($_GET['q']) ? (int) $_GET['q'] : null or $Qua = 75;
            //$CutName = explode("/", $FileName);
            //$NewFileNameF = "%s/upload_resize/%s/%s-%d";
            //$NewFileName = sprintf($NewFileNameF, $DirPrefix, $CutName[-2], $CutName[-1], $Qua);
            $NewFileName = substr_replace($FFileName, "uploads_resize", 2, 7) . $Qua;
            //$OriFileNameF = "%s/upload/%s/%s";
            //$OriFileName = sprintf($OriFileNameF, $DirPrefix, $CutName[-2], $CutName[-1]);
            if (file_exists($NewFileName))
            {
                PhotoMod::Display($NewFileName);
            }
            elseif ($Qua == 100)
            {
                PhotoMod::Display($FFileName);
            }
            else
            {
                PhotoMod::CheckAndCompress($NewFileName, $FFileName, $FileName, $Qua, $xlink);
                PhotoMod::Display($NewFileName);
            }
        }
        break;
    
    default:
        die;
        break;
}
