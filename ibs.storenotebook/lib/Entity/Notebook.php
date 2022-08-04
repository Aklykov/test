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
	FloatField,
	Relations\Reference,
	Validators\LengthValidator};
use Bitrix\Main\ORM\Data\DataManager;

Loc::loadMessages(__FILE__);

/**
 * Class NotebookTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(100) mandatory
 * <li> YEAR int mandatory
 * <li> PRICE double mandatory
 * <li> MODEL_ID int mandatory
 * </ul>
 *
 * @package \Ibs\StoreNotebook\Entity
 **/
class NotebookTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ibs_storenotebook_notebook';
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
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK_FIELD_ID')
				]
			),
			'NAME' => new StringField(
				'NAME',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateName'],
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK_FIELD_NAME')
				]
			),
			'YEAR' => new IntegerField(
				'YEAR',
				[
					'required' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK_FIELD_YEAR')
				]
			),
			'PRICE' => new FloatField(
				'PRICE',
				[
					'required' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK_FIELD_PRICE')
				]
			),
			'MODEL_ID' => new IntegerField(
				'MODEL_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK_FIELD_MODEL_ID')
				]
			),
			'MODEL' => new Reference(
				'MODEL',
				'Ibs\StoreNotebook\Entity\Model',
				['=this.MODEL_ID' => 'ref.ID'],
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
			new LengthValidator(null, 100),
		];
	}
}