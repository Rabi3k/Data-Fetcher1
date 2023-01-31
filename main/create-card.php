<?php
require_once("../bootstrap.php");
$screentype = $userLogin->GetUser()->GetScreenType();
include "../$templatePath/$screentype/create-card.php";