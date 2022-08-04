<?php
namespace Ibs\StoreNotebook;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class StoreNotebookComponent extends \CBitrixComponent
{
	protected function getComponentPage()
	{
		global $APPLICATION;

		$RIGHT = $APPLICATION->GetGroupRight('ibs.storenotebook');
		if ($RIGHT < 'R') {
			ShowError(GetMessage("IBS_STORENOTEBOOK_DONT_HAVE_RIGHTS"));
			return false;
		}

		$componentPage = '';

		$arDefaultUrlTemplates404 = array(
			'list_brand' => '',
			'list_model' => '#BRAND#/',
			'list_notebook' => '#BRAND#/#MODEL#/',
			'detail_notebook' => '#BRAND#/#MODEL#/detail/#NOTEBOOK#/',
		);

		$arDefaultVariableAliases404 = array();

		$arDefaultVariableAliases = array();

		$arComponentVariables = array(
			'BRAND',
			'MODEL',
			'NOTEBOOK',
		);

		if ($this->arParams['SEF_MODE'] == 'Y') {
			$arVariables = array();

			$arUrlTemplates = \CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams['SEF_URL_TEMPLATES']);
			$arVariableAliases = \CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams['VARIABLE_ALIASES']);

			$engine = new \CComponentEngine($this);
			$componentPage = $engine->guessComponentPath(
				$this->arParams['SEF_FOLDER'],
				$arUrlTemplates,
				$arVariables
			);

			$b404 = false;
			if (!$componentPage) {
				$componentPage = 'list_brand';
				$b404 = true;
			}

			if ($b404 && Loader::includeModule('iblock')) {
				$folder404 = str_replace('\\', '/', $this->arParams['SEF_FOLDER']);
				if ($folder404 != '/')
					$folder404 = '/'.trim($folder404, '/ \t\n\r\0\x0B').'/';
				if (mb_substr($folder404, -1) == '/')
					$folder404 .= 'index.php';

				if ($folder404 != $APPLICATION->GetCurPage(true)) {
					// Выдаем 404
					\Bitrix\Iblock\Component\Tools::process404(
						'',
						true,
						true,
						true
					);
					return;
				}
			}

			\CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

			$this->arResult = array(
				'FOLDER' => $this->arParams['SEF_FOLDER'],
				'URL_TEMPLATES' => $arUrlTemplates,
				'VARIABLES' => $arVariables,
				'ALIASES' => $arVariableAliases,
			);
		}
		else
		{
			$arVariableAliases = \CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $this->arParams['VARIABLE_ALIASES']);
			\CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

			$componentPage = '';
			if (
				isset($arVariables['BRAND']) && intval($arVariables['BRAND']) > 0 &&
				isset($arVariables['MODEL']) && intval($arVariables['MODEL']) > 0 &&
				isset($arVariables['NOTEBOOK']) && intval($arVariables['NOTEBOOK']) > 0
			) {
				$componentPage = 'detail_notebook';
			}
			else if(
				isset($arVariables['BRAND']) && intval($arVariables['BRAND']) > 0 &&
				isset($arVariables['MODEL']) && intval($arVariables['MODEL']) > 0
			) {
				$componentPage = 'list_notebook';
			}
			else if(
				isset($arVariables['BRAND']) && intval($arVariables['BRAND']) > 0
			) {
				$componentPage = 'list_model';
			}
			else {
				$componentPage = 'list_brand';
			}

			$currentPage = $APPLICATION->GetCurPage().'?';
			$this->arResult = array(
				'FOLDER' => '',
				'URL_TEMPLATES' => array(
					'list_brand' => $currentPage,
					'list_model' => $currentPage.
						$arVariableAliases['BRAND'].'=#BRAND#',
					'list_notebook' => $currentPage.
						$arVariableAliases['BRAND'].'=#BRAND#'.
						'&'.$arVariableAliases['MODEL'].'=#MODEL#',
					'detail_notebook' => $currentPage.
						$arVariableAliases['BRAND'].'=#BRAND#'.
						'&'.$arVariableAliases['MODEL'].'=#MODEL#'.
						'&'.$arVariableAliases['NOTEBOOK'].'=#NOTEBOOK#',
				),
				'VARIABLES' => $arVariables,
				'ALIASES' => $arVariableAliases
			);
		}

		return $componentPage;
	}

	protected function initNavigationByPage($componentPage = '')
	{
		$this->arResult['GRID_ID'] = 'storenotebook_'.$componentPage;

		$nav = new \Bitrix\Main\UI\PageNavigation($this->arResult['GRID_ID']);
		$nav->setPageSize($this->arParams['COUNT'])
			->initFromUri();

		$this->arResult['COUNT'] = $this->arParams['COUNT'];
		$this->arResult['PAGE'] = $nav->getCurrentPage();
	}

	public function executeComponent()
	{
		$componentPage = $this->getComponentPage();
		if ($componentPage) {
			$this->initNavigationByPage($componentPage);
			$this->includeComponentTemplate($componentPage);
		}
	}
}
