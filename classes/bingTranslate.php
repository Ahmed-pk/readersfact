<?php

class bingTranslate
{
	public $client;

	public function __construct($client = null)
	{
		if (!empty($client))
		{
			$this->client = $client;
		}
		else
		{
			$this->client = new HttpClient();
		}

		$this->client->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36');
	}

	public function translateAll($text)
	{

		$text = str_replace('"', "", $text);
		$text = str_replace("'", "", $text);
		$text = str_replace(";", "", $text);
		$text = str_replace("-", "", $text);
		$text = preg_replace("|[\r\n]|", "", $text );

	    $text = preg_replace('/\s\s+/', ' ', $text);


		if (strlen($text) > 3900)
		{
			return false;
		}

		$languages = ['fi', 'hun', 'en'];

		$fromLanguage = 'en';

        $getToken = $this->client->request('GET','https://www.bing.com/translator');
        $Tokenbody = $getToken->body;
		// echo $getToken->info['http_code'];
        preg_match('/var params_RichTranslateHelper = \[(.*?),"(.*?)"/is',$Tokenbody,$Token_matches);
        preg_match('/,IG:\"(.*?)\",/is',$Tokenbody,$IG_matches);
        $key = $Token_matches[1];
        $token = $Token_matches[2];
        $ig = $IG_matches[1];
		// echo "key = $key; token = $token; IG = $ig;";exit;
		foreach ($languages as $number => $toLanguage)
		{
			$text = $this->translate($text, $fromLanguage, $toLanguage, $token, $key, $ig, $number + 1);

			if (empty(trim($text)))
			{
				return false;
			}

			$fromLanguage = $toLanguage;
		}

		return $text;
	}

	public function checkResponse($response)
	{

		if ($response['status'] != 200)
		{
			return false;
		}

		$data = json_decode($response['body']);

		if (empty($data) || !is_array($data))
		{
			return false;
		}

		if (count($data) == 9)
		{
			return false;
		}

		return true;
	}

	public function translate($text, $fromLanguage, $toLanguage, $token, $key, $ig, $number)
	{
		// $text1 = $text;
		$text = urlencode($text);

		$url = "https://www.bing.com/ttranslatev3?isVertical=1&&IG=$ig&IID=translator.5023.$number";
        
        do {

            $response = $this->client->request('POST', $url,["" => "", 'from' => $fromLanguage,'text' => $text,'to' => $toLanguage,'token' => $token,'key' => $key]);

                echo "\t" . $response->info['http_code'] . "\t" . $url . "\r\n";
            

        } while($response->info['http_code'] !== 200);

		$data = $response->body;
        print_r($response);exit;
		$data = json_decode($data);
		if (!isset($data[0]->translations[0]->text))
		{
			return false;
		}

        $text = $data[0]->translations[0]->text;
		// echo "OK\r\n";

		return $text;
	}
}
