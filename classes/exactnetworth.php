<?php

class exactnetworth
{
    public $client;
    public $functions;

    public function __construct()
    {
 
        // print_r($this->links);exit;
        $this->func = new functions;
        $this->client = new HttpClient();
    }

    public function getData($link)
    {
        $link = str_replace("-net-worth", "", $link);
        preg_match('/https:\/\/exactnetworth.com\/(.*?)\//', $link, $name);

        if(!filter_var($link, FILTER_VALIDATE_URL) || !isset($name[1]))
        {
            return false;
        }
        $name = $name[1];   
        $name = str_replace("-", " ", $name);   
        // $link = "https://exactnetworth.com/tommyinnit-net-worth/";
        $data = $this->client->request("GET",$link);

        $body = $data->body;
        // echo $body;exit;
        preg_match('/<h1 .*?">(.*?)<\/h1>/is', $body, $title);
        $title = $title[1];
        
        preg_match('/<div id="mvp-content-main" class="left relative">(.*?)<div id="mvp-content-bot" /is', $body, $content);
        $content = $content[1];
        $content = preg_replace('/<hr \/>(.*?)<hr \/>/is', "", $content);
        $content = preg_replace('/<nav>(.*?)<\/nav>/is', "", $content);
        $content = preg_replace('/<a (.*?)<\/a>/is', "", $content);
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