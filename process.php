<?php


if($argv == null || count($argv) < 3)
{
    echo "Provide valid arguments";exit;
}
else 
{
    $total = $argv[2];
    $current = $argv[1] - 1;
}

require_once 'classes' . DIRECTORY_SEPARATOR . 'root.php';

$files = scandir($config->getPath("DataCrawled"));

unset($files[0]);
unset($files[1]);

$files = array_values($files);

$func = new functions;
$pr = new plagiarismRemover;
$y = new youtube;
$tm = new tableMaker;
// $am = new aboutMaker;

// the wiki feed

foreach($files as $key => $file)
{
    if($key % $total != $current)
    {   
        continue;
    }
    $processed = file_get_contents($config->getFilePath("Info","processed.txt"));
    $processedLinks = explode("\n", $processed);
    // print_r($crawledLinks);exit;
    if (in_array($file, $processedLinks))
    {
        continue;
    }

    $data = file_get_contents($config->getFilePath("DataCrawled", $file));
    $data = json_decode($data, true);

    if($data == false)
    {
        continue;
    }

    $title = $data['title'];
    unset($data['title']);

    $finalData = [];
    $finalData['title'] = $title;
    
    // if($title == null || $title == null || $content == null)
    // {
    //     continue;
    // }

    foreach ($data as $key => $value) 
    {
        $finalData[$key]['title'] = $data[$key]['title'];
        

        $finalData[$key]['content'] = $data[$key]['content'];
        $finalData[$key]['rewritedContent'] = $pr->rewrite_qa($data[$key]['content']);
        $finalData[$key]['Crawl Time'] = $data[$key]['Crawl Time'];
        $finalData[$key]['youtube'] = $y->search($title);
        $finalData[$key]['Processtime Time'] = date('g:i a - d/M/o ');
    }

    // $finalData['about'] = $am->makeAbout($finalData);
    // exit;
    // print_r($finalData);
    // exit;
    $json = json_encode($finalData);
    // print_r($json);
    if(file_put_contents($config->getFilePath("DataProcessed", $file), $json) !== false)
    {
        unlink($config->getFilePath("DataCrawled", $file));
        file_put_contents($config->getFilePath("Info","processed.txt"), $file . "\n", FILE_APPEND);
        echo "processed \t $title \t" . $file . "\n";
    }
    else 
    {
        echo "\nerror saving file\n";

    }
    exit;

}   