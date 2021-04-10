<?php
namespace Aklykov\Currencyrate;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\DatetimeField,
	Bitrix\Main\ORM\Fields\FloatField,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class CurrencyrateTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> CODE string(10) optional
 * <li> DATE_CREATE datetime optional
 * <li> COURSE double optional
 * </ul>
 *
 * @package Bitrix\Currencyrate
 **/

class CurrencyrateTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'aklykov_currencyrate';
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
					'title' => Loc::getMessage('CURRENCYRATE_ENTITY_ID_FIELD')
				]
			),
			new StringField(
				'CODE',
				[
					'validation' => [__CLASS__, 'validateCode'],
					'title' => Loc::getMessage('CURRENCYRATE_ENTITY_CODE_FIELD')
				]
			),
			new DatetimeField(
				'DATE_CREATE',
				[
					'title' => Loc::getMessage('CURRENCYRATE_ENTITY_DATE_CREATE_FIELD')
				]
			),
			new FloatField(
				'COURSE',
				[
					'title' => Loc::getMessage('CURRENCYRATE_ENTITY_COURSE_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for CODE field.
	 *
	 * @return array
	 */
	public static function validateCode()
	{
		return [
			new LengthValidator(null, 10),
		];
	}
}