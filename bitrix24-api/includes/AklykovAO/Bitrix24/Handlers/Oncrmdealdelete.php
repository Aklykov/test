<?php

namespace AklykovAO\Bitrix24\Handlers;
use AklykovAO\Bitrix24\Api\Deal;
use AklykovAO\Bitrix24\Api\Product;

class Oncrmdealdelete extends Oncrm
{
	protected static $appToken = '#CENSORED#';

	/**
	 * Обработчик
	 *
	 * @param array $params
	 */
	public static function execute($post=[])
	{
		if (static::checkAuth($post) === false)
			return;

		$params = $post['data'];

		static::writeLog($params);
	}

}