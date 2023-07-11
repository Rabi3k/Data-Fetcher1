<?php
require_once("../bootstrap.php");
if (!$userGateway->checkLogin()) {
    exit();
  }
$screentype = $userGateway->GetUser()->GetScreenType();

include "../$templatePath/$screentype/create-card.php";