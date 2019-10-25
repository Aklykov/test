<?php

namespace AklykovAO\Bitrix24\Api;
use \GuzzleHttp\Exception\ClientException;

class Client
{
	private $domain = 'https://#CENSORED#/rest/104/8fz4q1ozungaomv2';
	private $countRequest = 0;

	const COUNT_REQUEST_FOR_STER = 20;

	private static $instance = null;

	private function __construct()
	{

	}

	public static function getInstance()
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

	public function query($url='', $data=[])
	{
		$this->checkCountRequest();
		$url = $this->checkUrl($url);
		try
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->request(
				'GET',
				$url,
				['query' => $data]
			);

			return json_decode($request->getBody()->getContents(), true);
		}
		catch (ClientException $e)
		{
			return json_decode($e->getResponse()->getBody()->getContents(), true);
		}
	}
}