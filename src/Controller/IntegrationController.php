<?php

namespace Src\Controller;

class IntegrationController
{
    private $db;
    private $requestMethod;
    private $orderId;
    private array $secrets;


    public function __construct($db, $requestMethod, int $orderId = null, array $secrets = array())
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->orderId = $orderId ?? null;
        $this->secrets = $secrets;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                break;
            case 'POST':
                break;
            case 'PUT':
            case 'DELETE':
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    static private function PostCategory(string $catName)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.loyverse.com/v1.0/categories',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    
    "name": "' . $catName . '",
    "color": "RED"
    
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer 685a16950383408ca5dd9f883f291d5e'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
    static public function PostCategories(array $categories)
    {
        foreach ($categories as $key => $value) {
            # code...
            IntegrationController::PostCategory($value);
        }
        return GeneralController::CreateResponser(implode(", ", $categories));
    }
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
