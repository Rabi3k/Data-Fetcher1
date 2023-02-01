<?php
require_once("../bootstrap.php");
if (!$userLogin->checkLogin()) {
    exit();
  }
//echo json_encode($userLogin->GetUser());
$screentype = $userLogin->GetUser()->GetScreenType();

include "../$templatePath/$screentype/create-card.php";