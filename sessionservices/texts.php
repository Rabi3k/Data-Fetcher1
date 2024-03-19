<?php


$validator = true;
$LoggedInUsers = true;
include "index.php";

if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
}
$byId = false;
if (isset($_GET['id']) && $_GET['id'] != null) {
    $id = intval($_GET['id']);
    $byId = true;
}
if (isset($_GET['k']) && $_GET['k'] != null) {
    $textKey = $_GET['k'];
    $byKey = true;
    
}
TextsProcessRequest();

function TextsProcessRequest()
{
    global $textsStore, $requestMethod, $q, $byId, $id,$byKey,$textKey;
    switch ($requestMethod) {
        case 'GET':
            $textQueryBuilder = $textsStore->createQueryBuilder();
            if ($byId == true) {
                $allTexts=  $textQueryBuilder->where( [ "_id", "=", $id ] )
                ->disableCache()
                ->getQuery()
                ->fetch();
                //$allTexts = array($textsStore->findById($id));
            }
            else if ($byKey == true) {
                $allTexts=  $textQueryBuilder->where( [ "text_key", "=", $textKey ] )
                ->getQuery()
                ->fetch();
                //$allTexts = array($textsStore->findById($id));
            }
             else {
                // creating the QueryBuilder
                
                $allTexts = $textQueryBuilder->orderBy(["_id" => "asc"])->disableCache()->getQuery()->fetch();
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
            $textQueryBuilder = $textsStore->createQueryBuilder();
            $text = $textsStore->findById($textPostBody->id);
            $textCache = $textQueryBuilder->where( [ "_id", "=", $textPostBody->id ] )->getQuery()->getCache();
            $textCache->delete();
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
                case 'delete-text':
                    if ($text == null) {
                        $retval = json_decode("{}");
                        break;
                    }

                    $deleted = $textsStore->deleteById($textPostBody->id);
                    $retval = (object)array("Deleted" => $deleted);
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
