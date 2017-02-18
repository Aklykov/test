<?php

namespace Aklykov\Checkbox;

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Checkbox
 * @package Aklykov\Checkbox
 */
class Checkbox {

	const VALUE_YES = '1';
	const VALUE_NO = '0';
	const VALUE_LENGTH = 1;
	const PROPERTY_TYPE = 'S';
	const USER_TYPE = 'AKLYKOV.CHECKBOX';

	/**
	 * Функция возвращает массив описывающий поведение пользовательского свойства
	 *
	 * @return array - массив описывающий поведение пользовательского свойства
	 */
	function GetUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => self::PROPERTY_TYPE,
			'USER_TYPE' => self::USER_TYPE,
			'DESCRIPTION' => Loc::getMessage('AKLYKOV_CHECKBOX_DESCRIPTION'),
			'GetAdminListViewHTML' => array('Aklykov\Checkbox\Checkbox', 'GetAdminListViewHtml'),
			'GetPropertyFieldHtml' => array('Aklykov\Checkbox\Checkbox', 'GetPropertyFieldHtml'),
			'GetAdminFilterHTML' => array('Aklykov\Checkbox\Checkbox', 'GetAdminFilterHtml'),
			'GetLength' => array('Aklykov\Checkbox\Checkbox', 'GetLength')
		);
	}

	/**
	 * Показ в фильтре на странице списка элементов ИБ
	 *
	 * @param $arProperty
	 * @param $strHTMLControlName
	 * @return string
	 */
	function GetAdminFilterHtml($arProperty, $strHTMLControlName)
	{
		$name = $strHTMLControlName['VALUE'];
		$html = '<select name="'.$name.'" id="'.$name.'">';
		$html .= '<option value="">'.Loc::getMessage('AKLYKOV_CHECKBOX_TITLE_EMPTY').'</option>';
		$html .= '<option value="1">'.Loc::getMessage('AKLYKOV_CHECKBOX_TITLE_YES').'</option>';
		$html .= '<option value="0">'.Loc::getMessage('AKLYKOV_CHECKBOX_TITLE_NO').'</option>';
		$html .= '</select>';
		return $html;
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
		return (empty($value['VALUE']) ? Loc::getMessage('AKLYKOV_CHECKBOX_TITLE_NO') : Loc::getMessage('AKLYKOV_CHECKBOX_TITLE_YES'));
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
		$checked = (!empty($value['VALUE']) ? 'checked': '');
		$html = '<input type="hidden" name="'.$name.'" value="'.self::VALUE_NO.'">';
		$html .= '<label for="'.$name.'"><input type="checkbox" name="'.$name.'" id="'
			.$name.'" value="'.self::VALUE_YES.'" '.$checked.' > '
			.Loc::getMessage('AKLYKOV_CHECKBOX_TITLE_YES').'</label><br>';
		return $html;
	}

	/**
	 * Проверка длины значения
	 *
	 * @param array $arProperty - массив описывающий свойство
	 * @param array $value - массив описывающий значение свойства
	 * @return int - длина
	 */
	function GetLength($arProperty, $value)
	{
		return self::VALUE_LENGTH;
	}
}
