<?php

use \Bitrix\Main\{
	ModuleManager,
	EventManager,
	Loader,
	Application,
	Localization\Loc,
	Entity\Base
};

Loc::loadMessages(__FILE__);
class ibs_storenotebook extends CModule
{
	function __construct()
	{
		$arModuleInfo = [];
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'ibs.storenotebook';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('IBS_STORENOTEBOOK_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('IBS_STORENOTEBOOK_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('IBS_STORENOTEBOOK_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('IBS_STORENOTEBOOK_PARTNER_URI');

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
		return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
	}

	/**
	 * Определяем где лежит модуль (полный или относительный)
	 *
	 * @param bool $notDocumentRoot - нужен относительный путь? (по умолчанию-полный)
	 * @return string
	 */
	function getPath($notDocumentRoot=false)
	{
		if ($notDocumentRoot) {
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		} else {
			return dirname(__DIR__);
		}
	}

	/**
	 * Установка модуля
	 */
	function DoInstall()
	{
		global $APPLICATION;

		$arGet = Application::getInstance()->getContext()->getRequest()->getQueryList()->toArray();
		if (!isset($arGet['step'])) {
			$APPLICATION->IncludeAdminFile(Loc::getMessage('IBS_STORENOTEBOOK_INSTALL_TITLE'),
				$this->getPath().'/install/step1.php');
		} elseif ($arGet['step'] == 2) {
			if ($this->isVersionD7()) {
				ModuleManager::registerModule($this->MODULE_ID);
				Loader::includeModule($this->MODULE_ID);

				if ($arGet['rewritedata'] == 'Y') {
					// перезапись таблиц
					$this->UnInstallDB();
					$this->InstallDB();
				} else {
					// создание таблиц
					$this->InstallDB();
				}
				if ($arGet['loaddemo'] == 'Y') {
					// создание демо-данных
					$this->installDemo();
				}

				// установка компонентов
				$this->InstallFiles();

				$APPLICATION->IncludeAdminFile(Loc::getMessage('IBS_STORENOTEBOOK_UNINSTALL_TITLE'),
					$this->getPath().'/install/step2.php');
			} else {
				$APPLICATION->ThrowException(Loc::getMessage('IBS_STORENOTEBOOK_INSTALL_ERROR_VERSION'));
			}
		}
	}

	/**
	 * Удаление модуля
	 */
	function DoUninstall()
	{
		global $APPLICATION;

		$arGet = Application::getInstance()->getContext()->getRequest()->getQueryList()->toArray();

		if (!isset($arGet['step'])) {
			$APPLICATION->IncludeAdminFile(Loc::getMessage('IBS_STORENOTEBOOK_UNINSTALL_TITLE'),
				$this->getPath().'/install/unstep1.php');
		} elseif ($arGet['step'] == 2) {
			Loader::includeModule($this->MODULE_ID);
			if ($arGet['savedata'] != 'Y') {
				$this->UnInstallDB();
			}

			$this->UnInstallFiles();
			ModuleManager::UnRegisterModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile(Loc::getMessage('IBS_STORENOTEBOOK_UNINSTALL_TITLE'),
				$this->getPath().'/install/unstep2.php');
		}
	}

	function InstallDB()
	{
		// создаем ORM-таблицы
		$classEntityList = $this->getClassEntityList();
		foreach ($classEntityList as $classEntity) {
			$tableName = call_user_func($classEntity.'::getTableName');

			if (!Application::getConnection()->isTableExists($tableName)) {
				Base::getInstance($classEntity)->createDBTable();
			}
		}
	}

	function UnInstallDB()
	{
		// удаляем ORM-таблицы
		$classEntityList = $this->getClassEntityList();
		foreach ($classEntityList as $classEntity) {
			$tableName = call_user_func($classEntity.'::getTableName');

			if (Application::getConnection()->isTableExists($tableName)) {
				Application::getConnection()->dropTable($tableName);
			}
		}
	}

	function InstallFiles()
	{
		if ($_ENV['COMPUTERNAME'] != 'BX') {
			CopyDirFiles($this->getPath().'/install/components', $_SERVER['DOCUMENT_ROOT'].'/local/components', true, true);
		}

		return true;
	}

	function UnInstallFiles()
	{
		if ($_ENV['COMPUTERNAME'] != 'BX') {
			DeleteDirFilesEx('/local/components/ibs.storenotebook/');
		}

		return true;
	}

	function getClassEntityList()
	{
		$classEntityList = [];
		$pathToEntity = $this->getPath(true) . '/lib/Entity/*.php';
		$files = glob($pathToEntity);
		foreach ($files as $file) {
			$classEntityList[] = '\\Ibs\\StoreNotebook\\Entity\\'.basename($file, '.php').'Table';
		}

		return $classEntityList;
	}

	function installDemo()
	{
		include 'demo.php';
		foreach ($manufacturerList as $manufacturer) {
			$result = \Ibs\StoreNotebook\Entity\ManufacturerTable::add([
				'NAME' => $manufacturer['name']
			]);
			if ($result->isSuccess()) {
				$manufacturerId = $result->getId();
				foreach ($manufacturer['modelList'] as $model) {
					$result = \Ibs\StoreNotebook\Entity\ModelTable::add([
						'NAME' => $model['name'],
						'MANUFACTURER_ID' => $manufacturerId,
					]);
					if ($result->isSuccess()) {
						$modelId = $result->getId();
						foreach ($model['notebookList'] as $notebook) {
							$result = \Ibs\StoreNotebook\Entity\NotebookTable::add([
								'NAME' => $notebook['name'],
								'YEAR' => $notebook['year'],
								'PRICE' => $notebook['price'],
								'MODEL_ID' => $modelId,
							]);
							if ($result->isSuccess()) {
								$notebookId = $result->getId();
								foreach ($notebook['optionList'] as $optionName) {
									$result = \Ibs\StoreNotebook\Entity\OptionTable::add([
										'NAME' => $optionName,
									]);
									if ($result->isSuccess()) {
										$optionId = $result->getId();
										\Ibs\StoreNotebook\Entity\OptionLinkModelTable::add([
											'MODEL_ID' => $modelId,
											'OPTION_ID' => $optionId,
										]);
										\Ibs\StoreNotebook\Entity\OptionLinkNotebookTable::add([
											'NOTEBOOK_ID' => $notebookId,
											'OPTION_ID' => $optionId,
										]);
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
