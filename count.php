<?php
require_once 'classes' . DIRECTORY_SEPARATOR . 'root.php';
$f = new functions;
$path = $config->getPath("DataCrawled");
$files = scandir($path);

unset($files[0]);
unset($files[1]);
$final = [];
foreach($files as $file)
{
    $info = [];
    $data = file_get_contents($path . $file);
    $json = json_decode($data, true) ;
    $name = $json['name'];
    $info = $name . "\t||";

    unset($json['name']);
    $keys = array_keys($json);
    foreach($keys as $k)
    {
        $info .= $k . "\t||";
    }

    $final[] = $info;
}

$data = implode("\n", $final);
file_put_contents("count.txt", $data);


?>