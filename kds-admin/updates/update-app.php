<?php
require_once "../bootstrap.php";
//echo version_compare(VERSION, getenv('VERSION')) . "<br/>";



$curl = curl_init();
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.github.com/repos/Rabi3k/Data-Fetcher1/tags',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer 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',
    'User-Agent:'.$_SERVER['HTTP_USER_AGENT'],
  ),
));

$response = json_decode(curl_exec($curl));
$lov = $response[0]->name; // Latest Online Version
curl_close($curl);
echo $_ENV["VERSION"]." => ".VERSION." => $lov <br/>";
$statments= GetStatmentsToExecute($UpdatesSqlStatments);
foreach($statments as $kv=>$value)
{
  echo "$kv => $value <br/>";
}

 //setEnv("VERSION",VERSION);
 
