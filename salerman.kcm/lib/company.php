<?php
namespace Salerman\Kcm;

class Company {

	public $id;
	public $name;
	public $manager;
	public $users;
	public $fc_bp;

	const ADMIN_DETAIL_URL = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=1&type=company&ID=';

	/**
	 * Получить список компаний (в виде выпадающего списка) текущего пользователя
	 *
	 * @return array
	 */
	static public function getSelectMyCompany() {
		global $USER;
		$arListCompany = array();
		\Bitrix\Main\Loader::includeModule('iblock');
		$arSelect = array('ID', 'NAME');
		$arFilter = array(
			'IBLOCK_ID' => IBLOCK_ID__COMPANY,
			'ACTIVE' => 'Y',
			'PROPERTY_MANAGER' => $USER->GetID()
		);
		if( 1 || $USER->isAdmin() ) {
			unset($arFilter['PROPERTY_MANAGER']);
		}
		$res = \CIBlockElement::GetList( array('NAME' => 'ASC'), $arFilter, false, false, $arSelect );
		while( $ob = $res->GetNextElement() ) {
			$arFields = $ob->GetFields();
			$arListCompany[$arFields['ID']] = $arFields['~NAME'];
		}
		return $arListCompany;
	}

	/**
	 * Существует ли компания с внешним ИД $FC_BP
	 *
	 * @param string $docId
	 * @return bool
	 */
	static public function isExistFcBp($FC_BP='')
	{
		if( !empty($FC_BP) ) {
			\Bitrix\Main\Loader::includeModule('iblock');
			$arSelect = array('ID');
			$arFilter = array(
				'IBLOCK_ID' => IBLOCK_ID__COMPANY,
				'ACTIVE' => 'Y',
				'PROPERTY_FC_BP' => $FC_BP
			);
			$arNav = array('nTopCount' => 1);
			$count = \CIBlockElement::GetList( array(), $arFilter, array(), $arNav, $arSelect );
			return ($count > 0);
		}
		return false;
	}

	/**
	 * Получить ИД компании по Внешнему ИД
	 *
	 * @param $FcBp
	 * @return int
	 */
	static public function getIdByFcBp($FcBp)
	{
		if( empty($FcBp) ) {
			return false;
		}
		\Bitrix\Main\Loader::includeModule('iblock');
		$arSelect = array('ID');
		$arFilter = array(
			'IBLOCK_ID' => IBLOCK_ID__COMPANY,
			'ACTIVE' => 'Y',
			'PROPERTY_FC_BP' => $FcBp
		);
		$arNav = array('nTopCount' => 1);
		$res = \CIBlockElement::GetList( array(), $arFilter, false, $arNav, $arSelect );
		if( $ob = $res->GetNextElement() ) {
			$arFields = $ob->GetFields();
			return $arFields['ID'];
		}
	}

	/**
	 * Получить HTML-ссылку на деталку документа
	 *
	 * @param string $docId
	 * @return string
	 */
	static public function getHtmlLinkDetailById($id='')
	{
		$linkDetail = '';
		if( !empty($id) ) {
			\Bitrix\Main\Loader::includeModule('iblock');
			$arSelect = array('ID', 'NAME');
			$arFilter = array(
				'IBLOCK_ID' => IBLOCK_ID__COMPANY,
				'ACTIVE' => 'Y',
				'ID' => $id
			);
			$arNav = array('nTopCount' => 1);
			$res = \CIBlockElement::GetList( array(), $arFilter, false, $arNav, $arSelect );
			if( $ob = $res->GetNextElement() ) {
				$arFields = $ob->GetFields();
				$urlDetail = self::ADMIN_DETAIL_URL . $arFields['ID'];
				$linkDetail = '<a href="'.$urlDetail.'" title="title">'.$arFields['NAME'].'</a>';
			}
		}
		return $linkDetail;
	}

	/**
	 * Получить компанию по КлиентИД
	 *
	 * @param int $clientId
	 * @return array
	 */
	static public function getCompanyByClientID($clientId=0)
	{
		$arCompany = array();
		if( $clientId > 0 ) {
			\Bitrix\Main\Loader::includeModule('iblock');
			$arSelect = array('ID', 'NAME', 'PROPERTY_FC_BP');
			$arFilter = array(
				'IBLOCK_ID' => IBLOCK_ID__COMPANY,
				'ACTIVE' => 'Y',
				'PROPERTY_CLIENT' => $clientId
			);
			$arNav = array('nTopCount' => 1);
			$res = \CIBlockElement::GetList( array(), $arFilter, false, $arNav, $arSelect );
			if( $ob = $res->GetNextElement() ) {
				$arCompany = $ob->GetFields();
			}
		}
		return $arCompany;
	}

	/**
	 * Получить ИД моей компании (текущего клиента)
	 *
	 * @return array
	 */
	static public function getMyCompany()
	{
		global $USER;
		return self::getCompanyByClientID($USER->GetID());
	}

	/**
	 * Получение менеджеров по ИД компании
	 *
	 * @param int $companyId
	 * @return array
	 */
	static public function getManagersById($companyId=0)
	{
		$arManagers = array();
		if( $companyId > 0 ) {
			\Bitrix\Main\Loader::includeModule('iblock');
			$db_props = \CIBlockElement::GetProperty(
				IBLOCK_ID__COMPANY,
				$companyId,
				array('sort' => 'asc'),
				array(CODE => 'MANAGER')
			);
			while( $ar_props = $db_props->Fetch() ) {
				$arManagers[] = $ar_props["VALUE"];
			}
		}
		return $arManagers;
	}

}