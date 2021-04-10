<?php

use \Bitrix\Main\ModuleManager as ModuleManager;
use \Bitrix\Main\EventManager as EventManager;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\Loader as Loader;
use \Bitrix\Main\Application as Application;

Loc::loadMessages(__FILE__);
class aklykov_usergroup extends CModule
{
	function __construct()
	{
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'aklykov.usergroup';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('AKLYKOV_USERGROUP_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('AKLYKOV_USERGROUP_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('AKLYKOV_USERGROUP_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('AKLYKOV_USERGROUP_PARTNER_URI');

		$this->MODULE_SORT = 2;
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
		if ($this->isVersionD7())
		{
			if (ModuleManager::isModuleInstalled('iblock'))
			{
				ModuleManager::registerModule($this->MODULE_ID);
				$this->InstallFiles();
			}
			else
				$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_USERGROUP_INSTALL_ERROR_IBLOCK'));
		}
		else
			$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_USERGROUP_INSTALL_ERROR_VERSION'));

		$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_USERGROUP_INSTALL_TITLE'),
			$this->getPath().'/install/step.php');
	}

	/**
	 * Удаление модуля
	 */
	function DoUninstall()
	{
		global $APPLICATION;

		ModuleManager::UnRegisterModule($this->MODULE_ID);
		$this->UnInstallFiles();

		$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_USERGROUP_UNINSTALL_TITLE'),
			$this->getPath().'/install/unstep.php');
	}

	function InstallFiles()
	{
		if ($_ENV['COMPUTERNAME'] != 'BX')
		{
			CopyDirFiles($this->getPath().'/install/components', $_SERVER['DOCUMENT_ROOT'].'/local/components', true, true);
		}

		return true;
	}

	function UnInstallFiles()
	{
		if ($_ENV['COMPUTERNAME'] != 'BX')
		{
			DeleteDirFilesEx('/local/components/aklykov.usergroup/');
		}

		return true;
	}
}
