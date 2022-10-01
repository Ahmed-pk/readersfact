<?php
//Cache

class HttpClientException extends Exception
{
}

class HttpClient
{
	/**
	 * @var     array   Container for options.
	 * @since   0.1.0
	 */
	private $options;

	/**
	 * @var     array   Container for http headers.
	 * @since   0.1.0
	 */
	private $headers;

	/**
	 * @var     array   Container for http cookies.
	 * @since   0.1.0
	 */
	private $cookies;

	/**
	 * @var     array   Container for requests.
	 * @since   0.1.0
	 */
	private $requests;

	/**
	 * @var     boolean   Container for multi-threading.
	 * @since   0.1.0
	 */
	private $multiThreading;

	/**
	 * @var     int   Container for delay between requests.
	 * @since   0.1.0
	 */
	private $delay;

	/**
	 * @var     boolean   Container for curl auto redirection support.
	 * @since   0.1.0
	 */
	private $autoRredirection;

	/**
	 * Set default options.
	 *
	 * @param   string|array   Options for the http client.
	 *
	 * @since   0.1.0
	 */
	public function __construct($options=array())
	{
		if (!extension_loaded('curl'))
		{
			throw new HttpClientException('Curl extension is required for HttpClient.');
		}

		if (false == ini_get('safe_mode') && false == ini_get('open_basedir'))
		{
			$this->autoRedirection = true;
		}

		$defaults = array(
			'threading'   => false,
			'delay'       => 0,
			'timeout'     => 30,
			'redirect'    => true,
			'maxredirect' => 15,
			'useragent'   => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1',
			'encoding'    => ''
		);

		$options = $this->parseOptions($options, $defaults);

		$setters = array(
			'multithreading' => 'setMultiThreading',
			'delay'          => 'setDelay',
			'headers'        => 'setHeaders',
			'timeout'        => 'setTimeout',
			'referer'        => 'setReferer',
			'redirect'       => 'setRedirect',
			'maxredirect'    => 'setMaxRedirect',
			'useragent'      => 'setUserAgent',
			'cookies'        => 'setCookies',
			'cookiefile'     => 'setCookieFile',
			'proxy'          => 'setProxy',
			'proxyauth'      => 'setProxyAuth',
			'encoding'       => 'setEncoding',
			'interface'      => 'setInterface',
			'authorization'  => 'setAuthorization'
		);

		foreach ($options as $name => $option)
		{
			if (isset($setters[$name]))
			{
				call_user_func(array($this, $setters[$name]), $option);
			}
		}

		$this->methods = array('GET', 'POST', 'HEAD');
	}

	/**
	 * Set multi-threading flag for client.
	 *
	 * @param   boolean|string   $value   Enable/disable multi threading for client.
	 *
	 * @since   0.1.0
	 */
	public function setMultiThreading($value)
	{
		$this->multiThreading = $this->getBoolean ($value);
	}

	/**
	 * Set delay between requests for client.
	 *
	 * @param   int   $value   Delay in micro-seconds between 2 subsequent requests.
	 *
	 * @since   0.1.0
	 */
	public function setDelay($value)
	{
		$this->delay = (int) $value;
	}

	/**
	 * Set cookies value for options.
	 *
	 * @param   string|array   $value   Cookies to be sent for requests.
	 *
	 * @since   0.1.0
	 */
	public function setCookies($value)
	{
		$this->cookies = $this->getCookieArray($value);
	}

	/**
	 * Set http headers value for options.
	 *
	 * @param   string|array   $value   Http headers to be sent with request.
	 *
	 * @since   0.1.0
	 */
	public function setHeaders($value)
	{
		$this->headers = is_array($value) ? $value : array($value);
	}

	/**
	 * Set timeout value for client options.
	 *
	 * @param   int   $value   Timeout in seconds for client options.
	 *
	 * @since   0.1.0
	 */
	public function setTimeout($value)
	{
		$this->options['timeout'] = (int) $value;
	}

