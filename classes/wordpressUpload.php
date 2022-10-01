<?php
class WordpressUpload
{
    public $url = "https://www.readersfact.com/";
    public $user;


    public function PickRandomUser()
    {
        $config = config::getInstance();
        $file = $config->getVar('users');
        $users = $config->getLines($file,false);

        shuffle($users);
        $user = explode(":",$users[0]);
        // print_r($user);
        // exit;
        return 
        [
            'name' => $user[0],
            'password' => $user[1]
        ];

    }

    public function postExists($keyword)
    {
        $req = new HttpClient();
        $slug = str_replace(" ","-",$keyword);
        $status = $req->request("GET",$this->url.$slug);
        if($status->info['http_code'] == "404")
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function upload(array $topic)
    {
        $this->user = $this->PickRandomUser();
        $username = $this->user['name'];
        $password = $this->user['password'];
        $rest_api_url = $this->url ."wp-json/wp/v2/posts";

        // $img = $this->uploadMedia($topic['featured_media']);

        // $topic['featured_media'] = $img;

        $data_string = json_encode($topic);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rest_api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, 
            [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                'Authorization: Basic ' . base64_encode($username . ':' . $password)
            ]
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);
    
        $json=json_decode($result,true);


        print_r($json);

        if (isset($json['data']['status'])) 
        {
            return $json['data']['status'];
        } 
        else 
        {
            return true;
        }
    }
    public function uploadMedia($localfile)
    {    
        $url = $this->url . "wp-json/wp/v2/media";
        $username = $this->user['name'];
        $password = $this->user['password'];
        $file = file_get_contents($localfile);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);     
        curl_setopt($ch, CURLOPT_TIMEOUT,30);     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $uaa = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3'; 
        curl_setopt($ch, CURLOPT_USERAGENT, $uaa);
        
        $filename = explode("/",$localfile);
        $filename = array_pop($filename);
        
        // $filetype = mime_content_type($localfile);
        
        $header = array(
            'Authorization: Basic ' . base64_encode($username . ':' . $password),       
            "cache-control: no-cache",
            "content-disposition: attachment; filename=$filename",
            "content-type: png",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        
        $json=json_decode(curl_exec($ch),true);
        return $json['id'];
    }
}
