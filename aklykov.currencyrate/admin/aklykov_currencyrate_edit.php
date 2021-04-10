<?php

use \Aklykov\Currencyrate\CurrencyrateTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

// admin initialization
define('ADMIN_MODULE_NAME', 'aklykov.currencyrate');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

Loc::loadMessages(__FILE__);

if (!Loader::includeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$is_create_form = true;
$is_update_form = false;

$isEditMode = true;

$errors = array();

$entity = CurrencyrateTable::getEntity();
$entity_data_class = $entity->getDataClass();
$entity_table_name = CurrencyrateTable::getTableName();

// get row
$row = null;

if (isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0)
{
	$row = $entity_data_class::getById($_REQUEST['ID'])->fetch();

	if (!empty($row))
	{
		$is_update_form = true;
		$is_create_form = false;
	}
	else
	{
		$row = null;
	}
}

if ($is_create_form)
{
	$APPLICATION->SetTitle(Loc::getMessage('AKLYKOV_CURRENCYRATE_TITLE_CREATE'));
}
else
{
	$APPLICATION->SetTitle(Loc::getMessage('AKLYKOV_CURRENCYRATE_TITLE_EDIT'));
}

// form
$aTabs = array(
	array('DIV' => 'edit1', 'TAB' => 'CurrencyrateTable', 'ICON'=>'ad_contract_edit', 'TITLE'=> 'CurrencyrateTable')
);

$tabControl = new CAdminForm('hlrow_edit_CurrencyrateTable', $aTabs);

// delete action
if ($is_update_form && isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete' && check_bitrix_sessid())
{
	$entity_data_class::delete($row['ID']);
	LocalRedirect('aklykov_currencyrate_list.php?lang='.LANGUAGE_ID);
}

// save action
if ((strlen($save)>0 || strlen($apply)>0) && $REQUEST_METHOD=='POST' && check_bitrix_sessid())
{
	$data = array(
		'CODE' => $_POST['CODE'],
		'DATE_CREATE' => \Bitrix\Main\Type\Date::createFromTimestamp(strtotime($_POST['DATE_CREATE'])),
		'COURSE' => $_POST['COURSE'],
	);

	/** @param Bitrix\Main\Entity\AddResult $result */
	if ($is_update_form)
	{
		$ID = intval($_REQUEST['ID']);
		$result = $entity_data_class::update($ID, $data);
	}
	else
	{
		$result = $entity_data_class::add($data);
		$ID = $result->getId();
	}

	if ($result->isSuccess())
	{
		if (strlen($save)>0)
		{
			LocalRedirect('aklykov_currencyrate_list.php?lang='.LANGUAGE_ID);
		}
		else
		{
			LocalRedirect('aklykov_currencyrate_edit.php?ID='.intval($ID).'&lang='.LANGUAGE_ID.'&'.$tabControl->ActiveTabParam());
		}
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

// menu
$aMenu = array(
	array(
		'TEXT'	=> Loc::getMessage('AKLYKOV_CURRENCYRATE_BACK'),
		'TITLE'	=> Loc::getMessage('AKLYKOV_CURRENCYRATE_BACK'),
		'LINK'	=> 'aklykov_currencyrate_list.php?&lang='.LANGUAGE_ID,
		'ICON'	=> 'btn_list',
	)
);
$context = new CAdminContextMenu($aMenu);

//view
if ($_REQUEST['mode'] == 'list')
{
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');
}
else
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
}

$context->Show();
if (!empty($errors))
{
	CAdminMessage::ShowMessage(join("\n", $errors));
}

$tabControl->BeginPrologContent();
echo $USER_FIELD_MANAGER->ShowScript();
echo CAdminCalendar::ShowScript();
$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
?>

<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?=htmlspecialcharsbx(!empty($row)?$row['ID']:'')?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">

<?$tabControl->EndEpilogContent();?>
<? $tabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->GetCurPage().'?ID='.IntVal($ID).'&lang='.LANG
));?>
<? $tabControl->BeginNextFormTab(); ?>
<? $tabControl->AddViewField('ID', 'ID', !empty($row)?$row['ID']:''); ?>
<?
// ----------------- ВЫВОДИМ ПОЛЯ ФОРМЫ -----------------
$tabControl->AddEditField('CODE', 'Код валюты', true, array('size' => 47), $row['CODE']);
$tabControl->AddCalendarField('DATE_CREATE', 'Дата', !empty($row['DATE_CREATE']) ? $row['DATE_CREATE'] : date('d.m.Y'), true);
$tabControl->AddEditField('COURSE', 'Курс', true, array('size' => 47), $row['COURSE']);

$hasSomeFields = true;
$disable = true;
if($isEditMode)
	$disable = false;

if ($hasSomeFields)
{
	$tabControl->Buttons(array('disabled' => $disable, 'back_url'=>'highloadblock_rows_list.php?lang='.LANGUAGE_ID));
}
else
{
	$tabControl->Buttons(false);
}

$tabControl->Show();
?>
	</form>

	<style type="text/css">
		.adm-detail-content-wrap select {
			width: 389px;
		}
	</style>

<?
if ($_REQUEST['mode'] == 'list')
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');
else
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');