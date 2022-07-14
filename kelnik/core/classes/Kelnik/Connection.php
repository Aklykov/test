<?php

namespace Kelnik;

/**
 * Класс для инициализации БД
 *
 * Class Connection
 * @package Kelnik
 */
class Connection
{
	private static $instance = false;
	private \PDO $pdo;

	private function __construct()
	{
		try {
			$this->pdo = new \PDO(
				'mysql:dbname='.DB_NAME.';host='.DB_HOST,
				DB_USER,
				DB_PASSWORD
			);
		} catch (\PDOException $exception) {
			echo $exception->getMessage();
			die();
		}
	}

	/**
	 * @return self
	 */
	public static function getInstance() :self
	{
		if (static::$instance === false)
			static::$instance = new self();

		return static::$instance;
	}

	public function getPDO() :\PDO
	{
		return $this->pdo;
	}
}