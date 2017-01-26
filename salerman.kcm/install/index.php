<?php

IncludeModuleLangFile(__FILE__);

class salerman_kcm extends CModule {
	var $MODULE_ID = "salerman.kcm";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	protected $arErrors = array();

	/**
	 *
	 */
	function salerman_kcm () {
		$arModuleInfo = array();
		$arModuleInfo = include(__DIR__ . "/version.php");
		$this->MODULE_VERSION = $arModuleInfo["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleInfo["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SALERMAN_MODULE_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SALERMAN_MODULE_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("SALERMAN_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("SALERMAN_PARTNER_URI");
	}

	/**
	 * Установка модуля
	 *
	 * @return bool
	 */
	function DoInstall () {
		$bSuccess = true;
		if ($bSuccess) {
			if ( !IsModuleInstalled ($this->MODULE_ID) ) {
				RegisterModule  ($this->MODULE_ID);
			}
		}
		return $bSuccess;
	}

	/**
	 * Удаление модуля
	 *
	 * @return bool
	 */
	function DoUninstall () {
		$bSuccess = true;
		if ($bSuccess) {
			if ( IsModuleInstalled($this->MODULE_ID) ) {
				UnRegisterModule($this->MODULE_ID);
			}
		}
		return $bSuccess;
	}

}