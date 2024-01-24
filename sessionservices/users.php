<?php

use Pinq\Analysis\Functions\Func;
use Src\Classes\KMail;
use Src\Classes\User;
use Src\TableGateways\UserGateway;

$validator = true;
include "index.php";
if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
}
UsersProcessRequest();

function UsersProcessRequest()
{
    global $dbConnection, $requestMethod, $q;

    switch ($requestMethod) {
        case 'GET':

            $userId = isset($_GET["uid"]) && $_GET["uid"] != null && $_GET["uid"] != "" ? $_GET["uid"] : null;
            if (isset($userId)) {
                $lUser = UserGateway::GetUserClass($userId, false);
            }
            if (isset($lUser)) {
                $liArr = array($lUser->getJson());
            } else {
                $liArr = array();
            }

            $retval = (object)array(
                "draw" => 1,
                "recordsTotal" => count($liArr),
                "recordsFiltered" => count($liArr),
                "data" => ($liArr)
            );
            echo json_encode($retval);

            break;

        case 'POST':
            $body = file_get_contents('php://input');
            //echo $body;

            $userPostBody = json_decode($body);
            switch ($q) {
                case 'change-password':
                    break;
                default:
                    # code...
                    break;
            }
            $retval = json_decode("{}");
            echo json_encode($retval);
            break;
        case 'PUT':
            $body = file_get_contents('php://input');
            //echo $body;
            $userGateway = new UserGateway($dbConnection);
            $userPostBody = json_decode($body);
            $userId = $userPostBody->userId;
            if (isset($userId)) {
                $lUser = UserGateway::GetUserClass($userId, false);
            }
            switch ($q) {
                case 'change-password':
                    /*
                    {
                        userId:
                        password:
                    }
                    */
                    $userGateway->UpdateUserPassword($lUser, $userPostBody->password);
                    break;
                case 'send-rest-pasword':
                    /*
                    {
                        userId:
                    }
                    */
                    $userSecret = $userGateway->GetEncryptedKey($lUser->email);
                    KMail::sendResetPasswordMail($lUser, $userSecret);
                    echo "{'message':reset password sent}";
                    break;
                case 'edit-details':
                    if (!isset($userId)) {
                        $lUser = new User();
                    }
                    /*
                        {
                            userId:
                        full_name:
                        user_name:
                        email:
                        userType:
                        screenType:
                        profileId:
                        password:
                    }
                     */
                    if (isset($userPostBody->full_name) && !empty($userPostBody->full_name)) {
                        $lUser->full_name = $userPostBody->full_name;
                    }
                    if (isset($userPostBody->user_name) && !empty($userPostBody->user_name)) {
                        $lUser->user_name = $userPostBody->user_name;
                    }
                    if (isset($userPostBody->email) && !empty($userPostBody->email)) {
                        $lUser->email = $userPostBody->email;
                    }
                    // Activate user
                    if (isset($userPostBody->userType) && !empty($userPostBody->userType)) {
                        $lUser->SetUsertype(strval($userPostBody->userType));
                    }
                    if (isset($userPostBody->screenType) && !empty($userPostBody->screenType)) {
                        //$lUser->SetUsertype(strval($_POST['userType']));
                        switch ($userPostBody->screenType) {
                            case "OrderDisplay":
                                $lUser->screen_type = 1;
                                break;

                            case "ItemDisplay":
                                $lUser->screen_type = 2;
                                break;

                            case "CustomerDisplay":
                                $lUser->screen_type = 3;
                                break;

                            default:
                                $lUser->screen_type = 1;
                                break;
                        }
                    }
                    if (isset($userPostBody->profileId) && !empty($userPostBody->profileId)) {
                        $lUser->profile_id = intval($userPostBody->profileId);
                    }
                    $lUser = $userGateway->InsertOrUpdate($lUser);
                    break;
                default:
                    # code...
                    break;
            }
            $retval = json_decode("{}");
            echo json_encode($retval);
            break;
        case 'DELETE':
        default:
            break;
    }
}
