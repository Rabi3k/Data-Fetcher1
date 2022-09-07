<?php 
require "bootstrap.php";

session_unset();

session_destroy();

header("Location: $rootpath/");