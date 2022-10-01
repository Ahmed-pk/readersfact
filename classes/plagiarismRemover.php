<?php

class plagiarismRemover
{

    public $client;

    function __construct()
    {
        $this->client = new HttpClient;
    }

    public function rewrite_sentence($sentence)
    {
        $gt = new googleTranslate($this->client);
        return $gt->translateAll($sentence);
    }

    public function rewrite_qa($qna)
    {

        foreach ($qna as &$q)
        {
            $q['qq'] = str_replace("<br>", "", html_entity_decode($q['qq']));
            $q['qa'] = str_replace("<br>", "", html_entity_decode($q['qa']));
            
            if(str_word_count($q['qq']) < 3)
            {
                continue;
            }
            
            $q['qq'] = $this->rewrite_sentence($q['qq']);
            
            if(str_word_count($q['qa']) < 3)
            {
                continue;
            }
            
            $q['qa'] = $this->rewrite_sentence($q['qa']);
            

        }

        return $qna;

    }

    public function rewrite_from_html($html)
    {

        $html_array = preg_split('/(<[^>]*[^\/]>)/i', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $final_post = [];

        foreach ($html_array as $key => $tag) 
        {
            // skip table
            if($tag != strip_tags($tag) || str_word_count($tag) < 3)
            {
                $final_post[] = $tag;
                continue;
            }

            $final_post[] = $this->rewrite_sentence($tag);

        }

        $final_post = implode('',$final_post);
        return $final_post;

    }

}