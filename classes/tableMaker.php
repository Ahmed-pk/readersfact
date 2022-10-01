<?php

class tableMaker 
{
    function make($html)
    {
        // echo $html;
        preg_match_all('/<tr>(.*?)<\/tr>/si', $html, $matches);
        $table = [];
        foreach($matches[1] as $match)
        {
            if(preg_match('/<td>(.*?)<\/td>\s*<td>(.*?)<\/td>/is', $match, $data))
            {
                // print_r($data);
                if(isset($data[1]) && isset($data[1]))
                {
                    $table[$data[1]] = $data[2];

                }
            }
        }
        return $table;
    }
}

?>