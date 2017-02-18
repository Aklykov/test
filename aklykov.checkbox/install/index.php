<?php

use \Bitrix\Main\ModuleManager as ModuleManager;
use \Bitrix\Main\EventManager as EventManager;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\Loader as Loader;
use \Bitrix\Main\Application as Application;

Loc::loadMessages(__FILE__);
class aklykov_checkbox extends CModule
{
	function __construct()
	{
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'aklykov.checkbox';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('AKLYKOV_CHECKBOX_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('AKLYKOV_CHECKBOX_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('AKLYKOV_CHECKBOX_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('AKLYKOV_CHECKBOX_PARTNER_URI');

		$this->MODULE_SORT = 1;
		$this->MODULE_GROUP_RIGHTS = 'Y';
	}

	/**
	 * Имеет ли текущая версия ядра Битрикс поддержку D7
	 *
	 * @return bool
	 */
	function isVersionD7()
	{
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}

	/**
	 * Определяем где лежит модуль (полный или относительный)
	 *
	 * @param bool $notDocumentRoot - нужен относительный путь? (по умолчанию-полный)
	 * @return string
	 */
	function getPath($notDocumentRoot=false)
	{
		if($notDocumentRoot)
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		else
			return dirname(__DIR__);
	}

	/**
	 * Установка модуля
	 */
	function DoInstall()
	{
		global $APPLICATION;
		if($this->isVersionD7())
		{
			if(ModuleManager::isModuleInstalled('iblock'))
			{
				ModuleManager::registerModule($this->MODULE_ID);
				$this->InstallEvents();
			}
			else
				$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_CHECKBOX_INSTALL_ERROR_IBLOCK'));

		}
		else
			$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_CHECKBOX_INSTALL_ERROR_VERSION'));

		$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_CHECKBOX_INSTALL_TITLE'),
			$this->getPath().'/install/step.php');
	}

	/**
	 * Удаление модуля
	 */
	function DoUninstall()
	{
		global $APPLICATION;

		$arGet = Application::getInstance()->getContext()->getRequest()->getQueryList()->toArray();

		if($arGet['step'] < 2)
		{
			$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_CHECKBOX_UNINSTALL_TITLE'),
				$this->getPath().'/install/unstep1.php');
		}
		elseif($arGet['step'] == 2)
		{
			$this->UnInstallEvents();

			if($arGet['savedata'] != 'Y')
				$this->UnInstallDB();

			ModuleManager::UnRegisterModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_CHECKBOX_UNINSTALL_TITLE'),
				$this->getPath().'/install/unstep2.php');
		}
	}

	/**
	 * Установка событий
	 */
	function InstallEvents()
	{
		EventManager::getInstance()->registerEventHandler('iblock', 'OnIBlockPropertyBuildList',
			$this->MODULE_ID, 'Aklykov\Checkbox\Checkbox', 'GetUserTypeDescription');
	}

	/**
	 * Удаление событий
	 */
	function UnInstallEvents()
	{
		EventManager::getInstance()->unregisterEventHandler('iblock', 'OnIBlockPropertyBuildList',
			$this->MODULE_ID, 'Aklykov\Checkbox\Checkbox', 'GetUserTypeDescription');
	}

	/**
	 * Удаление из БД данных модуля (Удаление созданных свойств и их значений с типом Да/Нет)
	 */
	function UnInstallDB()
	{
		if(ModuleManager::isModuleInstalled('iblock') && Loader::includeModule('iblock'))
		{
			// Получаем список Свойств ИБ, которые были созданы с типом Да/Нет
			$arPropertyIds = array();
			$params = array(
				'select' => array('ID'),
				'filter' => array(
					'USER_TYPE' => Aklykov\Checkbox\Checkbox::USER_TYPE
				)
			);
			$rsProperty = Bitrix\Iblock\PropertyTable::getList($params);
			while($arProperty = $rsProperty->Fetch())
			{
				// Удаляем само свойство + все его заполненые значения у элементов
				CIBlockProperty::Delete($arProperty['ID']);
			}
		}
	}
}
