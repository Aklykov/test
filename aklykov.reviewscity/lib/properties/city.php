<?php

namespace Aklykov\Reviewscity\Properties;

use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\PropertyTable;

Loc::loadMessages(__FILE__);

class City
{
	const VALUE_YES = '1';
	const VALUE_NO = '0';
	const VALUE_LENGTH = 1;
	const USER_TYPE = 'AKLYKOV.REVIEWCITY.CITY';

	/**
	 * Функция возвращает массив описывающий поведение пользовательского свойства
	 *
	 * @return array - массив описывающий поведение пользовательского свойства
	 */
	function GetUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => PropertyTable::TYPE_NUMBER,
			'USER_TYPE' => static::USER_TYPE,
			'DESCRIPTION' => Loc::getMessage('AKLYKOV_REVIEWCITY_USER_TYPE_DESCRIPTION'),
			'GetAdminListViewHTML' => array(static::class, 'GetAdminListViewHtml'),
			'GetPropertyFieldHtml' => array(static::class, 'GetPropertyFieldHtml'),
		);
	}

	/**
	 * Показ в списке на странице списка элементов ИБ
	 *
	 * @param array $arProperty - массив описывающий свойство
	 * @param array $value - массив описывающий значение свойства
	 * @param array $strHTMLControlName - массив описывающий html-поле свойства
	 * @return string - html
	 */
	function GetAdminListViewHtml($arProperty, $value, $strHTMLControlName)
	{
		return !empty($value['VALUE']) ? $value['VALUE'] : '';
	}

	/**
	 * Отображение в форме редактирования
	 *
	 * @param array $arProperty - массив описывающий свойство
	 * @param array $value - массив описывающий значение свойства
	 * @param array $strHTMLControlName - массив описывающий html-поле свойства
	 * @return string - html
	 */
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$name = $strHTMLControlName['VALUE'];

		$html = '';
		$html .= '<select name="" id="ReviewsCityListIblock"></select>';
		$html .= '&nbsp;&nbsp;&nbsp;';
		$html .= '<select name="'.$name.'" id="ReviewsCityListSection" data-value="'.$value['VALUE'].'"></select>';

		\CJSCore::Init(array('jquery'));
		// не стал уже замарачиваться с проверкой где лежит модуль
		Asset::getInstance()->addJs('/bitrix/modules/aklykov.reviewscity/js/properties_city.js', true);

		return $html;
	}
}

?>


