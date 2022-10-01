<?php

class faqans
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
        $content = "";
        $html = $this->client->request("GET", $link);
        
        preg_match('/<title>(.*?)<\/title>/', $html->body, $title);
        $title = $title[1];

        preg_match('/<script>__S2T.startHTML\((.*?)\);<\/script>/is', $html->body, $match);
        $match = $match[1];
        $match = json_decode($match);

        $content = $match->rawdata->qqa;

        return 
        [
            'title' => $title,
            'content' => $content,
        ];
        
    }
}