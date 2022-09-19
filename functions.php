<?php

function setEnv($key, $value)
{
	file_put_contents($_SERVER["DOCUMENT_ROOT"]."/.env", str_replace(
		$key . '=' . getenv($key),
		$key . '=' . $value,
		file_get_contents($_SERVER["DOCUMENT_ROOT"]."/.env")
	));
    putenv("$key=$value");
}

function GetStatmentsToExecute(array $statments)
{
    $version = GetNearestVersion(array_keys($statments));

    $idx = intval($version['index']);
    $output = array_slice($statments, $idx +1);
    return  $output;
}


function GetNearestVersion(array $versionNumbers)
{
    $closest = null;
    $idx = 0;
    $index = 0;
    foreach ($versionNumbers as $item) {
        if ($closest === null || version_compare($item, getenv('VERSION')) < 1) {
            $index = $idx;
            $closest = $item;
        }
        $idx++;
    }
    return array('vn' => $closest, 'index' => $index);
}