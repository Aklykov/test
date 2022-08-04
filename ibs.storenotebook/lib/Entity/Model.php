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
 * Class ModelTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(50) mandatory
 * <li> MANUFACTURER_ID int mandatory
 * </ul>
 *
 * @package \Ibs\StoreNotebook\Entity
 **/
class ModelTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ibs_storenotebook_model';
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
			),
			'MANUFACTURER_ID' => new IntegerField(
				'MANUFACTURER_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_MANUFACTURER_FIELD_MANUFACTURER_ID')
				]
			),
			'MANUFACTURER' => new Reference(
				'MANUFACTURER',
				'Ibs\StoreNotebook\Entity\Manufacturer',
				['=this.MANUFACTURER_ID' => 'ref.ID'],
				['join_type' => 'LEFT']
			),
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