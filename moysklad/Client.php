<?php

namespace Zografdd\Moysklad;
use \GuzzleHttp\Exception\ClientException;

class Client
{
	private $api = null;
	private $login = '$login';
	private $password = '$password';
	private $domain = 'https://online.moysklad.ru/api/remap/1.1';
	private $countRequest = 0;

	const COUNT_REQUEST_FOR_STER = 100;

	private static $instance = null;

	private function __construct()
	{

	}

	static public function getInstance()
	{
		if(is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function checkUrl($url='')
	{
		if (strpos($url, $this->domain) === false)
			$url = $this->domain.$url;

		return $url;
	}

	private function checkCountRequest()
	{
		$this->countRequest++;
		if ($this->countRequest % static::COUNT_REQUEST_FOR_STER == 0)
			sleep(1);
	}

	public function get($url='')
	{
		$this->checkCountRequest();
		$url = $this->checkUrl($url);

		try
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->request(
				'GET',
				$url,
				[
					'auth' => [$this->login, $this->password]
				]
			);

			return json_decode($request->getBody()->getContents(), true);
		}
		catch (ClientException $e)
		{
			return json_decode($e->getResponse()->getBody()->getContents(), true);
		}
	}

	public function put($url='', $data=[])
	{
		$this->checkCountRequest();
		$url = $this->checkUrl($url);

		try
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->request(
				'PUT',
				$url,
				[
					'auth' => [$this->login, $this->password],
					'json' => $data
				]
			);

			return json_decode($request->getBody()->getContents(), true);
		}
		catch (ClientException $e)
		{
			return json_decode($e->getResponse()->getBody()->getContents(), true);
		}
	}

	public function post($url='', $data=[])
	{
		$this->checkCountRequest();
		$url = $this->checkUrl($url);

		try
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->request(
				'POST',
				$url,
				[
					'auth' => [$this->login, $this->password],
					'json' => $data
				]
			);

			return json_decode($request->getBody()->getContents(), true);
		}
		catch (ClientException $e)
		{
			return json_decode($e->getResponse()->getBody()->getContents(), true);
		}
	}

	public function delete($url='')
	{
		$this->checkCountRequest();
		$url = $this->checkUrl($url);

		try
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->request(
				'DELETE',
				$url,
				[
					'auth' => [$this->login, $this->password]
				]
			);

			return json_decode($request->getBody()->getContents(), true);
		}
		catch (ClientException $e)
		{
			return json_decode($e->getResponse()->getBody()->getContents(), true);
		}

	}
}