<?php

namespace Kelnik\Ajax;
use Kelnik\Entity\QuestBook as EntityQuestBook;

/**
 * Класс реализующий обработчики для ajax-запросов по сущности QuestBook
 *
 * Class QuestBook
 * @package Kelnik\Ajax
 */
class QuestBook extends Request
{
	/**
	 * Получить N последних записей
	 *
	 * @throws \Exception
	 */
	public static function getLastRecords() :void
	{
		$get = static::getQuery();
		$limit = (int) $get['limit'];

		$items = EntityQuestBook::getList(
			['dtime', 'name', 'body'],
			['dtime' => 'desc'],
			$limit
		);

		foreach ($items as &$item) {
			$item['name'] = htmlspecialchars($item['name']);
			$item['body'] = nl2br(htmlspecialchars($item['body']));
		}

		static::showJson(true, '', ['items' => $items]);
	}

	/**
	 * Создать запись
	 *
	 * @throws \Exception
	 */
	public static function addRecords() :void
	{
		$post = static::getPost();

		$errors = [];
		if (empty($post['name'])) {
			$errors[] = 'Заполните поле "Имя"';
		}
		if (empty($post['body'])) {
			$errors[] = 'Заполните поле "Сообщение"';
		}
		if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Заполните поле "E-mail"';
		}

		if (!empty($errors)) {
			static::showJson(false, implode("\n", $errors));
		}

		$data = [
			'dtime' => date('Y-m-d H:i:s'),
			'name' => $post['name'],
			'email' => $post['email'],
			'body' => $post['body'],
		];
		if (EntityQuestBook::add($data)) {
			static::showJson(true);
		} else {
			static::showJson(false, 'Произошла ошибка! Попробуйте еще раз!');
		}
	}
}