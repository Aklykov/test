<?php
namespace Afonya\Lognews;

use Afonya\Lognews\Entity\LogTable;
use Bitrix\Main\{SiteTable, UserTable, Mail\Event, Type\DateTime, Config\Option};

class Report
{
	public static function send()
	{
		$fio = '';
		$dirUser = []; // userId => count
		$dirEvents = [
			LogTable::EVENT_TYPE__ADD => [],
			LogTable::EVENT_TYPE__UPDATE => [],
			LogTable::EVENT_TYPE__DELETE => [],
		];

		$dateInsert = DateTime::createFromTimestamp(time());

		$rsLog = LogTable::getList([
			'select' => ['ID', 'USER_ID', 'NEWS_ID', 'EVENT_TYPE'],
			'filter' => [
				'<=DATE_INSERT' => $dateInsert
			]
		]);
		while ($arLog = $rsLog->fetch()) {
			// считаем кол-во уникальных событий на новость
			if (!in_array($arLog['NEWS_ID'], $dirEvents[$arLog['EVENT_TYPE']]))
				$dirEvents[$arLog['EVENT_TYPE']][] = $arLog['NEWS_ID'];

			// считаем кто из юзеров чаще действовал
			$dirUser[$arLog['USER_ID']] = !isset($dirUser[$arLog['USER_ID']]) ? 1 : ++$dirUser[$arLog['USER_ID']];

			LogTable::delete($arLog['ID']);
		}

		foreach ($dirEvents as &$event)
			$event = count($event);

		// определяем ФИО
		$userId = array_search(max($dirUser), $dirUser);
		if ($userId > 0) {
			$arUser = UserTable::getList([
				'select' => ['NAME', 'SECOND_NAME', 'LAST_NAME'],
				'filter' => ['=ID' => $userId],
				'limit' => 1,
			])->fetch();
			$fio = implode(' ', [
				$arUser['LAST_NAME'],
				$arUser['SECOND_NAME'],
				$arUser['NAME'],
			]);
		}

		// готовим письмо
		$html = '';
		if ($dirEvents[LogTable::EVENT_TYPE__ADD] > 0)
			$html .= 'добавлено: '.$dirEvents[LogTable::EVENT_TYPE__ADD] . '<br>';
		if ($dirEvents[LogTable::EVENT_TYPE__UPDATE] > 0)
			$html .= 'отредактировано: '.$dirEvents[LogTable::EVENT_TYPE__UPDATE] . '<br>';
		if ($dirEvents[LogTable::EVENT_TYPE__DELETE] > 0)
			$html .= 'удалено: '.$dirEvents[LogTable::EVENT_TYPE__DELETE] . '<br>';
		if (!empty($fio))
			$html .= 'главный активист: '.$fio . '<br>';

		// шлем письмо
		$sid = SiteTable::getList([
			'select' => ['LID'],
			'filter' => ['DEF' => 'Y'],
		])->fetch()['LID'];

		Event::send([
			'EVENT_NAME' => 'AFONYA_LOGNEWS_REPORT',
			'LID' => $sid,
			'C_FIELDS' => [
				'REPORT' => $html
			],
		]);

		return __METHOD__.'();';
	}
}