<?php
namespace Afonya\Lognews\Entity;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\DatetimeField,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class LogTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NEWS_ID int mandatory
 * <li> USER_ID int mandatory
 * <li> EVENT_TYPE string(6) mandatory
 * <li> DATE_INSERT datetime mandatory
 * </ul>
 *
 * @package \Afonya\Lognews\Entity
 **/

class LogTable extends Main\Entity\DataManager
{
	const EVENT_TYPE__ADD = 'ADD';
	const EVENT_TYPE__UPDATE = 'UPDATE';
	const EVENT_TYPE__DELETE = 'DELETE';

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'afonya_lognews_entity_log';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('AFONYA_LOGNEWS_LOG_ENTITY_FIELD_ID')
				]
			),
			new IntegerField(
				'NEWS_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('AFONYA_LOGNEWS_LOG_ENTITY_FIELD_NEWS_ID')
				]
			),
			new IntegerField(
				'USER_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('AFONYA_LOGNEWS_LOG_ENTITY_FIELD_USER_ID')
				]
			),
			new StringField(
				'EVENT_TYPE',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateEventType'],
					'title' => Loc::getMessage('AFONYA_LOGNEWS_LOG_ENTITY_FIELD_EVENT_TYPE')
				]
			),
			new DatetimeField(
				'DATE_INSERT',
				[
					'required' => true,
					'title' => Loc::getMessage('AFONYA_LOGNEWS_LOG_ENTITY_FIELD_DATE_INSERT')
				]
			),
		];
	}

	/**
	 * Returns validators for EVENT_TYPE field.
	 *
	 * @return array
	 */
	public static function validateEventType()
	{
		return [
			new LengthValidator(null, 6),
		];
	}

	/**
	 * Создать запись о создании новости
	 *
	 * @param int $newsId
	 */
	public static function createEventAdd(int $newsId)
	{
		static::createEvent($newsId, static::EVENT_TYPE__ADD);
	}

	/**
	 * Создать запись об обновлении новости
	 *
	 * @param int $newsId
	 */
	public static function createEventUpdate(int $newsId)
	{
		static::createEvent($newsId, static::EVENT_TYPE__UPDATE);
	}

	/**
	 * Создать запись об удалении новости
	 *
	 * @param int $newsId
	 */
	public static function createEventDelete(int $newsId)
	{
		static::createEvent($newsId, static::EVENT_TYPE__DELETE);
	}

	/**
	 * @param int $newsId
	 * @param string $type
	 * @throws Main\ArgumentException
	 * @throws Main\ObjectPropertyException
	 * @throws Main\SystemException
	 */
	public static function createEvent(int $newsId, string $type)
	{
		global $USER;
		$userId = $USER->GetID();

		$dateInsert = DateTime::createFromTimestamp(time());

		$isExist = static::getList([
			'select' => ['ID'],
			'filter' => [
				'=NEWS_ID' => $newsId,
				'=USER_ID' => $userId,
				'=EVENT_TYPE' => $type,
			],
			'limit' => 1
		])->getSelectedRowsCount();
		if (!$isExist) {
			static::add([
				'NEWS_ID' => $newsId,
				'USER_ID' => $userId,
				'DATE_INSERT' => $dateInsert,
				'EVENT_TYPE' => $type,
			]);
		}
	}

}