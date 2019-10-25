<?php

namespace AklykovAO\Bitrix24\Api;

/**
 * Класс для работы с абстрактной сущностью
 *
 * Class Entity
 * @package Zografdd\Moysklad
 */
abstract class Entity
{
	protected static $entity;
	protected static $cache = [];
	protected $data = [];

	protected function __construct($data=[])
	{
		$this->data = $data;
	}

	/**
	 * Получить ИД сущности
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->data['ID'];
	}

	/**
	 * Получить "Наименование" сущности
	 *
	 * @return string
	 */
	public function getName()
	{
		if (isset($this->data['NAME']))
			return $this->data['NAME'];
		else if (isset($this->data['TITLE']))
			return $this->data['TITLE'];

		return '';
	}

	/**
	 * Получить Поле сущности
	 *
	 * @return string
	 */
	public function getField($field='')
	{
		return $this->data[$field];
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
		return (bool) !empty($this->data['ID']);
	}

	/**
	 * Удалить сущность
	 *
	 * @return bool
	 */
	public function remove()
	{
		if ($this->data['ID'] > 0)
		{
			$data = Client::getInstance()->query(static::$entity.'.delete', [
				'ID' => $this->data['ID']
			]);
			return (bool) $data['result'];
		}
	}

	/**
	 * Получить значение свойства
	 *
	 * @param int $propertyId
	 * @return array
	 */
	public function getPropertyValue($propertyId=0)
	{
		$property = 'PROPERTY_'.$propertyId;
		if (!empty($this->data[$property]))
		{
			return $this->data[$property];
		}

		return [];
	}

	/**
	 * Установить значение свойства
	 *
	 * @param int $propertyId
	 * @param string $value
	 */
	public function setPropertyValue($propertyId=0, $value='')
	{
		$property = 'PROPERTY_'.$propertyId;
		if (!empty($this->data[$property]))
		{
			if (!empty($this->data[$property]['valueId']))
				$valueId = $this->data[$property]['valueId'];
			else
				$valueId = 0;

			$entity = static::update($this->data['ID'], [
				'fields' => [
					$property => [
						'valueId' => $valueId,
						'value' => $value,
					]
				]
			]);

			// Обновляем поля сущности
			$this->data = $entity->getData();
		}
	}

	/**
	 * Получить сущность по ID
	 *
	 * @param int $id
	 * @return static
	 */
	public static function getById($id=0)
	{
		$data = Client::getInstance()->query(static::$entity.'.get', [
			'ID' => $id
		]);
		$entity = new static($data['result']);

		return $entity;
	}

	/**
	 * Получить сущность по параметрам
	 *
	 * @param array $params
	 * @return static
	 */
	public static function getByParams($params=[])
	{
		$data = Client::getInstance()->query(static::$entity.'.list', $params);
		$entity = new static($data['result'][0]);

		return $entity;
	}

	/**
	 * Получить список сущностей по параметрам
	 *
	 * @param array $params
	 * @return static[]
	 */
	public static function getListByParams($params=[])
	{
		$arEntity = [];
		$data = Client::getInstance()->query(static::$entity.'.list', $params);
		foreach ($data['result'] as $result)
		{
			$arEntity[] = new static($result);
		}

		return $arEntity;
	}

	/**
	 * Создать сущность
	 *
	 * @param array $params
	 * @return static
	 */
	public static function add($params=[])
	{
		$data = Client::getInstance()->query(static::$entity.'.add', $params);
		if ($data['result'] > 0)
		{
			return static::getById($data['result']);
		}

		return false;
	}

	/**
	 * Обновить сущность
	 *
	 * @param int $id
	 * @param array $params
	 * @return static
	 */
	public static function update($id=0, $params=[])
	{
		$params['ID'] = $id;
		$data = Client::getInstance()->query(static::$entity.'.update', $params);
		if ($data['result'])
		{
			return static::getById($id);
		}

		return false;
	}

}