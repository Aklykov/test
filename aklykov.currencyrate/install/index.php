<?php

use \Bitrix\Main\ModuleManager as ModuleManager;
use \Bitrix\Main\EventManager as EventManager;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\Loader as Loader;
use \Bitrix\Main\Application as Application;

Loc::loadMessages(__FILE__);
class aklykov_currencyrate extends CModule
{
	function __construct()
	{
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'aklykov.currencyrate';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('AKLYKOV_CURRENCYRATE_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('AKLYKOV_CURRENCYRATE_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('AKLYKOV_CURRENCYRATE_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('AKLYKOV_CURRENCYRATE_PARTNER_URI');

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
		if ($this->isVersionD7())
		{
			ModuleManager::registerModule($this->MODULE_ID);
			$this->InstallDB();
			$this->InstallFiles();
		}
		else
			$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_CURRENCYRATE_INSTALL_ERROR_VERSION'));

		$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_CURRENCYRATE_INSTALL_TITLE'),
			$this->getPath().'/install/step.php');
	}

	/**
	 * Удаление модуля
	 */
	function DoUninstall()
	{
		global $APPLICATION;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		ModuleManager::UnRegisterModule($this->MODULE_ID);

		$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_CURRENCYRATE_UNINSTALL_TITLE'),
			$this->getPath().'/install/unstep.php');
	}

	/**
	 * Установка БД
	 */
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$this->errors = $DB->RunSQLBatch($this->getPath().'/install/db/'.$DBType.'/install.sql');

		if ($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode('', $this->errors));
			return false;
		}

		// регаем агента
		$result = \CAgent::AddAgent(
			'\Aklykov\Currencyrate\Import::loadFromCBR();',
			'aklykov.currencyrate',
			'N',
			86400,
			date('d.m.Y H:i:s'),
			'Y',
			date('d.m.Y H:i:s'),
			1
		);
	}

	/**
	 * Удаление из БД данных модуля
	 */
	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$this->errors = $DB->RunSQLBatch($this->getPath().'/install/db/'.$DBType.'/uninstall.sql');

		if ($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode('', $this->errors));
			return false;
		}
	}

	function InstallFiles()
	{
		if ($_ENV['COMPUTERNAME'] != 'BX')
		{
			CopyDirFiles($this->getPath().'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);
		}

		return true;
	}

	function UnInstallFiles()
	{
		if ($_ENV['COMPUTERNAME'] != 'BX')
		{
			DeleteDirFiles($this->getPath().'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
			DeleteDirFilesEx('/local/components/aklykov.usergroup/');
		}

		return true;
	}
}
