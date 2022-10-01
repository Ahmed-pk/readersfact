<?php

class functions
{
    public function makeFileName($str)
    {
        $str  = preg_replace('/[^a-z0-9]/i', '', $str);
        $str  = strtolower($str);
        return md5($str) . ".json";
    }

    public function getLinks($crawler)
    {
        $config = config::getInstance();
        $csv = new csvReader( $config->getFilePath("Csv","$crawler.csv") );
        $urls = $csv->getData("0");
        $urls = array_map(function ($string){
            $string = preg_replace('/\#.*/', '', $string);
            if(!preg_match('/page\/\d+/is', $string))
            {
                return $string;
            }
        },$urls);
        $urls = array_filter($urls);
        $urls = array_values($urls);
        return $urls;
    }

    public function removeUselessTags($post)
    {
        $post = strip_tags($post,'<p><ol><ul><li><table><tbody><thead><th><td><tbody><tr><h1><h2><h3><h4><h5><h6>');
        return $post;
    }

    public function removeAttr($post)
    {
        $post =  preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si",'<$1$2>', $post);
        return $post;
    }   
}