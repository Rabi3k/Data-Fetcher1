<?php


$validator = true;
include "index.php";

if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
}
$byId = false;
if (isset($_GET['id']) && $_GET['id'] != null) {
    $id = intval($_GET['id']);
    $byId = true;
}
TextsProcessRequest();

function TextsProcessRequest()
{
    global $textsStore, $requestMethod, $q, $byId, $id;
    switch ($requestMethod) {
        case 'GET':
            if ($byId == true) {
                $allTexts = array($textsStore->findById($id));
            } else {
                $allTexts = $textsStore->findAll(["_id" => "asc"]);
            }

            $retval = (object)array(
                "draw" => 1,
                "recordsTotal" => count($allTexts),
                "recordsFiltered" => count($allTexts),
                "data" => ($allTexts)
            );
            echo json_encode($retval);

            break;

        case 'POST':
            $body = file_get_contents('php://input');
            $textPostBody = json_decode($body);
            /*
             {
                id:0,
                text:"",
                language:"da_DK",
                language_text:""
             }
             */
            //echo $body;

            $text = $textsStore->findById($textPostBody->id);
            switch ($q) {
                case 'edit-text':
                    if ($text == null) {
                        $retval = json_decode("{}");
                        break;
                    }
                    $mustUpdate = false;
                    if ($text["text"] != $textPostBody->text) {
                        $text["text"] = $textPostBody->text;
                        $mustUpdate = true;
                    }
                    if (isset($text["languages"])) {
                        $textStr = $text["languages"][$textPostBody->language]["text"];
                        if ($textStr != $textPostBody->language_text) {
                            $text["languages"][$textPostBody->language]["text"] = $textPostBody->language_text;
                            $text["languages"][$textPostBody->language]["updated"] = new DateTime();
                            $mustUpdate = true;
                        }
                    }
                    if ($mustUpdate) {
                        $textsStore->update($text);
                    }
                    $retval = $text;

                    break;
                default:
                    $retval = json_decode("{}");
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
