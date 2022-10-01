<?php

$total = $argv[2];
$current = $argv[1] - 1;
require_once 'classes' . DIRECTORY_SEPARATOR . 'root.php';

$crawlers = 
[
    "faqans"
];
// $enw = $crawlers[0];
// $twf = $crawlers[1];
// $sg = $crawlers[2];

$func = new functions;

// the wiki feed
foreach($crawlers as $crawler)
{
    $crawl = new $crawler();
    $links = $func->getLinks($crawler);
    
    foreach($links as $key => $link)
    {
        if($key % $total != $current)
        {   
            continue;
        }
        $crawledLinks = file_get_contents($config->getFilePath("Info","crawled.txt"));
        $crawledLinks = explode("\n", $crawledLinks);
        // print_r($crawledLinks);exit;
        if(in_array($link, $crawledLinks))
        {
            continue;
        }

        $data = $crawl->getData($link);


        if($data == false)
        {
            continue;
        }
        $title = $data['title'];
        $content = $data['content'];
        
        if($title == null || $content == null)
        {
            continue;
        }

        $fileName = $func->makeFileName($title);

        if(file_exists($config->getFilePath("DataCrawled",$fileName)))
        {
            $fileData = file_get_contents($config->getFilePath("DataCrawled",$fileName));
            // echo $fileData;
            $Cdata = json_decode($fileData, true);

            if(isset($Cdata[$crawler]))
            {
                continue;
            }

            $Cdata[$crawler] = 
            [
                'title' => $title,
                'content' => $content,
            ];

        }
        else
        {     
            $Cdata = [];
            $Cdata['title'] = $title;
            
            
            
        }    
        $Cdata[$crawler] = 
        [
            'title' => $title,
            'content' => $content,
            'Crawl Time' => date('g:i a - d/M/o '),
        ];
        $json = json_encode($Cdata);
        file_put_contents($config->getFilePath("DataCrawled",$fileName), $json);
        file_put_contents($config->getFilePath("Info","crawled.txt"), $link . "\n", FILE_APPEND);
        echo "crawled\t" . $link . "\n";exit;
    }    
}
