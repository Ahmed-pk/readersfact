<?php

class thewikifeed
{
    public $client;
    public $functions;

    public function __construct()
    {
        $this->func = new functions;
        $this->client = new HttpClient();
    }

    public function getData($link)
    {
        // print_r($link);exit;
        // $link = preg_replace('/\#.*/', '', $link);
        preg_match('/https:\/\/www.thewikifeed.com\/(.*?)\//', $link, $name);
        $name = $name[1];   
        $name = str_replace("-", " ", $name);   
        
        $data = $this->client->request("GET",$link);

        $body = $data->body;
        
        preg_match('/<h1 class="entry-title"><span class="entry-title-span">(.*?)<\/span><\/h1>/is', $body, $title);
        if(!isset($title[1]))
        {
            return false;
        }
        $title = $title[1];
        
        preg_match('/<div class="entry-content">(.*?)<nav class="navigation single-navigation meta-container row">/is', $body, $content);
        $content = $content[1];
        $content = str_replace('<p class="ez-toc-title">Table of Contents</p>', "", $content);
        $content = preg_replace('/<p>Read Also :(.*?)<\/p>/', "", $content);
        $content = preg_replace('/<div id="toc_container" (.*?)<\/li><\/ul><\/div>/', "", $content);
        $content = preg_replace('/<a (.*?)<\/a>/', "", $content);
        $content = $this->func->removeUselessTags($content);
        $content = $this->func->removeAttr($content);

        $content = htmlspecialchars_decode($content, ENT_QUOTES);
        $title = htmlspecialchars_decode($title, ENT_QUOTES);

        return 
        [
            'name' => $name,
            'title' => $title,
            'content' => $content,
        ];
    }
}