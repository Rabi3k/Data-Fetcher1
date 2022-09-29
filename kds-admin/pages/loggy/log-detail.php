<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $line = $lines[$id];
    $val = json_decode($line);
    $val->lineNum = $id;
    $val->errorType = FriendlyErrorType($val->context->exception->level);
} else {
    $val = new stdClass();
}

function PrintoutArray($array)
{
    echo "<fieldset disabled><ul class='list-group'>";
    foreach ($array as $key => $val) {
        echo " <li class='list-group-item'>
        <div class='input-group mb-3'>
  <div class='input-group-prepend'>";


        if (gettype($val) == 'array') {
            echo "<i data-feather='arrow-down-right'></i><span class='input-group-text'  data-target='#$key' data-toggle='collapse' aria-expanded='false' aria-controls='$key'>$key</span>
            <ul id='$key' class='collapse'>";
            PrintoutArray($val);
            echo "</ul>";
        } else {
            echo "
            <span class='input-group-text'>$key</span>
          </div>
          <input type='text' id='$key' class='form-control' aria-describedby='$key' value='$val'>";
        }
        echo "</div></li>";
    }
    echo "</ul></fieldset>";
}

$arrayVal = json_decode(json_encode($val), true);
PrintoutArray($arrayVal);
