<?php

define('DS',DIRECTORY_SEPARATOR);

date_default_timezone_set('Asia/Karachi');

spl_autoload_register(function ($classname){
    require_once 'classes' . DS . $classname . '.php';
});

$config = config::getInstance();
$config->setVar("url", "https://www.readersfact.com/");
$config->setVar("users", $config->getFilePath("Config", "users.txt"));
// print_r($config->getFileData("Info","crawled.txt"));