	/**
	 * Set referer value for client options.
	 *
	 * @param   string   $value   Http referer to be used in requests.
	 *
	 * @since   0.1.0
	 */
	public function setReferer($value)
	{
		$this->options['referer'] = $value;
	}

	/**
	 * Set redirect value for client options.
	 *
	 * @param   boolean|string   $value   Auto redirection for a request.
	 *
	 * @since   0.1.0
	 */
	public function setRedirect($value)
	{
		$this->options['redirect'] = $this->getBoolean ($value);
	}

	/**
	 * Set max-redirect value for client options.
	 *
	 * @param   int   $value   Maximum auto redirections for a request.
	 *
	 * @since   0.1.0
	 */
	public function setMaxRedirect($value)
	{
		$this->options['maxredirect'] = (int) $value;
	}

	/**
	 * Set useragent value for options.
	 *
	 * @param   string   $value   UserAgent to be used for requests.
	 *
	 * @since   0.1.0
	 */
	public function setUserAgent($value)
	{
		$this->options['useragent'] = $value;
	}

	/**
	 * Set cookiefile value for options.
	 *
	 * @param   string   $value   Absolute path to store cookies for client.
	 *
	 * @since   0.1.0
	 */
	public function setCookieFile($value)
	{
		$this->options['cookiefile'] = $value;
	}

	/**
	 * Set proxy value for options.
	 *
	 * @param   string   $value   Proxy through which requests to be sent.
	 *
	 * @since   0.1.0
	 */
	public function setProxy($value)
	{
		$this->options['proxy'] = $value;
	}

	/**
	 * Set proxy authentication value for options.
	 *
	 * @param   string   $value   Authentication for the proxy used.
	 *
	 * @since   0.1.0
	 */
	public function setProxyAuth($value)
	{
		$this->options['proxyauth'] = $value;
	}

	/**
	 * Set content encoding value for options.
	 *
	 * @param   string   $value   Content encoding for client.
	 *
	 * @since   0.1.0
	 */
	public function setEncoding($value)
	{
		$this->options['encoding'] = $value;
	}

	/**
	 * Set interface value for options.
	 *
	 * @param   string   $value   IP address through which requests to be sent.
	 *
	 * @since   0.1.0
	 */
	public function setInterface($value)
	{
		$this->options['interface'] = $value;
	}

	/**
	 * Set username password value for options.
	 *
	 * @param   string   $value   Username:Password for authentication.
	 *
	 * @since   0.1.0
	 */
	public function setAuthorization($value)
	{
		$this->options['authorization'] = $value;
	}

	public function request($method, $url, $params=null, $options=null, $cookies=null, $headers=null)
	{
		if (!in_array($method, $this->methods))
		{
			throw new HttpClientException(sprintf('Method "%s" is not supported by HttpClient.', $method));
		}

		$hash1 = md5(print_r([$method, $url, $params, $options, $cookies, $headers], true));

		if (preg_match('|/[^/?#]+|si', $url, $match))
		{
			$title = preg_replace('|[^a-z\.\-_0-9 ]+|si', '-', ltrim($match[0], '/'));

			if (!empty($title))
			{
				$hash = $title . "/" . $hash1;
			}
			else
			{
				$hash = md5($match[0]) . "/" . $hash1;
			}
		}
		else
		{
			$hash = $hash1;
		}

		$file = "cache/${hash}.txt";

		@mkdir(dirname($file), 0777, true);

		if (file_exists($file))
		{
			$response = unserialize(file_get_contents($file));
			$response->cacheFile = $file;

			if ($response->status == 200)
			{
				if (preg_match('|^http://translate.googleapis.com/|si', $url))
				{
					$d = json_decode($response->body);

					if (!empty($d) && count($d) != 9)
					{
						return $response;
					}
				}
				else
				{
					return $response;
				}
			}
		}

		$this->addRequest (0, $method, $url, $params, $options, $cookies, $headers);

		$response = $this->execute ();
		$response = $response[0];

		if ($response->status == 200)
		{
			// echo "Live Request: " . $url . "\r\n";
			file_put_contents($file, serialize($response));
		}

		return $response;
	}

