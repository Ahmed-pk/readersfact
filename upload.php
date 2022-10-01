<?php 

require_once 'classes' . DIRECTORY_SEPARATOR . 'root.php';

$wp_uploader = new WordpressUpload;
$postMaker = new postMaker;

$processedPath = $config->getPath('DataProcessed');
$uploadedPath = $config->getPath('DataUploaded');
$failedPath = $config->getPath('Datafailed');

$data = scandir($processedPath);
unset($data[1]);
unset($data[0]);

foreach ($data as $file) 
{
    $postData = file_get_contents($processedPath . $file);
    $json = json_decode($postData);
    
    $readyPost = [];
    $categories = [];
    $tags = [];
    $title = $json->title;
    $readyPost[] = '<h1>' . $title . '</h1>';

    foreach ($json as $post) 
    {
        if(!is_object($post))
        {
            continue;
        }
        // $readyPost[] = $config->makeTableHtml($post->table);
        
        $readyPost[] = $postMaker->make($post->rewritedContent);
        // $categories[] = $post->category;
        // $tags[] = $post->tag;
    }
    
    $content = implode("\n" ,$readyPost);

    $uploadedTxt = $config->getFilePath('Info', 'uploaded.txt');
    $fileData = file_get_contents($uploadedTxt);
    
    $uploadedData = explode("\n", $fileData);

    if(in_array($title, $uploadedData))
    {
        continue;
    }

    $uploaded = $wp_uploader->upload(
        [
            'title' => $title,
            'slug' => str_replace(" ","-",$title),
            'content' => $content,
            // 'featured_media' => $json->image,
            'categories' => $categories,
            'tags' => $tags,
            'status' => 'draft'
            ]
        );
        
        if($uploaded == true)
        {
            
            echo $json->title . "\t Uploaded\n";
            
            $json->uploaded = 1;

            file_put_contents($uploadedTxt, strtolower($title) . "\n", FILE_APPEND );
            
            if (file_put_contents($uploadedPath . $file, json_encode($json)) !== false) 
            {
                unlink($processedPath  . $file);
            }
            
        
    }
    else 
    {
        $json->code = $uploaded;
        if (file_put_contents($failedPath . $file, json_encode($json)) !== false) 
        {
            unlink($processedPath . $file);
        }

    }

}
