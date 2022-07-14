<?php

namespace Kelnik\Entity;
use \Kelnik\Connection;

/**
 * Класс для работы с таблицами
 *
 * Class Entity
 * @package Kelnik\Entity
 */
abstract class Entity
{
	protected static string $table = '';

	/**
	 * Создать новую запись в таблицу
	 *
	 * @param array $data
	 * @return bool
	 * @throws \Exception
	 */
	static public function add(array $data) :bool
	{
		if (empty(static::$table))
			throw new \Exception('Для класса '.__CLASS__.' метод '.__METHOD__.' недоступен! Унаследуйте отдельный класс и переопределите $table');

		if (empty($data))
			throw new \Exception('Заполните параметр $data');

		// подготовка данных
		$dataExecute = [];
		foreach ($data as $field => $value)
			$dataExecute[':'.$field] = $value;

		$insertFields = implode(', ', array_keys($data));
		$insertValue = implode(', ', array_keys($dataExecute));

		// запрос
		$sql = 'INSERT INTO '.static::$table.'('.$insertFields.') VALUES('.$insertValue.')';
		$sth = Connection::getInstance()->getPDO()->prepare($sql);
		$result = $sth->execute($dataExecute);

		return $result;
	}

	/**
	 * Запросить список записей из таблицы
	 *
	 * @param array $fields - список полей для выборки
	 * @param array $sorts - сортировка в виде "поле" => "тип сортировки"
	 * @param int $limit - ограничение выборки
	 * @return array
	 * @throws \Exception
	 */
	static public function getList(array $fields=[], array $sorts, int $limit) :array
	{
		if (empty(static::$table))
			throw new \Exception('Для класса '.__CLASS__.' метод '.__METHOD__.' недоступен! Унаследуйте отдельный класс и переопределите $table');

		if (empty($sorts))
			throw new \Exception('Заполните параметр $sorts');

		if (empty($limit))
			throw new \Exception('Заполните параметр $limit');

		// сортировка
		$order = '';
		array_walk($sorts, function(&$value, $key) {
			$value = $key.' '.$value;
		});
		$order = implode(', ', $sorts);

		// выборка
		$select = '*';
		if (!empty($fields))
			$select = implode(', ', $fields);

		// запрос
		$sql = 'SELECT '.$select.' FROM '.static::$table.' ORDER BY '.$order.' LIMIT '.$limit;
		$sth = Connection::getInstance()->getPDO()->prepare($sql);
		$sth->execute();
		$items = $sth->fetchAll(\PDO::FETCH_ASSOC);

		return $items;
	}
}