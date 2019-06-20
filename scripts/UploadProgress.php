<?php
abstract class ProgressFile
{
    protected static function CheckFileType($File)
    {
        $FileType = array();
        if (is_uploaded_file($File))
        {
            $imgmimetype = mime_content_type($File);
            switch ($imgmimetype)
            {
                case "image/jpeg":
                    $imgext = "jpeg";
                    break;
                case "image/png":
                    $imgext = "png";
                    break;
                case "image/webp":
                    $imgext = "webp";
                    break;
                case "image/gif":
                    $imgext = "gif";
                    break;
                case "image/bmp":
                    $imgext = "bmp";
                    break;
                default:
                    header("Status: 403");
                    die;
                    break;
            }
            $FileType['EXT'] = $imgext;
            $FileType['MIME'] = $imgmimetype;
            return $FileType;
        }
        else {
            header("Status: 403");
            die;
        }
    }
    protected static function SaveFileAndInfo($File, $Sql, $ImgExt, $PathPrefix, $CAddress, $User)
    {
        $Filestream = fopen($File, "r");
        $FileData = fread($Filestream,filesize($File));
        fclose($Filestream);
        $FileChecksum = hash("crc32", $FileData) . hash("md5", $FileData);
        $Locations = array();
        $DateR = date("Y-m-d");
        $DirName = "/uploads/" . $DateR . '/';
        is_dir($PathPrefix . $DirName) or mkdir($PathPrefix . $DirName);
        $Locations['Fake'] = $DirName . $FileChecksum . '.' . $ImgExt;
        $Locations['Real'] = $PathPrefix . $Locations['Fake'];
        if (file_exists($Locations['Real']))
        {
            return $Locations;
        }
        else {
            move_uploaded_file($File, $Locations['Real']);
            $Sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            try {
                $startrecond = $Sql->prepare('INSERT INTO `pictures` (`ID`,`LOCATION`,`USER`,`IP`) VALUES (UUID(), :imgname, :user, :clientip);');
                $startrecond->execute(array(':imgname' => $Locations['Fake'], ':user' => $User, ':clientip' => $CAddress));
                $startrecond->closeCursor();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            return $Locations;
        }
    }
}
class UploadFile extends ProgressFile
{
    public $FileType;
    public $Locations;
    public function Upload($FILEX, $Sql, $Prefix, $ClientIP, $User)
    {
        $this->FileType = $this::CheckFileType($FILEX['tmp_name']);
        $this->Locations = $this::SaveFileAndInfo($FILEX['tmp_name'], $Sql, $this->FileType['EXT'], $Prefix, $ClientIP, $User);
    }
}
