<?php

namespace Salerman\Kcm;

/**
 * Class AgentReport
 * @package Salerman\Kcm
 */

class AgentReport {

	public $id;
	public $doc_id;
	public $date;
	public $file;

	/**
	 * @const int
	 */
	const HLBLOCK_ID = 1;

	/**
	 * Создать Отчет
	 *
	 * @return AgentReport
	 */
	public function Add() {
		\Bitrix\Main\Loader::includeModule('highloadblock');
		$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID' => self::HLBLOCK_ID)));
		$arData = $rsData->fetch();
		$LANG_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
		$LANG_entity_data_class = $LANG_entity->getDataClass();
		$arBxData = array(
			"UF_DOC_ID" => (int) $this->doc_id,
			"UF_DATE" => self::TimeToTimeObject($this->date),
			"UF_FILE" => \CFile::MakeFileArray($this->file);
		);
		$result = $LANG_entity_data_class::add($this->id, $arBxData);
		if (!$result->isSuccess()) {
			echo $result->getErrorMessages();
		} else {
			$this->id = $result->getId();
			return $this;
		}
	}

	/**
	 * Обновить Отчет
	 *
	 * @return AgentReport
	 */
	public function Update() {
		if( !empty($this->id) ) {
			\Bitrix\Main\Loader::includeModule('highloadblock');
			$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID' => self::HLBLOCK_ID)));
			$arData = $rsData->fetch();
			$LANG_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
			$LANG_entity_data_class = $LANG_entity->getDataClass();
			$arBxData = array(
				"UF_DOC_ID" => $this->doc_id,
				"UF_DATE" => self::TimeToTimeObject($this->date),
				"UF_FILE" => \CFile::MakeFileArray($this->file);
			);
			$result = $LANG_entity_data_class::update($this->id, $arBxData);
			if (!$result->isSuccess()) {
				echo $result->getErrorMessages();
			} else {
				return $this;
			}
		}
	}

	/**
	 * Получить список отчетов
	 *
	 * @param array $arFilter
	 * @param array $arOrder
	 * @param int $limit
	 * @param int $offset
	 * @return AgentReport[]
	 */
	static public function GetList($arFilter=array(), $arOrder=array('UF_DATE' => 'DESC'), $limit=0, $offset=0) {
		\Bitrix\Main\Loader::includeModule('highloadblock');
		$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID' => self::HLBLOCK_ID)));
		$arData = $rsData->fetch();
		$LANG_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
		$main_query = new \Bitrix\Main\Entity\Query($LANG_entity);
		$main_query->setSelect(array('*'));
		$main_query->setFilter($arFilter);
		$main_query->setOrder($arOrder);
		$result = $main_query->exec();
		$result = new \CDBResult($result);
		$arObj = array();
		while ($row = $result->Fetch()) {
			$obj = new self();
			$obj->id = (int) $row['ID'];
			$obj->doc_id = (int) $row['UF_DOC_ID'];
			$obj->date = self::TimeObjectToTime($row['UF_DATE']);
			$obj->file = \CFile::GetPath($row['UF_FILE']);
			$arObj[] = $obj;
		}
		return $arObj;
	}

	/**
	 * Получить отчет по ИД
	 *
	 * @param $id
	 * @return bool|mixed
	 */
	static public function GetById( $id ) {
		if( !empty($id) ) {
			$arFilter = array('ID' => $id);
			$arObj = self::GetList($arFilter);
			return current($arObj);
		}
		return false;
	}

	/**
	 * Получение объекта Time из времени
	 *
	 * @param $time
	 * @return mixed
	 */
	public static function TimeToTimeObject($time){
		if(is_string($time)){
			$time = str_replace(".", "-", $time);
			$time = str_replace(" ", "T", $time);
			if(strlen($time) == 16){
				$time .= ":00";
			}
			$ctimeNew = strtotime($time);
			$timeObjNew = \Bitrix\Main\Type\DateTime::createFromTimestamp($ctimeNew);
			return $timeObjNew;
		}else{
			return $time;
		}
	}

	/**
	 * Получение даты из объекта Time
	 *
	 * @param $timeObjNew
	 * @return mixed
	 */
	public static function TimeObjectToTime($timeObjNew){
		if(get_class($timeObjNew) == 'Bitrix\Main\Type\DateTime' || get_class($timeObjNew) == 'Bitrix\Main\Type\Date'){
			$phpDate = \Bitrix\Main\Type\DateTime::convertFormatToPhp($timeObjNew);
			return $phpDate;
		}else{
			return $timeObjNew;
		}
	}

}