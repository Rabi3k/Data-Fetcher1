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
    'Authorization: Bearer ' . GIT_ACCESS_TOKEN,
    'User-Agent:' . $_SERVER['HTTP_USER_AGENT'],
  ),
));

$response = json_decode(curl_exec($curl));
$lov = $response[0]->name; // Latest Online Version
curl_close($curl);
echo $_ENV["VERSION"] . " => " . VERSION . " => $lov <br/>";
$statments = GetStatmentsToExecute($UpdatesSqlStatments);
?>

<body>
  

    <div class="container-fluid">
      <div class="card-columns">




        <?php foreach ($statments as $kv => $value) { ?>
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Version <?php echo $kv ?></h5>
            </div>
            <div class="card-body">
              <p class="card-text"><?php echo $value ?></p>

            </div>
          </div>
        <?php }
        //setEnv("VERSION",VERSION);

        ?>
      </div>


    </div>
  </div>
</body>