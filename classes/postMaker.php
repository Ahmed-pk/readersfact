<?php

class postMaker
{
    public function make($qna)
    {
        $post = '';
        
        foreach ($qna as $q)
        {
            $post .= "<h2>" . $q->qq . "</h2>\n";
            $post .= "<p>" . $q->qa . "</p>\n";
        }
        return $post;
    }
}