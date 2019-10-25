<?php

namespace AklykovAO\Bitrix24\Api;
use AklykovAO\Bitrix24\Exception\NotImplementedException;

/**
 * Класс для работы с массивом абстрактной сущностью
 *
 * Class EntityCollection
 * @package Zografdd\Moysklad
 */
abstract class EntityCollection
{
	protected static $entity;
	protected static $cache = [];
	protected $data = [];

	protected function __construct($data=[])
	{
		$this->data = $data;
	}

	/**
	 * Получить данные сущности
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Получить данные сущности
	 *
	 * @return bool
	 */
	public function isSuccess()
	{
		return (bool) !empty($this->data[0]['ID']);
	}

	/**
	 * Получить массив данных сущности
	 *
	 * @param int $id
	 * @return static
	 */
	public static function get($id=0)
	{
		$data = Client::getInstance()->query(static::$entity.'.get', [
			'ID' => $id
		]);

		$entity = new static($data['result']);

		return $entity;
	}

	/**
	 * Обновить массив данных сущности
	 *
	 * @param int $id
	 * @param array $params
	 * @return static
	 */
	public static function set($id=0, $params=[])
	{
		throw new NotImplementedException('Метод не поддерживается!');
	}

}