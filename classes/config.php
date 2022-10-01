<?php

class config
{
    private static $_instance = null;
    private $_vars = [];

    public static function getInstance()
    {
        if( is_null(self::$_instance) )
        {
            self::$_instance = new config;
        }

        return self::$_instance;
    }

    public function setVar($name,$value)
    {
        $this->_vars[$name] = $value;
    }

    public function getVar($name)
    {
        return $this->_vars[$name];
    }

    public function getPath($path)
    {
        // DataCrawled
        $path = preg_split('/(?=[A-Z])/', $path);
        $path = array_filter($path);
        $path = array_map('strtolower', $path);
        $path = implode(DS, $path) . DS;
        return $path;
    }

    public function getFileData($path,$file)
    {
        $path = $this->getPath($path);
        $filepath = $path . $file;
        $data = file_get_contents($filepath);
        return $data;
    }

    public function getFilePath($path,$file)
    {
        $path = $this->getPath($path);
        $filepath = $path . $file;
        return $filepath;
    }

    public function getLines($file_path,$comments = true)
    {
        $file = file_get_contents($file_path);
        $lines_array = explode("\n",$file);
        if($comments)
        {
            $lines_array = preg_replace('/\#(.*)/','',$lines_array);
            $lines_array = array_filter($lines_array);
        }
        
        
        return array_values($lines_array);
    }

    public function makeTableHtml($table)
    {
        $finalTable = '<table><tbody>';
        foreach ($table as $key => $value) 
        {
            $finalTable .= "<tr><td>$key</td><td>$value</td></tr>";
        }
        $finalTable .= '</table></tbody>';
        return $finalTable;
    }
}