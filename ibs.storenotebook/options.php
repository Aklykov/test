<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

/**
 * @global \CMain $APPLICATION
 * @global \CUser $USER
 * @global string $mid
 * @global string $module_id
 * @global string $TRANS_RIGHT
 */
$module_id = 'ibs.storenotebook';

$RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($RIGHT < 'R') {
	return;
}
if (!Main\Loader::includeModule($module_id)) {
	return;
}

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

$Update = !empty($_REQUEST['Update']) ? 'Y' : '';
$Apply = !empty($_REQUEST['Apply']) ? 'Y' : '';
$RestoreDefaults = !empty($_REQUEST['RestoreDefaults']) ? 'Y' : '';

$hasPermissionEdit = true;

if (
	$_SERVER["REQUEST_METHOD"] === "GET" &&
	$hasPermissionEdit &&
	$RestoreDefaults <> '' &&
	check_bitrix_sessid()
) {
	\COption::RemoveOption($module_id);
	$z = \CGroup::GetList("id", "asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch()) {
		$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
	}
}

$arAllOptions = array();

$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET")
	),
	array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
		"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

//region POST Action

if (
	$_SERVER["REQUEST_METHOD"] == "POST" &&
	$Update. $Apply. $RestoreDefaults <> '' &&
	$hasPermissionEdit &&
	check_bitrix_sessid()
) {
	if ($RestoreDefaults <> '') {
		\COption::RemoveOption($module_id);
		$z = \CGroup::GetList("id", "asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch()) {
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
		}
	} else {
		foreach($arAllOptions as $option) {
			if(!is_array($option)) {
				continue;
			}

			$name = $option[0];
			if (!isset($_POST[$name]) && $option[3][0] != "checkbox") {
				continue;
			}

			if ($option[3][0] == "multiselectbox") {
				if (!is_array($_POST[$name])) {
					continue;
				}
				$val = implode(",", $_POST[$name]);
			} else {
				$val = (isset($_POST[$name]) ? (string)$_POST[$name] : '');
				if($option[3][0] == "checkbox" && $val != "Y") {
					$val = "N";
				}
			}

			\COption::SetOptionString($module_id, $name, $val);
		}
		unset($option);
	}

	$Update = $Update. $Apply;
	ob_start();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	ob_end_clean();

	if ($_REQUEST["back_url_settings"] <> '') {
		if (($Apply <> '') || ($RestoreDefaults <> '')) {
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".LANGUAGE_ID."&mid_menu=1&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		} else {
			LocalRedirect($_REQUEST["back_url_settings"]);
		}
	} else {
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".LANGUAGE_ID."&mid_menu=1&".$tabControl->ActiveTabParam());
	}
}

//endregion

//region Form
?>
<form method="post" action="<?= $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&amp;lang=<?=LANGUAGE_ID?>&mid_menu=1">
	<?
	$tabControl->Begin();

	$tabControl->BeginNextTab();

	__AdmSettingsDrawList($module_id, $arAllOptions);

	$tabControl->BeginNextTab();

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");

	$tabControl->Buttons();

	?>
	<input <?=(!$hasPermissionEdit ? "disabled" : '')?> type="submit" name="Update" value="<?=Loc::getMessage("MAIN_SAVE")?>" title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input <?=(!$hasPermissionEdit ? "disabled" : '')?> type="submit" name="Apply" value="<?=Loc::getMessage("MAIN_OPT_APPLY")?>" title="<?=Loc::getMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?
	if ($_REQUEST["back_url_settings"] <> ''):
		?>
		<input <?if ($RIGHT < 'W') echo "disabled" ?> type="button" name="Cancel" value="<?=Loc::getMessage("MAIN_OPT_CANCEL")?>" title="<?=Loc::getMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?= htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
		<?
	endif;
	?>
	<input <?=(!$hasPermissionEdit ? "disabled" : '')?> type="submit" name="RestoreDefaults" title="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" onclick="return confirm('<?= AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?= Loc::getMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
	<?

	$tabControl->End();
?>
</form>
<?
//endregion