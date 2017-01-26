<?php
namespace Salerman\Kcm;

/**
 * Класс для проверки доступов
 *
 * Class Access
 * @package Salerman\Kcm
 */
class Access
{

	private static $instance = null;

	private function __construct()
	{

	}

	/**
	 * Получить сущность Access
	 *
	 * @return Access
	 */
	static public function getInstance()
	{
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Проверка прав на документ
	 *
	 * @param int $documentId (БитриксИД)
	 * @return bool
	 */
	public function checkRigth__Document($documentId=0)
	{
		$documentId = (int) $documentId;
		if( $documentId > 0 ) {
			global $USER;
			if( $USER->isAdmin() ) return true;
			\Bitrix\Main\Loader::includeModule('iblock');
			$rsDocument = DocumentTable::getById($documentId);
			if( $arDocument = $rsDocument->Fetch() ) {
				$companyId = $arDocument['COMPANY_ID'];
				return self::checkRigth__Company($companyId);
			}
		}
		return false;
	}

	/**
	 * Проверка прав на компанию
	 *
	 * @param int $companyId (БитриксИД)
	 * @return bool
	 */
	public function checkRigth__Company($companyId=0)
	{
		$companyId = (int) $companyId;
		global $USER;
		$userId = $USER->GetID();
		if( $USER->isAdmin() ) return true;
		if( $companyId > 0 ) {
			\Bitrix\Main\Loader::includeModule('iblock');
			$arSelect = array('ID');
			$arFilter = array(
				'IBLOCK_ID' => IBLOCK_ID__COMPANY,
				'ID' => $companyId,
				'ACTIVE' => 'Y',
				array(
					'LOGIC' => 'OR',
					'PROPERTY_MANAGER' => $userId,
					'PROPERTY_CLIENT' => $userId
				)
			);
			$arNav = array('nTopCount' => 1);
			$count = \CIBlockElement::GetList( array(), $arFilter, array(), $arNav, $arSelect );
			return ($count > 0);
		}
		return false;
	}

	/**
	 * Проверка прав на выдачу файла
	 *
	 * @param $ENTITY - сущность
	 * @param $FC_CONTRACT - договор
	 * @param $FC_BP - контрагент
	 * @param $NUM - документ
	 * @return bool
	 */
	private function checkRigth__FILE($ENTITY='', $FC_CONTRACT='', $FC_BP='', $NUM='')
	{
		global $USER;
		if( $USER->isAdmin() ) return true;
		if( !empty($ENTITY) && !empty($FC_CONTRACT) && !empty($FC_BP) && !empty($NUM) ) {
			switch ($ENTITY) {
				case 'supply':
					$arElement = SupplyTable::getByInfo($FC_CONTRACT, $FC_BP, $NUM);
					break;
				case 'release':
					$arElement = ReleaseTable::getByInfo($FC_CONTRACT, $FC_BP, $NUM);
					break;
				case 'shipment':
					$arElement = ShipmentTable::getByInfo($FC_CONTRACT, $FC_BP, $NUM);
					break;
				case 'report':
					$arElement = ReportTable::getByInfo($FC_CONTRACT, $FC_BP, $NUM);
					break;
				default:
					return false;
			}
			// Проверяем что есть права и на договор и на компанию
			$arCompany = Company::getMyCompany();
			$documentId = DocumentTable::getIdByDocIdAndCompany($arElement['DOC_ID'], $arCompany['ID']);
			$companyId = $arCompany['ID'];
			if( $this->checkRigth__Document($documentId) && $this->checkRigth__Company($companyId) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Проверка доступа к элементу таблицы "Поставка"
	 *
	 * @param int $supplyId
	 * @return bool
	 */
	public function checkSupplyById($supplyId=0)
	{
		$supplyId = (int) $supplyId;
		if( $supplyId > 0 ) {
			$rsSupply = SupplyTable::getById($supplyId);
			if( $arSupply = $rsSupply->Fetch() ) {
				$arCompany = Company::getMyCompany();
				$documentId = DocumentTable::getIdByDocIdAndCompany($arSupply['DOC_ID'], $arCompany['ID']);
				return self::checkRigth__Document($documentId);
			}
		}
		return false;
	}

	/**
	 * Проверка доступа к элементу таблицы "Выпуск"
	 *
	 * @param int $releaseId
	 * @return bool
	 */
	public function checkReleaseById($releaseId=0)
	{
		$releaseId = (int) $releaseId;
		if( $releaseId > 0 ) {
			$rsRelease = ReleaseTable::getById($releaseId);
			if( $arRelease = $rsRelease->Fetch() ) {
				$arCompany = Company::getMyCompany();
				$documentId = DocumentTable::getIdByDocIdAndCompany($arRelease['DOC_ID'], $arCompany['ID']);
				return self::checkRigth__Document($documentId);
			}
		}
		return false;
	}

	/**
	 * Проверка доступа к элементу таблицы "Отгрузка"
	 *
	 * @param int $shipmentId
	 * @return bool
	 */
	public function checkShipmentById($shipmentId=0)
	{
		$shipmentId = (int) $shipmentId;
		if( $shipmentId > 0 ) {
			$rsShipment = ShipmentTable::getById($shipmentId);
			if( $arShipment = $rsShipment->Fetch() ) {
				$arCompany = Company::getMyCompany();
				$documentId = DocumentTable::getIdByDocIdAndCompany($arShipment['DOC_ID'], $arCompany['ID']);
				return self::checkRigth__Document($documentId);
			}
		}
		return false;
	}

	/**
	 * Проверка прав на выдачу JSON
	 *
	 * @param $ENTITY - сущность
	 * @param $FC_CONTRACT - договор
	 * @param $FC_BP - контрагент
	 * @param $NUM - документ
	 * @return bool
	 */
	public function checkRigth__JSON($ENTITY='', $FC_CONTRACT='', $FC_BP='', $NUM='')
	{
		return $this->checkRigth__FILE($ENTITY, $FC_CONTRACT, $FC_BP, $NUM);
	}

	/**
	 * Проверка прав на выдачу PDF
	 *
	 * @param $ENTITY - сущность
	 * @param $FC_CONTRACT - договор
	 * @param $FC_BP - контрагент
	 * @param $NUM - документ
	 * @return bool
	 */
	public function checkRigth__PDF($ENTITY='', $FC_CONTRACT='', $FC_BP='', $NUM='')
	{
		return $this->checkRigth__FILE($ENTITY, $FC_CONTRACT, $FC_BP, $NUM);
	}

}