	public function requests($requests, $params=null, $options=null, $cookies=null, $headers=null)
	{
		foreach ($requests as $key=>$request)
		{
			if (is_array($request))
			{
				$_params = $params;
				$_options = $options;
				$_cookies = $cookies;
				$_headers = $headers;

				$method = $request[0];

				if (!in_array($method,$this->methods))
				{
					throw new HttpClientException(sprintf('Method "%s" is not supported by HttpClient.', $method));
				}

				$url = $request[1];
				$_params = isset($request[2]) && !is_null($request[2]) ? $request[2] : $_params;
				$_options = isset($request[3]) ? $this->parseOptions($request[3], $_options) : $_options;
				$_cookies = isset($request[4]) && !is_null($request[4]) ? $request[4] : $_cookies;
				$_headers = isset($request[5]) && !is_null($request[5]) ? $request[5] : $_headers;

				$this->addRequest($key, $method, $url, $_params, $_options, $_cookies, $_headers);
			}
		}

		$responses = $this->execute ();

		return $responses;
	}

	private function addRequest($key, $method, $url, $params, $options, $cookies, $headers)
	{
		$options = $this->parseOptions($options, $this->options);
		$headers = !is_null ($headers) ? $headers : $this->headers;
		$cookies = !is_null ($cookies) ? $cookies : $this->cookies;

		$cookies = $this->getCookieArray($cookies);

		$request = array
		(
			'method' => $method,
			'url' => $url,
			'params' => $params,
			'options' => $options,
			'cookies' => $cookies,
			'headers' => $headers
		);

		$this->requests[$key] = $request;
	}

	private function execute()
	{
		$threads = array();

		foreach ($this->requests as $key => $requests)
		{
			$threads[$key] = $this->createThread ($requests);
		}

		if (true == $this->multiThreading && count($threads)>1)
		{
			$responses = $this->runMulti ($threads);
		}
		else
		{
			$responses = $this->runSingle ($threads);
		}

		unset ($this->requests);

		return $responses;
	}

