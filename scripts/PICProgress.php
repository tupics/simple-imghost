<?php
class PhotoMod
{
    public static function Display($FileName)
    {
        $FileMIME = mime_content_type($FileName);
        header("Content-type: " . $FileMIME);
        echo readfile($FileName);
    }
    public static function CheckAndCompress($FileName, $OriFileName, $FakeFileName, $Qua, $SQLink)
    {
        $CheckSQL = $SQLink->prepare("SELECT count(`ID`) FROM `pictures` WHERE LOCATION = ?");
        $CheckSQL->execute(array($FakeFileName));
        $CheckResult = $CheckSQL->fetchColumn();
        if ($CheckResult == 1)
        {
            $FileMIME = mime_content_type($OriFileName);
            switch ($FileMIME) {
                case 'image/webp':
                    $cmd = "cwebp -q %d %s -o %s";
                    $command = sprintf($cmd, $Qua, $OriFileName, $FileName);
                    break;

                default:
                    $cmd = "cjpeg -outfile %s -quality %d %s";
                    $command = sprintf($cmd, $FileName, $Qua, $OriFileName);
                    break;
            }
            exec($command);
        }
        else
        {
            die;
        }
    }
}