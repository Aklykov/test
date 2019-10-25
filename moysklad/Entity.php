<?php

namespace Zografdd\Moysklad;

/**
 * Класс для работы с абстрактной сущностью
 *
 * Class Entity
 * @package Zografdd\Moysklad
 */
abstract class Entity
{
	protected static $entity;
	protected static $defaultId = false;
	protected static $cache = [];
	protected static $deletedIds = [];
	protected $data = [];

	protected function __construct($data=[])
	{
		$this->data = $data;
	}

	/**
	 * Загрузить мета-данные по названию
	 *
	 * @param string $name
	 */
	public function loadMetaByName($name='')
	{
		if (!empty($this->data[$name][0]) && empty($this->data[$name]['data'])) // массив сущностей
		{
			foreach ($this->data[$name] as $k => $entity)
			{
				if (!empty($entity['meta']['href']))
				{
					$this->data[$name][$k]['data'] = Client::getInstance()->get($entity['meta']['href']);
				}
			}
		}
		else if (empty($this->data[$name]['data']) && !empty($this->data[$name]['meta']['href'])) // одна сущность
		{
			$this->data[$name]['data'] = Client::getInstance()->get($this->data[$name]['meta']['href']);
		}
	}

	/**
	 * Получить ИД сущности
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->data['id'];
	}

	/**
	 * Получить Мета-описание сущности
	 *
	 * @return array
	 */
	public function getMeta()
	{
		return ['meta' => $this->data['meta']];
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
		return (bool) !empty($this->data['id']);
	}

	/**
	 * Удалить сущность
	 *
	 * @return mixed|void
	 */
	public function remove()
	{
		if (empty($this->data['id']))
			return;

		static::$deletedIds = [];
		return static::removeRecursivelyByUrl(static::$entity.'/'.$this->data['id']);
	}

	/**
	 * Рекурсивное удаление сущностей (всех связанных с ней) по URL
	 *
	 * @param string $url
	 * @return mixed
	 */
	static public function removeRecursivelyByUrl($url='')
	{
		$result = Client::getInstance()->delete($url);
		$error = $result['errors'][0];
		if ($error['code'] > 0 && in_array($error['code'], [1028, 1023]))
		{
			foreach ($error['dependencies'] as $dependency)
			{
				$id = end(explode('/', $dependency['href']));
				if (in_array($id, static::$deletedIds) == false)
				{
					$result = static::removeRecursivelyByUrl($dependency['href']);
					if (empty($result['errors']))
					{
						static::$deletedIds[] = $id;
						$result = static::removeRecursivelyByUrl($url);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Получить сущность по ID
	 *
	 * @param string $id
	 * @return static
	 */
	static public function getById($id='')
	{
		$data = Client::getInstance()->get(static::$entity.'/'.$id);
		$entity = new static($data);
		$entity->loadMetaByName('positions');

		return $entity;
	}

	/**
	 * Получить сущность по NAME
	 *
	 * @param string $name
	 * @return static
	 */
	static public function getByName($name='')
	{
		$data = Client::getInstance()->get(static::$entity.'/?filter=name='.$name.'&limit=1');
		$data = $data['rows'][0];

		$entity = new static($data);
		$entity->loadMetaByName('positions');

		return $entity;
	}

	/**
	 * Получить сущность по XML_ID
	 *
	 * @param string $xmlId
	 * @return static
	 */
	static public function getByExternalCode($externalCode='')
	{
		$data = Client::getInstance()->get(static::$entity.'/?filter=externalCode='.$externalCode.'&limit=1');
		$data = $data['rows'][0];

		$entity = new static($data);
		$entity->loadMetaByName('positions');

		return $entity;
	}

	/**
	 * Получить сущность по-умолчанию (если задано)
	 *
	 * @return static
	 */
	static public function getDefaultEntity()
	{
		if (static::$defaultId)
			return static::getById(static::$defaultId);

		return false;
	}

	/**
	 * Создать сущность
	 *
	 * @param array $params
	 * @return static
	 */
	static public function add($params)
	{
		$data = Client::getInstance()->post(static::$entity, $params);
		$entity = new static($data);

		return $entity;
	}

	/**
	 * Обновить сущность
	 *
	 * @param string $id
	 * @param array $params
	 * @return static
	 */
	static public function update($id, $params)
	{
		$data = Client::getInstance()->put(static::$entity.'/'.$id, $params);
		$entity = new static($data);

		return $entity;
	}

}