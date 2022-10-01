<?php

class youtube
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

	public function getVideo($term)
	{
		$term = strtolower($term);
		$url = 'https://www.youtube.com/results?search_query=' . urlencode($term);

		// $proxyRotator = ProxyRotator::getInstance();
		// $config = Config::getInstance();
		// $response = null;

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

		// 	} while($response->status != 200 && $response->status != 404);
		// }
		// else
		// {
			// $this->client->setProxy(false);
			$response = $this->client->request('GET', $url);
		// }

		if (empty($response) || $response->info['http_code'] != 200)
		{
			return false;
		}

		if (preg_match_all('|"url":"/watch\?v=([^"]+)"|si', $response->body, $matches))
		{
			return "https://www.youtube.com/watch?v=" . $matches[1][0];
		}
	}

	public function search($term)
	{
		return $this->getVideo($term);
	}
}