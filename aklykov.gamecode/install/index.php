<?php

use \Bitrix\Main\ModuleManager as ModuleManager;
use \Bitrix\Main\EventManager as EventManager;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\Loader as Loader;
use \Bitrix\Main\Application as Application;
use \Bitrix\Main\Config\Option as Option;
use \Bitrix\Main\Config\Configuration as Configuration;
use \Bitrix\Highloadblock as HL;

Loc::loadMessages(__FILE__);
class aklykov_gamecode extends CModule
{
	const MODULE_ID = 'aklykov.gamecode';

	function __construct()
	{
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'aklykov.gamecode';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('AKLYKOV_GAMECODE_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('AKLYKOV_GAMECODE_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('AKLYKOV_GAMECODE_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('AKLYKOV_GAMECODE_PARTNER_URI');

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
			if(ModuleManager::isModuleInstalled('sale') && ModuleManager::isModuleInstalled('highloadblock'))
			{
				ModuleManager::registerModule($this->MODULE_ID);
				$this->InstallFiles();
				$this->InstallEvents();
				$this->InstallDB();
			}
			else
				$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_INSTALL_ERROR_REQUIRED'));

		}
		else
			$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_INSTALL_ERROR_VERSION'));

		$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_GAMECODE_INSTALL_TITLE'),
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
			$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_GAMECODE_UNINSTALL_TITLE'),
				$this->getPath().'/install/unstep1.php');
		}
		elseif($arGet['step'] == 2)
		{
			$this->UnInstallEvents();
			$this->UnInstallFiles();

			if($arGet['savedata'] != 'Y')
				$this->UnInstallDB();

			ModuleManager::UnRegisterModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile(Loc::getMessage('AKLYKOV_GAMECODE_UNINSTALL_TITLE'),
				$this->getPath().'/install/unstep2.php');
		}
	}

	/**
	 * Установка событий
	 */
	function InstallEvents()
	{
		EventManager::getInstance()->registerEventHandler('sale', 'OnOrderStatusSendEmail',
			$this->MODULE_ID, 'Aklykov\Gamecode\Order', 'OnOrderStatusSendEmailHandler');
	}

	/**
	 * Удаление событий
	 */
	function UnInstallEvents()
	{
		EventManager::getInstance()->unregisterEventHandler('sale', 'OnOrderStatusSendEmail',
			$this->MODULE_ID, 'Aklykov\Gamecode\Order', 'OnOrderStatusSendEmailHandler');
	}

	/**
	 * Установка файлов
	 */
	function InstallFiles()
	{
		// Распаковываем папку по умолчанию из которых будет происходить импорт с демо-файлом
		$pathFrom = $this->getPath() . '/upload/import.csv';
		$pathTo = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $this->MODULE_ID.'/import.csv';
		CopyDirFiles( $pathFrom, $pathTo, true, true );
	}

	/**
	 * Удаление файлов
	 */
	function UnInstallFiles()
	{
		// Удаляем шаблон сайта
		$pathTo = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $this->MODULE_ID.'/import.csv';
		DeleteDirFilesEx($pathTo);
	}

	/**
	 * Установка в БД
	 */
	function InstallDB()
	{
		Loader::includeModule('highloadblock');
		Loader::includeModule('sale');
		Loader::includeModule($this->MODULE_ID);

		// Проверяем осталась ли информация в БД (Если уже устанавливали модуль)
		if(!empty(Configuration::getInstance()->getValue($this->MODULE_ID)))
		{
			return true;
		}

		// Массив настроек модуля
		$arConfigSettings = array(
			'HLBLOCK_GAMECODE' => '',
			'PROPS_GROUP' => array(),
			'PROPS' => array(),
			'EVENT_TYPES' => array(),
			'EVENT_MESSAGES' => array(),
		);

		// Создание HL Для хранения "Серийных номеров"
		$arFields = array(
			'NAME' => 'AklykovGamecode',
			'TABLE_NAME' => 'b_aklykov_gamecode',
		);
		$result = HL\HighloadBlockTable::add($arFields);
		$arConfigSettings['HLBLOCK_GAMECODE'] = $result->getId();
		$arFieldsHL = array(
			array(
				'CODE' => 'UF_CODE',
				'NAME' => Loc::getMessage('AKLYKOV_GAMECODE_HL_UF_CODE'),
				'USER_TYPE_ID' => 'string',
				'SIZE' => 70,
				'ROWS' => 1
			),
			array(
				'CODE' => 'UF_ORDER',
				'NAME' => Loc::getMessage('AKLYKOV_GAMECODE_HL_UF_ORDER'),
				'USER_TYPE_ID' => 'string',
				'SIZE' => 70,
				'ROWS' => 1
			),
			array(
				'CODE' => 'UF_EMAIL',
				'NAME' => Loc::getMessage('AKLYKOV_GAMECODE_HL_UF_EMAIL'),
				'USER_TYPE_ID' => 'string',
				'SIZE' => 70,
				'ROWS' => 1
			)
		);
		$obUserField = new \CUserTypeEntity;
		$sort = 100;
		foreach( $arFieldsHL as $arItem )
		{
			$arFields = array(
				'ENTITY_ID' => 'HLBLOCK_'.$arConfigSettings['HLBLOCK_GAMECODE'],
				'FIELD_NAME' => $arItem['CODE'],
				'USER_TYPE_ID' => $arItem['USER_TYPE_ID'],
				'SORT' => $sort,
				'SETTINGS' => array('SIZE' => $arItem['SIZE'], 'ROWS' => $arItem['ROWS']),
				'EDIT_FORM_LABEL' => array('ru' => $arItem['NAME'], 'en' => ''),
				'LIST_COLUMN_LABEL' => array('ru' => $arItem['NAME'], 'en' => ''),
				'LIST_FILTER_LABEL' => array('ru' => $arItem['NAME'], 'en' => ''),
			);
			$sort += 100;
			$obUserField->Add($arFields);
		}

		// Для всех типов плательщиков создаем доп. свойства заказа "Серийный номер"
		$rsPersonType = CSalePersonType::GetList();
		while($arPersonType = $rsPersonType->Fetch())
		{
			// Создаем новую группу свойств
			$arFiledsPropsGroup = array(
				'PERSON_TYPE_ID' => $arPersonType['ID'],
				'NAME' => Loc::getMessage('AKLYKOV_GAMECODE_SALE_PROPS_GROUP_NAME'),
				'SORT' => 1000,
			);
			$PROPS_GROUP = CSaleOrderPropsGroup::Add($arFiledsPropsGroup);
			if($PROPS_GROUP > 0)
			{
				// Запоминаем ID в конфиг
				$arConfigSettings['PROPS_GROUP'][] = $PROPS_GROUP;
				// Создаем в этой группе свойство
				$arFieldsProps = array(
					"PERSON_TYPE_ID" => $arPersonType['ID'],
					"NAME" => Loc::getMessage('AKLYKOV_GAMECODE_SALE_PROPS_NAME'),
					"TYPE" => "TEXT",
					"REQUIED" => "N",
					"DEFAULT_VALUE" => "",
					"SORT" => 1000,
					"PROPS_GROUP_ID" => $PROPS_GROUP,
					"CODE" => "GAMECODE"
				);
				$ID = CSaleOrderProps::Add($arFieldsProps);
				if($ID > 0)
				{
					// Запоминаем ID в конфиг
					$arConfigSettings['PROPS'][] = $ID;
				}
			}
		}

		// Создаем почтовые сообщения
		$langs = CLanguage::GetList(($b=""), ($o=""));
		while($lang = $langs->Fetch())
		{
			$lid = $lang['LID'];
			$objEventType = new CEventType;
			$arFieldsEventType = array(
				'LID' => $lid,
				'EVENT_NAME' => 'AKLYKOV_GAMECODE',
				'NAME' => Loc::getMessage('AKLYKOV_GAMECODE_EVENT_TYPE_NAME'),
				'DESCRIPTION' => Loc::getMessage('AKLYKOV_GAMECODE_EVENT_TYPE_DESC'),
			);
			$EVENT_TYPE_ID = $objEventType->Add($arFieldsEventType);
			// Запоминаем ID в конфиг
			$arConfigSettings['EVENT_TYPES'][] = $EVENT_TYPE_ID;
		}
		$arSites = array();
		$sites = CSite::GetList(($b=''), ($o=''), Array());
		while ($site = $sites->Fetch())
			$arSites[] = $site['LID'];

		$objEventMessage = new CEventMessage;
		$arFieldsEventMessage = array(
			'ACTIVE' => 'Y',
			'EVENT_NAME' => 'AKLYKOV_GAMECODE',
			'LID' => $arSites,
			'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
			'EMAIL_TO' => '#EMAIL_TO#',
			'BCC' => '#BCC#',
			'SUBJECT' => Loc::getMessage('AKLYKOV_GAMECODE_EVENT_MESSAGE_SUBJECT'),
			'BODY_TYPE' => 'text',
			'MESSAGE' => Loc::getMessage('AKLYKOV_GAMECODE_EVENT_MESSAGE_MESSAGE'),
		);
		$EVENT_MESSAGE_ID = $objEventMessage->Add($arFieldsEventMessage);
		// Запоминаем ID в конфиг
		$arConfigSettings['EVENT_MESSAGES'][] = $EVENT_MESSAGE_ID;

		// Сохранения настроек модуля (по-умолчанию)
		Option::set($this->MODULE_ID, 'path_from', '/upload/aklykov.gamecode/import.csv');
		Option::set($this->MODULE_ID, 'path_to', '/upload/aklykov.gamecode/export.csv');

		// Сохраняем ИД HLBLOCK_GAMECODE в настройках сайта
		Configuration::getInstance()->add($this->MODULE_ID, $arConfigSettings);
		Configuration::getInstance()->saveConfiguration();

		$this->InstallDemo();
	}

	/**
	 * Удаление из БД
	 */
	function UnInstallDB()
	{

		// Получаем HL Для хранения "Серийных номеров"
		$arConfigSettings = Configuration::getInstance()->getValue($this->MODULE_ID);

		// Удаляем HL
		if(Loader::includeModule('highloadblock') && !empty($arConfigSettings['HLBLOCK_GAMECODE']))
		{
			HL\HighloadBlockTable::delete($arConfigSettings['HLBLOCK_GAMECODE']);
		}

		// Удаляем группы свойств, свойства и значения свойств заказов
		if(Loader::includeModule('sale'))
		{
			// группы свойств заказа
			foreach($arConfigSettings['PROPS_GROUP'] as $propsGroupId)
			{
				CSaleOrderPropsGroup::delete($propsGroupId);
			}
			// свойства заказа
			foreach($arConfigSettings['PROPS'] as $propsId)
			{
				CSaleOrderProps::delete($propsId);
			}
			// значения свойств заказа
			$arFilterPropsValue = array('CODE' => 'GAMECODE');
			$rsPropsValue = \CSaleOrderPropsValue::GetList(array(), $arFilterPropsValue, false, false, array('ID'));
			while($arPropsValue = $rsPropsValue->Fetch())
			{
				\CSaleOrderPropsValue::Delete($arPropsValue['ID']);
			}
		}

		// Удаляем типы и почтовые события
		CEventType::Delete('AKLYKOV_GAMECODE');
		foreach($arConfigSettings['EVENT_MESSAGES'] as $eventMessageId)
		{
			CEventMessage::Delete($eventMessageId);
		}

		// Удаляем параметры модуля
		$sql = "delete from b_option where `MODULE_ID`='".$this->MODULE_ID."'";
		Application::getConnection()->query($sql);

		// Удаляем ИД HLBLOCK_GAMECODE в настройках сайта
		Configuration::getInstance()->delete($this->MODULE_ID);
		Configuration::getInstance()->saveConfiguration();

	}

	/**
	 * Установка демо-данных
	 */
	function InstallDemo()
	{
		\Bitrix\Main\Loader::includeModule($this->MODULE_ID);
		$objImport = new \Aklykov\Gamecode\Import\Context(new \Aklykov\Gamecode\Import\Csv());
		$objImport->import();
	}

}
