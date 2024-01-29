<?php

use Pinq\Analysis\Functions\Func;
use Src\Classes\KMail;
use Src\Classes\LoginUser;
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
    $userGateway = new UserGateway($dbConnection);
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
            $userId = $userPostBody->userId;
            $retval = json_decode("{}");
            if (isset($userId) && $userId > 0) {
                $lUser = UserGateway::GetUserClass($userId, false);
            }
            switch ($q) {
                case 'edit-details':
                    $newUser = false;
                    if (!isset($userId) ||  $userId == 0) {
                        $lUser = LoginUser::NewUser();
                        $newUser = true;
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
                    } =>
                    {
            "id": 0,
            "email": "",
            "full_name": "",
            "user_name": "",
            "password": "",
            "secret_key": "",
            "screen_type": 1,
            "isSuperAdmin": 0,
            "IsAdmin": 0,
            "profile_id": 0,
            "Profile": null,
            "companies": [],
            "restaurants": [],
            "Restaurants_Id": []
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
                        $lUser->screen_type = intval($userPostBody->screenType);
                    }
                    if (isset($userPostBody->profileId) && !empty($userPostBody->profileId)) {
                        $lUser->profile_id = intval($userPostBody->profileId);
                    }

                    if ($newUser) {
                        $lUser->password = random_str(10);
                    }

                    $lUser = $userGateway->InsertOrUpdate($lUser);
                    if ($newUser) {
                        $userSecret = $userGateway->GetEncryptedKey($lUser->email);
                        KMail::sendResetPasswordMail($lUser, $userSecret);
                    }
                    $retval = $lUser->getJson();
                    break;
                default:
                    # code...
                    break;
            }
            //$retval = json_decode("{}");
            echo json_encode($retval);
            break;
        case 'PUT':
            $body = file_get_contents('php://input');
            //echo $body;

            $userPostBody = json_decode($body);
            $userId = $userPostBody->userId;
            $retval = json_decode("{}");
            if (isset($userId) && $userId > 0) {
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
                    if (!isset($lUser) ||  empty($userPostBody->password)) {
                        break;
                    }
                    $userGateway->UpdateUserPassword($lUser, $userPostBody->password);
                    $retval = json_decode("{'message':password changed}");
                    break;
                case 'send-rest-pasword':
                    /*
                    {
                        userId:
                    }
                    */
                    if (!isset($lUser)) {
                        break;
                    }
                    $userSecret = $userGateway->GetEncryptedKey($lUser->email);
                    KMail::sendResetPasswordMail($lUser, $userSecret);
                    $retval = json_decode("{'message':reset password sent}");
                    break;
                case 'set-user-relations':
                    /*
                        {
                            'userId':<int>,
                            'relations': <Array>
                                [{
                                    'companyId': <int>,
                                    'restaurantId': <Array (int) CAN BE NULL !>
                                }]
                        }
                    */
                    $userGateway->updateUserRelations($userPostBody);
                    $retval = json_decode("{'message':user relations changed}");
                    break;
                default:
                    # code...
                    break;
            }

            echo json_encode($retval);
            break;
        case 'DELETE':
        default:
            break;
    }
}
