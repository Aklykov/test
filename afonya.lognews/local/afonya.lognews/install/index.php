<?php

use Bitrix\Main\Mail\Internal\{EventMessageTable, EventTypeTable};
use \Bitrix\Main\{ModuleManager, EventManager, Loader, Application, Localization\Loc, Entity\Base};

Loc::loadMessages(__FILE__);
class afonya_lognews extends \CModule
{
	function __construct()
	{
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'afonya.lognews';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('AFONYA_LOGNEWS_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('AFONYA_LOGNEWS_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('AFONYA_LOGNEWS_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('AFONYA_LOGNEWS_PARTNER_URI');

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
		return \CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
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
		if ($this->isVersionD7()) {
			if (ModuleManager::isModuleInstalled('iblock')) {
				ModuleManager::registerModule($this->MODULE_ID);
				Loader::includeModule($this->MODULE_ID);
				$this->InstallDB();
				$this->InstallEvents();
			}
			else
				$APPLICATION->ThrowException(Loc::getMessage('AFONYA_LOGNEWS_INSTALL_ERROR_IBLOCK'));
		}
		else
			$APPLICATION->ThrowException(Loc::getMessage('AFONYA_LOGNEWS_INSTALL_ERROR_VERSION'));

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage('AFONYA_LOGNEWS_INSTALL_TITLE'),
			$this->getPath().'/install/step.php'
		);
	}

	/**
	 * Удаление модуля
	 */
	function DoUninstall()
	{
		global $APPLICATION;

		Loader::includeModule($this->MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallEvents();

		ModuleManager::UnRegisterModule($this->MODULE_ID);

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage('AFONYA_LOGNEWS_UNINSTALL_TITLE'),
			$this->getPath().'/install/unstep.php'
		);
	}

	function InstallDB()
	{
		// orm сущности
		if (!Application::getConnection()->isTableExists(\Afonya\Lognews\Entity\LogTable::getTableName())) {
			Base::getInstance(\Afonya\Lognews\Entity\LogTable::class)->createDBTable();
		}

		// почтовые шаблоны
		$lid = Bitrix\Main\Localization\LanguageTable::getList([
			'select' => ['LID'],
			'filter' => ['DEF' => 'Y'],
		])->fetch()['LID'];

		$sid = Bitrix\Main\SiteTable::getList([
			'select' => ['LID'],
			'filter' => ['DEF' => 'Y'],
		])->fetch()['LID'];

		EventTypeTable::add([
			'EVENT_TYPE' => EventTypeTable::TYPE_EMAIL,
			'LID' => $lid,
			'EVENT_NAME' => 'AFONYA_LOGNEWS_REPORT',
			'NAME' => 'Отчет системы логирования действий с новостями',
			'DESCRIPTION' => 'Отчет системы логирования действий с новостями',
			'SORT' => 100,
		]);

		(new \CEventMessage)->Add([
			'ACTIVE' => 'Y',
			'EVENT_NAME' => 'AFONYA_LOGNEWS_REPORT',
			'LID' => [$sid],
			'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
			'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
			'SUBJECT' => '#SITE_NAME#: Отчет системы логирования действий с новостями',
			'MESSAGE' => 'Отчет системы логирования действий с новостями <br><br>#REPORT#',
			'BODY_TYPE' => 'html',
			'SITE_TEMPLATE_ID' => 'mail_join',
		]);

		// агенты
		\CAgent::AddAgent(
			'Afonya\Lognews\Report::Send();',
			$this->MODULE_ID,
			'N',
			86400 * 7,
			date('d.m.Y H:i:s', time() + 86400 * 7),
			'Y',
			date('d.m.Y H:i:s', time() + 86400 * 7),
			1
		);
	}

	function UnInstallDB()
	{
		// orm сущности
		if (Application::getConnection()->isTableExists(\Afonya\Lognews\Entity\LogTable::getTableName())) {
			Application::getConnection()->dropTable(\Afonya\Lognews\Entity\LogTable::getTableName());
		}

		// почтовые шаблоны
		$rsEventMessage = EventMessageTable::getList([
			'select' => ['ID'],
			'filter' => ['EVENT_NAME' => 'AFONYA_LOGNEWS_REPORT'],
		]);
		while ($arEventMessage = $rsEventMessage->fetch()) {
			EventMessageTable::delete($arEventMessage['ID']);
		}
		$rsEventType = EventTypeTable::getList([
			'select' => ['ID'],
			'filter' => ['EVENT_NAME' => 'AFONYA_LOGNEWS_REPORT'],
		]);
		while ($arEventType = $rsEventType->fetch()) {
			EventTypeTable::delete($arEventType['ID']);
		}

		// агенты
		\CAgent::RemoveAgent(
			'Afonya\Lognews\Report::Send();',
			$this->MODULE_ID
		);
	}

	function InstallEvents()
	{
		EventManager::getInstance()->registerEventHandler(
			'iblock',
			'OnAfterIBlockElementAdd',
			$this->MODULE_ID,
			\Afonya\Lognews\EventHandlers::class,
			'onAfterIBlockElementAdd'
		);

		EventManager::getInstance()->registerEventHandler(
			'iblock',
			'OnAfterIBlockElementUpdate',
			$this->MODULE_ID,
			\Afonya\Lognews\EventHandlers::class,
			'onAfterIBlockElementUpdate'
		);

		EventManager::getInstance()->registerEventHandler(
			'iblock',
			'OnAfterIBlockElementDelete',
			$this->MODULE_ID,
			\Afonya\Lognews\EventHandlers::class,
			'onAfterIBlockElementDelete'
		);
	}

	function UnInstallEvents()
	{
		EventManager::getInstance()->unRegisterEventHandler(
			'iblock',
			'OnAfterIBlockElementAdd',
			$this->MODULE_ID,
			\Afonya\Lognews\EventHandlers::class,
			'onAfterIBlockElementAdd'
		);

		EventManager::getInstance()->unRegisterEventHandler(
			'iblock',
			'OnAfterIBlockElementUpdate',
			$this->MODULE_ID,
			\Afonya\Lognews\EventHandlers::class,
			'onAfterIBlockElementUpdate'
		);

		EventManager::getInstance()->unRegisterEventHandler(
			'iblock',
			'OnAfterIBlockElementDelete',
			$this->MODULE_ID,
			\Afonya\Lognews\EventHandlers::class,
			'onAfterIBlockElementDelete'
		);
	}
}
