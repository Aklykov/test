<?php
namespace Ibs\StoreNotebook\Entity;

use Bitrix\Main;
use Bitrix\Main\{
	Localization\Loc,
	Type\DateTime};
use Bitrix\Main\ORM\Fields\{
	DatetimeField,
	IntegerField,
	ScalarField,
	StringField,
	Relations\Reference,
	Validators\LengthValidator};
use Bitrix\Main\ORM\Data\DataManager;

Loc::loadMessages(__FILE__);

/**
 * Class ManufacturerTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(50) mandatory
 * </ul>
 *
 * @package \Ibs\StoreNotebook\Entity
 **/
class ManufacturerTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ibs_storenotebook_manufacturer';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'ID' => new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_MANUFACTURER_FIELD_ID')
				]
			),
			'NAME' => new StringField(
				'NAME',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateName'],
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_MANUFACTURER_FIELD_NAME')
				]
			)
		];
	}

	/**
	 * Returns validators for NAME field.
	 *
	 * @return array
	 */
	public static function validateName()
	{
		return [
			new LengthValidator(null, 50),
		];
	}
}