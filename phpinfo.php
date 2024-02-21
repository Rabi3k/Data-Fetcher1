<?php 
require "bootstrap.php";

echo "<p>".T_("hello")."</p>" ;
echo T_("welcome");
$locale = 'en_DK';
echo "<p>"._("hello")."</p>" ;
echo _("welcome");
