<?php
namespace Salerman\Kcm;

/**
 * Справочник файлов
 *
 * Class FileMap
 * @package Salerman\Kcm
 */
class FileMap
{

	public $id;
	public $suffix;
	public $name;
	public $bp;

	const HLBLOCK_ID = 1;
	const BP_1 = 1;
	const BP_2 = 2;
	const BP_3 = 3;
	const BP_4 = 4;

	/**
	 * Получить список
	 *
	 * @param array $arFilter
	 * @param array $arOrder
	 * @param int $limit
	 * @param int $offset
	 * @return FileMap[]
	 */
	public static function getList( $arFilter=array(), $arOrder=array('ID' => 'ASC'), $limit=0, $offset=0 )
	{
		\Bitrix\Main\Loader::includeModule('highloadblock');
		$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID' => self::HLBLOCK_ID)));
		$arData = $rsData->fetch();
		$LANG_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
		$main_query = new \Bitrix\Main\Entity\Query($LANG_entity);
		$main_query->setSelect( array('*') );
		$main_query->setFilter( $arFilter );
		$main_query->setOrder( $arOrder );
		$main_query->setLimit( $limit );
		$main_query->setOffset( $offset );
		$result = $main_query->exec();
		$result = new \CDBResult($result);
		$arObj = array();
		while ( $row = $result->Fetch() ) {
			$obj = new self();
			$obj->id = $row['ID'];
			$obj->suffix = $row['UF_SUFFIX'];
			$obj->name = $row['UF_NAME'];
			$obj->bp = $row['UF_BP'];
			$arObj[] = $obj;
		}
		return $arObj;
	}

	/**
	 * Получить по ИД
	 *
	 * @param int $id
	 * @return FileMap
	 */
	public static function getByID( $id )
	{
		$arFilter = array('ID' => $id);
		$arObj = self::getList( $arFilter );
		return current($arObj);
	}

	/**
	 * Получить список "Поставки"
	 *
	 * @return FileMap[]
	 */
	public static function getListBP1()
	{
		$arFilter = array('UF_BP' => self::BP_1);
		$arObj = self::getList( $arFilter );
		return $arObj;
	}

	/**
	 * Получить список "Выпуск"
	 *
	 * @return FileMap[]
	 */
	public static function getListBP2()
	{
		$arFilter = array('UF_BP' => self::BP_2);
		$arObj = self::getList( $arFilter );
		return $arObj;
	}

	/**
	 * Получить список "Отгрузка"
	 *
	 * @return FileMap[]
	 */
	public static function getListBP3()
	{
		$arFilter = array('UF_BP' => self::BP_3);
		$arObj = self::getList( $arFilter );
		return $arObj;
	}

	/**
	 * Получить список "Отчеты агентов"
	 *
	 * @return FileMap[]
	 */
	public static function getListBP4()
	{
		$arFilter = array('UF_BP' => self::BP_4);
		$arObj = self::getList( $arFilter );
		return $arObj;
	}

}