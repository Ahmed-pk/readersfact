<?php

class starsgab
{
    public $links;
    public $client;
    public $functions;

    public function __construct()
    {
        $this->func = new functions;
        $this->client = new HttpClient();
    }

    public function getData($link)
    {
        $link = str_replace("-biography", "", $link);

        preg_match('/https:\/\/starsgab.com\/(.*?)\//', $link, $name);
        $name = $name[1];   
        $name = str_replace("-", " ", $name);   
        
        $data = $this->client->request("GET",$link);

        $body = $data->body;
        preg_match('/<h1 class="entry-title">(.*?)<\/h1>/is', $body, $title);
        $title = $title[1];
        preg_match('/<div class="entry-content clearfix">(.*?)<div class="entry-content clearfix">/is', $body, $content);
        $content = $content[1];

        $content = str_replace('<p class="ez-toc-title">Table of Contents</p>', "", $content);
        $content = str_replace('<p><strong>Read Also:</strong></p>', "", $content);
        $content = str_replace('<p>Read Also : </p>', "", $content);
        $content = preg_replace('/<nav>(.*?)<\/nav>/', "", $content);
        $content = preg_replace('/<a (.*?)<\/a>/', "", $content);
        $content = $this->func->removeUselessTags($content);
        $content = $this->func->removeAttr($content);
        $content = htmlspecialchars_decode($content, ENT_QUOTES);
        $title = htmlspecialchars_decode($title, ENT_QUOTES);
        $content = preg_replace('/<p>Read Also.*?<\/p>/is', "", $content);
        
        return 
        [
            'name' => $name,
            'title' => $title,
            'content' => $content,
        ];
    }
}