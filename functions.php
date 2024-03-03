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

    $retval = array();
    foreach ($statments as $Key => $Value) {
        // echo "{$Key} : ".getenv('VERSION')." => ".version_compare($Key, getenv('VERSION'))."<br/>";
        if (version_compare($Key, getenv('VERSION')) > 0) {
            //echo "=>1";
            $retval[$Key] =  $Value;
        }
    }
    return $retval;

    // $version = GetNearestVersion(array_keys($statments));

    // $idx = intval($version['index']);
    // $output = array_slice($statments, $idx + 1);
    // return  $output;
}
function random_str(
    $length,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function GetNearestVersion(array $versionNumbers)
{
    $closest = null;
    $idx = 0;
    $index = -1;
    foreach ($versionNumbers as $item) {
        if (version_compare($item, getenv('VERSION')) < 1) {
            echo "=>1";
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
        return array("upload" => 0, "message" => "File is not an image!");
    }
    //}


    // Check file size
    if ($fileToUpload["size"] > 500000) { //$_FILES["fileToUpload"]
        return array("upload" => 0, "message" => "Sorry, your file is too large!");
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "svg"
    ) {
        //echo "Sorry, only JPG, JPEG, PNG & SVG files are allowed.";
        return array("upload" => 0, "message" => "Sorry, only JPG, JPEG, PNG & SVG files are allowed!");
    }

    // Check if file already exists
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $target_file)) {
        chmod($_SERVER['DOCUMENT_ROOT'] . $target_file, 0755); //Change the file permissions if allowed
        rename($_SERVER['DOCUMENT_ROOT'] . $target_file, $_SERVER['DOCUMENT_ROOT'] . "$target_file.old");
        //unlink($target_file); //remove the file
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return array("upload" => 0, "message" => "Sorry, your file was not uploaded!");
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($fileToUpload["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $target_file)) {
            // echo "The file " . htmlspecialchars(basename($fileToUpload["name"])) . " has been uploaded.";
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "$target_file.old")) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "$target_file.old"); //remove the file
            }
            return array("upload" => 1, "target_file" => $target_file, "message" => "");
        } else {
            return array("upload" => 0, "message" => "Sorry, there was an error uploading your file!");
        }
    }
}

/**
 * Get image path from the server
 * 
 * @param UploadType    $type               (Restaurant,User,System) to know in wich folder we insert the file
 * @param string        $fileName           the file name in which the file is saved with
 * 
 */
function GetImagePath(UploadType $type, string|null $fileName)
{
    //TODO: make all echo to log
    if (!isset($fileName) || empty($fileName)) {
        return "/media/restaurant/no-image.png";
    }
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
    $imageFileTypes = array("jpg", "jpeg", "png", "svg");
    foreach ($imageFileTypes as $imageFileType) {
        # code...
        $target_file = "$target_dir$fileName.$imageFileType"; //basename($_FILES["fileToUpload"]["name"]);
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $target_file)) {
            return $target_file;
        }
    }
    return "/media/restaurant/no-image.png";
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
/**
 * Create GUID
 */
function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
/**
 * GetHostUrl
 *
 * @return void
 */
function GetHostUrl()
{
    if (isset($_SERVER['HTTPS'])) {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    } else {
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'];
}

/**
 * Undocumented function
 *
 * @param string $str
 * @return void
 */
function str_Encrypt(string $str): string
{
    // Store a string into the variable which
    // need to be Encrypted
    $simple_string = $str;

    // Store the cipher method
    $ciphering = "AES-128-CTR";

    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;

    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '6684984797599552';

    // Store the encryption key
    $encryption_key = "BUnnfAPYYYUAJJHe";

    // Use openssl_encrypt() function to encrypt the data
    $encryption = openssl_encrypt(
        $simple_string,
        $ciphering,
        $encryption_key,
        $options,
        $encryption_iv
    );
    return $encryption;
}

function str_Decrypt(string $encryption): string
{
    // Store the cipher method
    $ciphering = "AES-128-CTR";

    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    // Non-NULL Initialization Vector for decryption
    $decryption_iv = '6684984797599552';

    // Store the decryption key
    $decryption_key = "BUnnfAPYYYUAJJHe";

    // Use openssl_decrypt() function to decrypt the data
    $decryption = openssl_decrypt(
        $encryption,
        $ciphering,
        $decryption_key,
        $options,
        $decryption_iv
    );

    return $decryption;
}

//if (isset($_GET["locale"])) $locale = $_GET["locale"];

// read directly .po files

function _e($contenido, $defaultText, $local = null)
{
    echo __($contenido, $defaultText, $local);
}
function __($contenido, $defaultText, $local = null)
{
    global $defaultLocale, $textsStore;
    $language = isset($local) && $local != null ? $local : $defaultLocale;
    // Find documents.
    $result = $textsStore
        ->findBy(
            [
                ["text_key", "=", "$contenido"]
            ],
            ["_id" => "desc"]
        );
        if (!isset($result) || count($result) < 1) {
            $text = [
                'text_key' => "$contenido",
                'text_lang' => "default",
                'text' => "$defaultText",
                'languages' => array(
                    "$language" => (object)array("text" => "", "updated" => new DateTime())
                    )
                ];
                // Insert the data.
                $textObj = (object)$textsStore->updateOrInsert($text);
                //var_dump($textObj);
            } else {
                $textObj = (object) $result[0];
            }
            $textStr = $textObj->text;
            if (isset($textObj->languages) && ($textObj->languages[$language])) {
                $langTO = (object)($textObj->languages[$language]);
                $textStr = !empty($langTO->text)? $langTO->text:$textObj->text;
            }

    return $textStr;
}
