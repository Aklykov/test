<?php

namespace Kelnik\Ajax;

/**
 * Класс для работы с Ajax-запросами
 *
 * Class Request
 * @package Kelnik\Ajax
 */
abstract class Request
{
	/**
	 * Получить данные из GET
	 *
	 * @return array
	 */
	protected static function getQuery() :array
	{
		$get = [];
		foreach ($_GET as $key => $value) {
			$get[$key] = htmlspecialchars(trim($value));
		}

		return $get;
	}

	/**
	 * Получить данные из POST
	 *
	 * @return array
	 */
	protected static function getPost() :array
	{
		$post = [];
		foreach ($_POST as $key => $value) {
			$post[$key] = htmlspecialchars(trim($value));
		}

		return $post;
	}

	/**
	 * Ответ на ajax-запрос
	 *
	 * @param bool $result
	 * @param string $message
	 * @param array $data
	 */
	protected static function showJson(bool $result, string $message='', array $data=[]) :void
	{
		ob_end_clean();
		ob_start();

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode([
			'result' => $result,
			'message' => $message,
			'data' => $data,
		]);

		die();
	}

	/**
	 * Контроллер под ajax запросы
	 */
	public static function proccess() :void
	{
		$request = static::getPost() + static::getQuery();
		if (empty($request['action'])) {
			static::showJson(
				false,
				'Неправильно передан параметр action',
			);
		}

		list($class, $method) = explode(':', $request['action']);
		$class = __NAMESPACE__.'\\'.$class;

		if (!method_exists($class, $method)) {
			static::showJson(
				false,
				'Метод '.$class.'::'.$method.' не найден',
			);
		}

		call_user_func([
			$class,
			$method
		]);
	}
}