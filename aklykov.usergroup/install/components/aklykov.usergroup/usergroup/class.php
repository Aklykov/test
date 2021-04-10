<?php
namespace Aklykov\UserGroup;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class UsergroupComponent extends \CBitrixComponent
{
	protected function getComponentPage()
	{
		global $APPLICATION;

		$componentPage = '';

		$arDefaultUrlTemplates404 = array(
			'list' => '',
			'detail' => '#GROUP_ID#/',
		);

		$arDefaultVariableAliases404 = array();

		$arDefaultVariableAliases = array();

		$arComponentVariables = array(
			'GROUP_ID',
			'GROUP_CODE',
		);

		if ($this->arParams['SEF_MODE'] == 'Y')
		{
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
			if (!$componentPage)
			{
				$componentPage = 'list';
				$b404 = true;
			}

			if ($componentPage == 'detail')
			{
				if (isset($arVariables['GROUP_ID']))
					$b404 |= (intval($arVariables['GROUP_ID']).'' !== $arVariables['GROUP_ID']);
				else
					$b404 |= !isset($arVariables['GROUP_CODE']);
			}

			if ($b404 && Loader::includeModule('iblock'))
			{
				$folder404 = str_replace('\\', '/', $this->arParams['SEF_FOLDER']);
				if ($folder404 != '/')
					$folder404 = '/'.trim($folder404, '/ \t\n\r\0\x0B').'/';
				if (mb_substr($folder404, -1) == '/')
					$folder404 .= 'index.php';

				if ($folder404 != $APPLICATION->GetCurPage(true))
				{
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

			if(isset($arVariables['GROUP_ID']) && intval($arVariables['GROUP_ID']) > 0)
				$componentPage = 'detail';
			elseif(isset($arVariables['GROUP_CODE']) && $arVariables['GROUP_CODE'] <> '')
				$componentPage = 'detail';
			else
				$componentPage = 'list';

			$this->arResult = array(
				'FOLDER' => '',
				'URL_TEMPLATES' => array(
					'list' => htmlspecialcharsbx($APPLICATION->GetCurPage()),
					'detail' => htmlspecialcharsbx($APPLICATION->GetCurPage().'?'.$arVariableAliases['GROUP_ID'].'=#GROUP_ID#')
				),
				'VARIABLES' => $arVariables,
				'ALIASES' => $arVariableAliases
			);
		}

		return $componentPage;
	}

	public function executeComponent()
	{
		$componentPage = $this->getComponentPage();
		$this->includeComponentTemplate($componentPage);
	}
}