	private function createThread($request)
	{
		$request['headers'] = is_array($request['headers']) ? $request['headers'] : array($request['headers']);
		$request['cookies'] = $this->getCookieString($request['cookies']);

		$options = array();
		$options[CURLOPT_URL]            = $request['url'];
		$options[CURLOPT_TIMEOUT]        = is_numeric($request['options']['timeout']) ? $request['options']['timeout'] : 30;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_FRESH_CONNECT]  = true;
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_FOLLOWLOCATION] = $this->autoRedirection==true && $request['options']['redirect']==true ? true : false;
		$options[CURLOPT_AUTOREFERER]    = $this->autoRedirection==true && $request['options']['redirect']==true ? true : false;
		$options[CURLOPT_MAXREDIRS]      = $this->autoRedirection==true && isset($request['options']['maxredirect']) ? $request['options']['maxredirect'] : 0;
		$options[CURLOPT_HEADER]         = true;
		$options[CURLINFO_HEADER_OUT]    = true;
		$options[CURLOPT_NOBODY]         = $request['method']=='HEAD' ? true : false;
		$options[CURLOPT_REFERER]        = isset($request['options']['referer']) ? $request['options']['referer'] : '';
		$options[CURLOPT_USERAGENT]      = isset($request['options']['useragent']) ? $request['options']['useragent'] : '';
		$options[CURLOPT_ENCODING]       = isset($request['options']['encoding']) ? $request['options']['encoding'] : '';
		$options[CURLOPT_HTTPHEADER]     = is_array($request['headers']) ? $request['headers'] : array('Accept: text/html, application/xhtml+xml, application/xml, */*');
		$options[CURLOPT_COOKIEJAR]      = isset($request['options']['cookiefile']) ? $request['options']['cookiefile'] : '';
		$options[CURLOPT_COOKIEFILE]     = isset($request['options']['cookiefile']) ? $request['options']['cookiefile'] : '';
		$options[CURLOPT_COOKIE]         = isset($request['cookies']) ? $request['cookies'] : '';
		$options[CURLOPT_PROXY]          = isset($request['options']['proxy']) ? $request['options']['proxy'] : '';
		$options[CURLOPT_PROXYUSERPWD]   = isset($request['options']['proxyauth']) ? $request['options']['proxyauth'] : '';
		$options[CURLOPT_POST]           = $request['method']=='POST' ? true : false;
		
		if (isset($request['options']['authorization']))
		{
			$options[CURLOPT_USERPWD]    = $request['options']['authorization'];
		}

		if ('POST' == $request['method'] && isset($request['params']))
		{
			$options[CURLOPT_POSTFIELDS] = $request['params'];
		}
		
		if (isset($request['options']['interface']))
		{
			$options[CURLOPT_INTERFACE]  = $request['options']['interface'];
		}

		$thread = curl_init();

		foreach ($options as $optionKey => $optionValue)
		{
			curl_setopt($thread, $optionKey, $optionValue);
		}

		return $thread;
	}

	private function runSingle($threads)
	{
		$responses = array();
		$completed = 0;
		$count = count ($threads);
		
		foreach ($threads as $key => $thread)
		{
			$data = @curl_exec ($thread);
			$info = @curl_getinfo ($thread);
			$error = @curl_error($thread);
			@curl_close ($thread);

			if (true == $this->requests[$key]['options']['redirect'] && false == $this->autoRedirection)
			{
				$response = $this->redirect($data, $info, $error, $this->requests[$key]);
			}
			else
			{
				$response = $this->getResponse($data, $info, $error, $this->requests[$key]);
			}

			$responses[$key] = $response;

			$completed++;

			if ($this->delay>0 && $completed<$count)
			{
				usleep ($this->delay);
			}
		}

		return $responses;
	}

	private function runMulti($threads)
	{
		$responses = array();
		$mc = curl_multi_init();

		foreach ($threads as $key => $thread)
		{
			@curl_multi_add_handle($mc, $thread);
		}

		$running = null;
		do
		{
			@curl_multi_exec($mc, $running);
			usleep(250);
		}
		while ($running > 0);

		foreach ($threads as $key => $thread)
		{
			$data = @curl_multi_getcontent($thread);
			$info = @curl_getinfo($thread);
			$error = @curl_error($thread);
			@curl_multi_remove_handle($mc, $thread);
			@curl_close($thread);

			if (true == $this->requests[$key]['options']['redirect'] && false == $this->autoRedirection)
			{
				$response = $this->redirect($data, $info, $error, $this->requests[$key]);
			}
			else
			{
				$response = $this->getResponse($data, $info, $error, $this->requests[$key]);
			}

			$responses[$key] = $response;
		}

		curl_multi_close ($mc);

		return $responses;
	}

	private function getResponse($data, $info, $error, $request)
	{
		$response = (object) array();
		$response->status = $info['http_code'];
		$headers = substr($data, 0, $info['header_size']);
		$response->body = substr($data, $info['header_size']);
		$response->info = $info;
		$response->error = $error;

		if (isset($request['options']['file']))
		{
			$fp = @fopen($request['options']['file'], 'w');

			if (true == $fp)
			{
				@fwrite ($fp, $response->body);
				@fclose($fp);
			}

			$response->body = true;
		}

		$response->cookies = $this->parseCookies ($headers);
		$response->headers = $this->parseHeaders ($headers);
		$response->request = $request;
		$response->request['request_headers'] = @$info['request_header'];

		unset ($response->info['request_header']);

		return $response;
	}

	private function redirect($data, $info, $error, $request)
	{
		$status = $info['http_code'];
		$response = $this->getResponse($data, $info, $error, $request);

		if ('301' == $status || '302' == $status)
		{
			if (isset($response->headers['location']))
			{
				$redirects = array();
				$redirected = 0;
				$_request = $request;

				do
				{
					$redirects[] = $response;

					$_request['method'] = 'GET';
					$_request['options']['referer'] = $_request['url'];
					$_request['url'] = $this->getRedirectionUrl ($_request['url'], $response->headers['location']);

					if (!isset($_request['options']['cookiefile']))
					{
						if (is_array($_request['cookies']) && is_array($response->cookies))
						{
							$_request['cookies'] = array_merge ($_request['cookies'], $response->cookies);
						}
					}

					$thread = $this->createThread ($_request);

					$data = @curl_exec ($thread);
					$info = @curl_getinfo ($thread);
					$error = @curl_error($thread);
					@curl_close ($thread);

					$response = $this->getResponse ($data, $info, $error, $_request);
					
					$status = $info['http_code'];
					
					if ('301' != $status && '302' != $status)
					{
						break;
					}

					$redirected++;
				}
				while ($redirected<$request['options']['maxredirect']);

				if (count($redirects)>0)
				{
					$response->redirects = $redirects;
				}
			}
		}

		return $response;
	}

	private function getRedirectionUrl($url, $location)
	{
		$url = parse_url ($url);

		if (preg_match('|^http[s]?://|', $location))
		{
			return $location;
		}
		else if (preg_match('|^/.*$|', $location))
		{
			return $url['scheme'] . '://' . $url['host'] . $location;
		}
		else
		{
			$url['path'] = preg_replace ('|/+|', '/', $url['path']);
			$url['path'] = preg_replace('|/[^/]+\.[^/]+$|', '', $url['path']);
			$location = preg_replace ('|/+|', '/', $location);

			$path = explode ('/', $url['path']);
			$location = explode ('/', $location);

			array_shift ($path);

			foreach ($location as $loc)
			{
				if ('..' == $loc)
				{
					array_pop ($path);
				}
				else if ('.' != $loc)
				{
					array_push ($path, $loc);
				}
            }

			return $url['scheme'] . '://' . $url['host'] . '/' . join('/', $path);
		}
	}

	private function parseHeaders($data)
	{
		$headers = array();

		if (preg_match_all('|(.+): (.+)|', $data, $matches, PREG_SET_ORDER))
		{
			foreach ($matches as $match)
			{
				$name = strtolower($match[1]);
				if ('set-cookie' == $name)
				{
					$headers[$name][] = trim($match[2]);
				}
				else
				{
					$headers[$name] = trim($match[2]);
				}
			}
		}

		return $headers;
	}

	private function parseCookies($data)
	{
		$cookies = array();

		if (preg_match_all('|(.+): (.+)|', $data, $matches, PREG_SET_ORDER))
		{
			foreach ($matches as $match)
			{
				if ('set-cookie' == strtolower($match[1]))
				{
					$cookie = trim($match[2]);

					if (preg_match('|^([^=]+)=([^;]+)|', $cookie, $match))
					{
						$cookies[$match[1]] = $match[2];
					}
				}
			}
		}

		return $cookies;
	}

	private function getCookieArray($cookies)
	{
		if (!is_array($cookies) && !empty($cookies))
		{
			$_cookies = str_replace ('; ', ';', $cookies);
			$_cookies = explode (';', $_cookies);

			$cookies = array ();

			foreach ($_cookies as $_cookie)
			{
				$_cookie = explode ('=', $_cookie);
				$cookies[@$_cookie[0]] = @$_cookie[1];
			}

			if (count($cookies)==0)
			{
				$cookie = '';
			}
		}

		return $cookies;
	}

	private function getCookieString($cookies)
	{
		if (is_array($cookies))
		{
			$_cookies = '';

			foreach ($cookies as $name => $value)
			{
				$_cookies .= $name . '=' . $value . '; ';
			}

			$cookies = rtrim ($_cookies, ' ');
		}

		return $cookies;
	}

	private function parseOptions($options, $defaults='')
	{
		if (!is_array($options))
		{
			parse_str ($options, $options);
		}

		if (!is_array($defaults))
		{
			parse_str ($defaults, $defaults);
		}

		$options = array_merge ($defaults, $options);

		return $options;
	}

	private function getBoolean($value)
	{
		if (is_string($value))
		{
			if ('true' == $value || '1' == $value)
			{
				$value = true;
			}
			else
			{
				$value = false;
			}
		}

		return (boolean) $value;
	}
}