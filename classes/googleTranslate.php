<?php

class googleTranslate
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

		$this->client->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:82.0) Gecko/20100101 Firefox/82.0');
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

		$languages = ['fr', 'de', 'en'];

		$fromLanguage = 'en';

		foreach ($languages as $toLanguage)
		{
			$text = $this->translate($text, $fromLanguage, $toLanguage);

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

		if ($response->status != 200)
		{
			return false;
		}

		$data = json_decode($response->body);

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

	public function translate($text, $fromLanguage, $toLanguage)
	{
		$failed = false;
		// $text1 = $text;
		

		$url = 'http://translate.googleapis.com/translate_a/single?client=gtx&sl=' . $fromLanguage . '&tl=' . $toLanguage . '&dt=t&q=' . urlencode($text);

		// $proxyRotator = ProxyRotator::getInstance();
		$config = Config::getInstance();
		$response = null;

		// if ($proxyRotator->hasProxies())
		// {
		// 	do {
		// 		$proxy = $proxyRotator->get();

		// 		if ($proxy == false)
		// 		{
		// 			break;
		// 		}

		// 		$this->client->setProxy($proxy);
		// 		$response = $this->client->request('GET', $url);

		// 		if ($response->status != 200)
		// 		{
		// 			$proxyRotator->block($proxy);
		// 		}

		// 		if ($config->getVar("verbose"))
		// 		{
		// 			echo $proxy . "\t" . $response->status . "\t" . $url . "\r\n";
		// 		}

		// 	} while(!$this->checkResponse($response));
		// }
		// else
		// {
			// $this->client->setProxy(false);
			$response = $this->client->request('GET', $url);
			echo "\t" . $response->status . "\t" . $url . "\r\n";

		// }

		if (empty($response) || $response->status != 200)
		{
			$failed = true;
		}

		$data = $response->body;
		$data = json_decode($data);

		if (empty($data))
		{
			$failed = true;

		}

		if (count($data) == 9)
		{
			$failed = true;
		}

		$text = '';

		foreach ($data[0] as $item)
		{
			$text .= $item[0];
		}

		// echo "OK\r\n";
		
		if($failed == true)
		{
			$text = $this->translate($text, $fromLanguage, $toLanguage);
		}

		return $text;
	}
}