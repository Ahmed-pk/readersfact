<?php 

class csvReader
{

    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getData($index = null)
    {
        $csvFile = file($this->path);
        $data = [];
        if($index == null)
        {
            foreach ($csvFile as $line) {
                $data[] = str_getcsv($line);
            }
        }
        else 
        {
            foreach ($csvFile as $line) {
                $data[] = str_getcsv($line)[$index];
            }
        }
        
        return $data;
    }
    
}