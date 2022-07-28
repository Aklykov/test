<?php

use \Bitrix\Main\{
	ModuleManager,
	EventManager,
	Loader,
	Application,
	Localization\Loc,
	Config\Option,
	Entity\Base
};

Loc::loadMessages(__FILE__);
class aklykov_reviewscity extends \CModule
{
	function __construct()
	{
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . '/version.php');

		$this->MODULE_ID = 'aklykov.reviewscity';
		$this->MODULE_VERSION = $arModuleInfo['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleInfo['VERSION_DATE'];
		$this->MODULE_NAME = Loc::GetMessage('AKLYKOV_REVIEWSCITY_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::GetMessage('AKLYKOV_REVIEWSCITY_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::GetMessage('AKLYKOV_REVIEWSCITY_PARTNER_NAME');
		$this->PARTNER_URI = Loc::GetMessage('AKLYKOV_REVIEWSCITY_PARTNER_URI');

		$this->MODULE_SORT = 2;
		$this->MODULE_GROUP_RIGHTS = 'Y';
	}

	/**
	 * Имеет ли текущая версия ядра Битрикс поддержку D7 и контроллеры
	 *
	 * @return bool
	 */
	function isVersionD7()
	{
		return \CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '20.00.00');
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
				$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_REVIEWSCITY_INSTALL_ERROR_IBLOCK'));
		}
		else
			$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_REVIEWSCITY_INSTALL_ERROR_VERSION'));

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage('AKLYKOV_REVIEWSCITY_INSTALL_TITLE'),
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
			Loc::getMessage('AKLYKOV_REVIEWSCITY_UNINSTALL_TITLE'),
			$this->getPath().'/install/unstep.php'
		);
	}

	function InstallDB()
	{
		\Bitrix\Main\Loader::includeModule('iblock');

		$CIBlockType = new \CIBlockType();
		$CIBlockType->Add([
			'ID' => 'aklykov_reviewscity',
			'SECTIONS' => 'Y',
			'IN_RSS' => 'N',
			'SORT' => 1,
			'LANG' => [
				'ru' => [
					'NAME' => 'aklykov_reviewscity',
					'SECTION_NAME' => 'Разделы',
					'ELEMENT_NAME' => 'Элементы'
				]
			]
		]);

		// ------------------ ИБ "Отзывы" ------------------
		$CIBlock = new \CIBlock();
		$iblockIdReview = $CIBlock->Add([
			'IBLOCK_TYPE_ID' => 'aklykov_reviewscity',
			'ACTIVE' => 'Y',
			'NAME' => 'Отзывы',
			'CODE' => 'aklykov_reviewscity__reviews',
			'SITE_ID' => 's1',
			'SORT' => 100,
		]);

		Option::set('aklykov.reviewscity', 'IBLOCK_ID_REVIEWS', $iblockIdReview);

		$propertyDescription = \Aklykov\Reviewscity\Properties\City::GetUserTypeDescription();
		$CIBlockProperty = new CIBlockProperty();
		$CIBlockProperty->Add([
			'PROPERTY_TYPE' => $propertyDescription['PROPERTY_TYPE'],
			'USER_TYPE' => $propertyDescription['USER_TYPE'],
			'NAME' => $propertyDescription['DESCRIPTION'],
			'ACTIVE' => 'Y',
			'CODE' => 'CITY',
			'SORT' => 100,
			'MULTIPLE' => 'N',
			'IBLOCK_ID' => $iblockIdReview,
		]);
		$CIBlockProperty->Add([
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => '',
			'NAME' => 'Оценка',
			'ACTIVE' => 'Y',
			'CODE' => 'RATING',
			'SORT' => 200,
			'MULTIPLE' => 'N',
			'IBLOCK_ID' => $iblockIdReview,
		]);

		// ------------------ ИБ "Города" ------------------
		$CIBlock = new \CIBlock();
		$iblockIdCities = $CIBlock->Add([
			'IBLOCK_TYPE_ID' => 'aklykov_reviewscity',
			'ACTIVE' => 'Y',
			'NAME' => 'Города',
			'CODE' => 'aklykov_reviewscity__cities',
			'SITE_ID' => 's1',
			'SORT' => 200,
		]);

		// ------------------ ДЕМО-ДАННЫЕ ------------------
		$demoCities = [
			'Москва',
			'Питер',
			'Новгород',
			'Екатеринбург',
			'Омск',
			'Новосибирск',
			'Красноярск',
			'Иркутск',
		];
		foreach ($demoCities as $demoCity)
		{
			$resultSection = \Bitrix\Iblock\SectionTable::add([
				'IBLOCK_ID' => $iblockIdCities,
				'NAME' => $demoCity,
				'ACTIVE' => 'Y',
				'TIMESTAMP_X' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
			]);

			$CIBlockElement = new CIBlockElement();
			$CIBlockElement->Add([
				'IBLOCK_ID' => $iblockIdReview,
				'IBLOCK_SECTION_ID' => false,
				'ACTIVE' => 'Y',
				'NAME' => 'Отзыв на город '.$demoCity,
				'DETAIL_TEXT' => 'Офигеть какой отзыв на город '.$demoCity,
				'PROPERTY_VALUES' => [
					'CITY' => $resultSection->getId(),
					'RATING' => '5',
				],
			]);
		}
	}

	function UnInstallDB()
	{
		CIBlockType::Delete('aklykov_reviewscity');
	}

	function InstallEvents()
	{
		EventManager::getInstance()->registerEventHandler(
			'iblock',
			'OnIBlockPropertyBuildList',
			$this->MODULE_ID,
			Aklykov\Reviewscity\Properties\City::class,
			'GetUserTypeDescription'
		);
	}

	function UnInstallEvents()
	{
		EventManager::getInstance()->registerEventHandler(
			'iblock',
			'OnIBlockPropertyBuildList',
			$this->MODULE_ID,
			Aklykov\Reviewscity\Properties\City::class,
			'GetUserTypeDescription'
		);
	}
}
