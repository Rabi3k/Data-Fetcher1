<?php

use Src\Enums\UploadType;

use function PHPSTORM_META\type;

function setEnv($key, $value)
{
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/.env", str_replace(
        $key . '=' . getenv($key),
        $key . '=' . $value,
        file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.env")
    ));
    putenv("$key=$value");
}

function GetStatmentsToExecute(array $statments)
{
    $version = GetNearestVersion(array_keys($statments));

    $idx = intval($version['index']);
    $output = array_slice($statments, $idx + 1);
    return  $output;
}


function GetNearestVersion(array $versionNumbers)
{
    $closest = null;
    $idx = 0;
    $index = 0;
    foreach ($versionNumbers as $item) {
        if ($closest === null || version_compare($item, getenv('VERSION')) < 1) {
            $index = $idx;
            $closest = $item;
        }
        $idx++;
    }
    return array('vn' => $closest, 'index' => $index);
}
/**
 * upload image to the server
 * 
 * @param UploadType    $type               (Restaurant,User,System) to know in wich folder we insert the file
 * @param string        $fileName           the file name in which the file will be save to
 * @param mixed         $fileToUpload       the File array that hold te info about the uploaded file $_FILE['fileToUpload']
 * 
 */
function UplaodImage(UploadType $type, string $fileName, $fileToUpload)
{
    //TODO: make all echo to log

    $target_dir = "/media";
    switch ($type) {
        case UploadType::Restaurant:
            $target_dir = "$target_dir/restaurant/";
            break;
        case UploadType::User:
            $target_dir = "$target_dir/user/";
            break;
        case UploadType::System:
            $target_dir = "$target_dir/System/";
            break;
    }
    $imageFileType = strtolower(pathinfo($fileToUpload["name"], PATHINFO_EXTENSION));

    $target_file = "$target_dir$fileName.$imageFileType"; //basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;

    // Check if image file is a actual image or fake image
    //if (isset($_POST["submit"])) {
    $check = $fileToUpload["tmp_name"]; //getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        // echo "File is an image - " . $fileToUpload["type"] . ".";
        $uploadOk = 1;
    } else {
        //echo "File is not an image.";
        return "";
    }
    //}


    // Check file size
    if ($fileToUpload["size"] > 500000) { //$_FILES["fileToUpload"]
        //echo "Sorry, your file is too large.";
        return "";
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "svg"
    ) {
        //echo "Sorry, only JPG, JPEG, PNG & SVG files are allowed.";
        return "";
    }

    // Check if file already exists
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $target_file)) {
        chmod($_SERVER['DOCUMENT_ROOT'] . $target_file, 0755); //Change the file permissions if allowed
        rename($_SERVER['DOCUMENT_ROOT'] . $target_file, $_SERVER['DOCUMENT_ROOT'] . "$target_file.old");
        //unlink($target_file); //remove the file
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($fileToUpload["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $target_file)) {
            // echo "The file " . htmlspecialchars(basename($fileToUpload["name"])) . " has been uploaded.";
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "$target_file.old")) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "$target_file.old"); //remove the file
            }
            return $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    return "";
}

/**
 * 
 */
function FriendlyErrorType($type)
{
    switch ($type) {
        case E_ERROR: // 1 //
            return 'E_ERROR';
        case E_WARNING: // 2 //
            return 'E_WARNING';
        case E_PARSE: // 4 //
            return 'E_PARSE';
        case E_NOTICE: // 8 //
            return 'E_NOTICE';
        case E_CORE_ERROR: // 16 //
            return 'E_CORE_ERROR';
        case E_CORE_WARNING: // 32 //
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR: // 64 //
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING: // 128 //
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR: // 256 //
            return 'E_USER_ERROR';
        case E_USER_WARNING: // 512 //
            return 'E_USER_WARNING';
        case E_USER_NOTICE: // 1024 //
            return 'E_USER_NOTICE';
        case E_STRICT: // 2048 //
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR: // 4096 //
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED: // 8192 //
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED: // 16384 //
            return 'E_USER_DEPRECATED';
    }
    return "";
}
