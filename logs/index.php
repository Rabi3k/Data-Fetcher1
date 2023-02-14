<?php
include_once "../bootstrap.php";

$lines = file($_SERVER["DOCUMENT_ROOT"] . '/logs/app.log');
foreach ($lines as $lneNum => $line) {
    $val = json_decode($line);
    $val->lineNum = isset($lneNum) ? $lneNum : 0;
    $val->errorType = isset($val->context->exception->level) ? FriendlyErrorType($val->context->exception->level) : "E_ERROR";
    if (isset($val->context->Server->SERVER_SIGNATURE)) {
        $val->context->Server->SERVER_SIGNATURE = htmlentities($val->context->Server->SERVER_SIGNATURE);
    }

    if (empty($val->message) && isset($val->context->exception->error)) {
        $val->message = $val->context->exception->error;
    } else if (empty($val->message) && isset($val->context->exception->message)) {
        $val->message = $val->context->exception->message;
    }

    $liArr[] = $val;
}
echo '{"draw": 1,
    "recordsTotal":' . count($liArr) . ',
    "recordsFiltered": ' . count($liArr) . ',
    "data":' . json_encode($liArr) . "}";
