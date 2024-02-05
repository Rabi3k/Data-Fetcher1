
<?php

use Pinq\Analysis\Functions\Func;
use Src\Classes\Company;
use Src\TableGateways\CompanyGateway;

$validator = true;
include "index.php";
if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
}
UsersProcessRequest();

function UsersProcessRequest()
{
    global $dbConnection, $requestMethod, $q;
    $companyGateway = new CompanyGateway($dbConnection);
    switch ($requestMethod) {
        case 'GET':

            $companyId = isset($_GET["cid"]) && $_GET["cid"] != null && $_GET["cid"] != "" ? $_GET["cid"] : null;
            if (isset($companyId)) {
                $company = $companyGateway->FindById($companyId);
                //var_dump($company);
            }
            if (isset($company)) {
                $liArr = array($company->getJson());
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

            $companyPostBody = json_decode($body);
            $retval = json_decode("{'message':'Nothing is here'}");
            // $company = $userPostBody->userId;
            // if (isset($userId) && $userId > 0) {
            //     $lUser = UserGateway::GetUserClass($userId, false);
            // }
            switch ($q) {
                case 'edit-company':
                    $lCompany = Company::NewCompany();
                    $lCompany->setFromJsonStr($body);
                    $lCompany = $companyGateway->InsertOrUpdate($lCompany);
                    $retval =  $lCompany->getJson();
                    break;
                default:
                    # code...
                    break;
            }
            echo json_encode($retval);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}
