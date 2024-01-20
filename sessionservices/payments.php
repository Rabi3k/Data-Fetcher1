<?php

use Pinq\Analysis\Functions\Func;
use Src\Classes\PaymentRelation;
use Src\Classes\Payments;
use Src\Controller\GeneralController;
use Src\Controller\IntegrationController;
use Src\TableGateways\IntegrationGateway;
use Src\TableGateways\PaymentRelationGateway;

$validator = true;
include "index.php";
if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
    switch ($q) {
        default:
            # code...
            break;
    }
} else {
    PaymentRelationsProcessRequest();
}

function PaymentRelationsProcessRequest()
{
    global $dbConnection, $requestMethod;

    switch ($requestMethod) {
        case 'GET':

            $integration_Id = isset($_GET["iid"]) && $_GET["iid"] != null && $_GET["iid"] != "" ? $_GET["iid"] : null;
            $liArr = array();
            if ($integration_Id != null) {
                $liArr = GetPaymentRelations(intval($integration_Id));
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
            /*
            {
                'integrationId': parseInt('<?php echo $integration->Id ?>'),
                'gfPayment': gfPayment,
                'loyverseId': loyverseId
            }
             */
            $paymentBody = json_decode($body);
            //echo json_encode($paymentBody);
            $retval = savePaymentRelations($paymentBody);
            // $integrationGateway = new IntegrationGateway($dbConnection);
            // $integration = $integrationGateway->findById($discountBody->integrationId);
            // if (!isset($discountBody->discountId) || $discountBody->discountId == null || $discountBody->discountId == "0") {
            //     //echo json_encode("{'message': 'Create New'}");
            //     //var_dump($integration);
            //     $liArr = IntegrationController::PostDiscount($integration, $discountBody->gfMenuId);
            // } else {
            //     $liArr = $integrationGateway->InsertOrUpdatePostedType($discountBody->discountName, "10", "discount", $discountBody->integrationId, $discountBody->gfMenuId, $discountBody->discountId, null, null);
            // }
            // echo json_encode($liArr);
            // $retval = (object)array(
            //     "draw" => 1,
            //     "recordsTotal" => count($liArr),
            //     "recordsFiltered" => count($liArr),
            //     "data" => ($liArr)
            // );
            echo json_encode($retval);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}
function savePaymentRelations($paymentBody)
{
    global $dbConnection;
    $paymentRelationGateway = new PaymentRelationGateway($dbConnection);
    $allPayments = $paymentRelationGateway->findByIntegrationId($paymentBody->integrationId);

    $paymentRelation = PaymentRelation::GetPaymentRelation(array(
        "integration_id" => $paymentBody->integrationId,
        "gf_payment" => $paymentBody->gfPayment,
        "loyveres_id" => $paymentBody->loyverseId,
        "last_updated" => (new DateTime('now'))->format('Y-m-d H:i:s')
    ));
    return $paymentRelationGateway->InsertOrupdate($paymentRelation);
}
function GetPaymentRelations(int $integration_id)
{
    global $dbConnection;
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($integration_id);
    $paymentRelationGateway = new PaymentRelationGateway($dbConnection);
    $allPayments = $paymentRelationGateway->findByIntegrationId($integration_id);
    $apayments = array_filter($allPayments, function ($p) {
        return strtolower($p->GfPayment) != "default";
    });
    $payments = array_values($apayments);
    $paymentMethods = array(
        "CASH",
        "CARD",
        "CARD_PHONE",
        "MobilePay"
    );
    $paymentsAv = array_column($apayments, "GfPayment");
    $missingP = array_diff($paymentMethods, $paymentsAv);
    foreach ($missingP as $key => $value) {
        # code...
        //# integration_id, gf_payment, loyveres_id, last_updated
        //'1', 'CASH', '1234', '2024-01-17 21:41:57'

        $payments[] = PaymentRelation::GetPaymentRelation(array("integration_id" => $integration_id, "gf_payment" => $value, "loyveres_id" => "0", "last_updated" => (new DateTime('now'))->format('Y-m-d H:i:s')));
    }



    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/payment_types',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));


    $lpaymentsTxt = array("NONINTEGRATEDCARD", "CASH", "OTHER");
    //    echo  json_encode($lpaymentsTxt);

    $response = json_decode(curl_exec($curl));
    $lpayments = array_filter($response->payment_types, function ($p) use ($lpaymentsTxt) {
        return in_array($p->type, $lpaymentsTxt);
    });
    curl_close($curl);

    foreach ($payments as $key => $value) {
        # code...
        $value->lpayments = $lpayments;
    }

    return $payments;
}
