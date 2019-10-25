<?php

namespace AklykovAO\Bitrix24\Handlers;

class Oncrm
{
	protected static $appDomain = 'zografdd.bitrix24.ru';
	protected static $appToken; // у каждого дочернего класса свой Токен

	/**
	 * Метод-контроллер, определяющий какой обработчик запустить
	 * Переопределяется в дочерних классах на логику обработчика
	 */
	public static function execute()
	{
		$post = $_POST;

		if (static::checkPost($post) === false)
			return;

		// Подключаем класс-обработчик по коду события из Б24
		$event = ucfirst(strtolower($post['event']));
		$callClass = __NAMESPACE__ . "\\" . htmlspecialchars($event);

		if (class_exists($callClass))
			call_user_func($callClass . '::execute', $post);
	}

	/**
	 * Проверяем POST
	 *
	 * @param array $post
	 * @return bool
	 */
	private static function checkPost($post=[])
	{
		if (empty($post['event']) || empty($post['data']) || empty($post['auth']['domain']) || empty($post['auth']['application_token']))
			return false;

		return true;
	}

	/**
	 * Проверяем авторизацию
	 *
	 * @param array $post
	 * @return bool
	 */
	protected static function checkAuth($post=[])
	{
		if ($post['auth']['domain'] != static::$appDomain || $post['auth']['application_token'] != static::$appToken)
			return false;

		return true;
	}

	/**
	 * Запись в лог
	 *
	 * @param $var
	 * @param bool $clear
	 */
	protected static function writeLog($var, $clear=false)
	{
		$path = $_SERVER['SCRIPT_FILENAME'] . '_log.txt';
		$content = date('d.m.Y H:i:s') . "\n";
		$content .= print_r($var, true) . "\n";
		if ($clear)
			\file_put_contents($path, $content);
		else
			\file_put_contents($path, $content, \FILE_APPEND);
	}
}