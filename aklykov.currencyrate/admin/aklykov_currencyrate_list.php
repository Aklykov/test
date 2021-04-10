<?php
use \Bitrix\Main\Entity\Query;
use \Bitrix\Main\Entity\ExpressionField;
use \Aklykov\Currencyrate\CurrencyrateTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

// admin initialization
define('ADMIN_MODULE_NAME', 'aklykov.currencyrate');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

global $APPLICATION, $USER, $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$APPLICATION->SetTitle(Loc::getMessage('AKLYKOV_CURRENCYRATE_TITLE'));

$entity = CurrencyrateTable::getEntity();
$entity_data_class = $entity->getDataClass();
$entity_table_name = CurrencyrateTable::getTableName();

$sTableID = 'tbl_'.$entity_table_name;
$oSort = new CAdminSorting($sTableID, 'ID', 'asc');
$lAdmin = new CAdminList($sTableID, $oSort);

$arHeaders = array();
foreach (CurrencyrateTable::getMap() as $key => $arMap)
{
	$arHeaders[] = array(
		'id' => $arMap->getName(),
		'content' => $arMap->getName(),
		'sort' => $arMap->getName(),
		'default' => true
	);
}

$ufEntityId = 'aklykov_currencyrate';
$USER_FIELD_MANAGER->AdminListAddHeaders($ufEntityId, $arHeaders);

$lAdmin->AddHeaders($arHeaders);

if (!in_array($by, $lAdmin->GetVisibleHeaderColumns(), true))
{
	$by = 'ID';
}

// add filter
$filter = null;

$filterFields = array(
	0 => 'find_ID',
	1 => 'find_CODE',
	2 => 'find_DATE_CREATE',
	3 => 'find_COURSE',
);
$filterTitles = array(
	0 => 'ID',
	1 => 'CODE',
	2 => 'DATE_CREATE',
	3 => 'COURSE',
);

$USER_FIELD_MANAGER->AdminListAddFilterFields($ufEntityId, $filterFields);

$filter = $lAdmin->InitFilter($filterFields);
$filterValues = array();

if (!empty($find_id) && empty($del_filter))
{
	$filterValues['ID'] = $find_id;
}
if (!empty($find_code) && empty($del_filter))
{
	$filterValues['CODE'] = $find_code;
}
if (!empty($find_date_create) && empty($del_filter))
{
	$filterValues['DATE_CREATE'] = $find_date_create;
}
if (!empty($find_course) && empty($del_filter))
{
	$filterValues['COURSE'] = (double) $find_course;
}

$USER_FIELD_MANAGER->AdminListAddFilter($ufEntityId, $filterValues);
$USER_FIELD_MANAGER->AddFindFields($ufEntityId, $filterTitles);

$filter = new CAdminFilter(
	$sTableID.'_filter',
	$filterTitles
);

// group actions
if ($lAdmin->EditAction())
{
	foreach ($FIELDS as $ID=>$arFields)
	{
		$ID = (int)$ID;
		if ($ID <= 0)
			continue;

		if(!$lAdmin->IsUpdated($ID))
			continue;

		$entity_data_class::update($ID, $arFields);
	}
}

if ($arID = $lAdmin->GroupAction())
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = array();

		$rsData = $entity_data_class::getList(array(
			'select' => array('ID'),
			'filter' => $filterValues
		));

		while ($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach ($arID as $ID)
	{
		$ID = (int)$ID;

		if (!$ID)
		{
			continue;
		}

		switch($_REQUEST['action'])
		{
			case 'delete':
				// if( Access::getInstance()->checkRigth__Document($ID) ) {
				$entity_data_class::delete($ID);
				// }
				break;
		}
	}
}

$arr = array('delete' => true);
$lAdmin->AddGroupActionTable($arr);

// select data
/** @var string $order */
$order = strtoupper($order);

$usePageNavigation = true;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel')
{
	$usePageNavigation = false;
}
else
{
	$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage().'?ENTITY_ID='.$ENTITY_ID)
	));
	if ($navyParams['SHOW_ALL'])
	{
		$usePageNavigation = false;
	}
	else
	{
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}
}
$selectFields = $lAdmin->GetVisibleHeaderColumns();
if (!in_array('ID', $selectFields))
	$selectFields[] = 'ID';
$getListParams = array(
	'select' => $selectFields,
	'filter' => $filterValues,
	'order' => array($by => $order)
);

unset($filterValues, $selectFields);
if ($usePageNavigation)
{
	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}

if ($usePageNavigation)
{

	$countQuery = new Query($entity_data_class::getEntity());
	$countQuery->addSelect(new ExpressionField('CNT', 'COUNT(1)'));
	$countQuery->setFilter($getListParams['filter']);
	$totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
	unset($countQuery);
	$totalCount = (int)$totalCount['CNT'];
	if ($totalCount > 0)
	{
		$totalPages = ceil($totalCount/$navyParams['SIZEN']);
		if ($navyParams['PAGEN'] > $totalPages)
			$navyParams['PAGEN'] = $totalPages;
		$getListParams['limit'] = $navyParams['SIZEN'];
		$getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
	}
	else
	{
		$navyParams['PAGEN'] = 1;
		$getListParams['limit'] = $navyParams['SIZEN'];
		$getListParams['offset'] = 0;
	}
}

$rsData = new CAdminResult($entity_data_class::getList($getListParams), $sTableID);
if ($usePageNavigation)
{
	$rsData->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
	$rsData->NavRecordCount = $totalCount;
	$rsData->NavPageCount = $totalPages;
	$rsData->NavPageNomer = $navyParams['PAGEN'];
}
else
{
	$rsData->NavStart();
}
// build list
$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage('PAGES')));
while($arRes = $rsData->NavNext(true, 'f_'))
{

	$row = $lAdmin->AddRow($f_ID, $arRes);

	// Получаем html представления договоров и компаний
	$USER_FIELD_MANAGER->AddUserFields('aklykov_currencyrate', $arRes, $row);

	$can_edit = true;

	$arActions = array();

	$arActions[] = array(
		'ICON' => 'edit',
		'TEXT' => Loc::getMessage($can_edit ? 'MAIN_ADMIN_MENU_EDIT' : 'MAIN_ADMIN_MENU_VIEW'),
		'ACTION' => $lAdmin->ActionRedirect('aklykov_currencyrate_edit.php?&ID='.$f_ID.'&lang='.LANGUAGE_ID),
		'DEFAULT' => true
	);

	$arActions[] = array(
		'ICON'=>'delete',
		'TEXT' => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		'ACTION' => "if(confirm('Удалить запись ?')) ".
			$lAdmin->ActionRedirect('aklykov_currencyrate_edit.php?action=delete&ID='.$f_ID.'&lang='.LANGUAGE_ID.'&'.bitrix_sessid_get())
	);

	$row->AddActions($arActions);
}


// view
$lAdmin->AddAdminContextMenu(array(array(
	'TEXT'	=> Loc::getMessage('AKLYKOV_CURRENCYRATE_ADD_TITLE'),
	'TITLE'	=> Loc::getMessage('AKLYKOV_CURRENCYRATE_ADD_TITLE'),
	'LINK'	=> 'aklykov_currencyrate_edit.php?lang='.LANGUAGE_ID,
	'ICON'	=> 'btn_new'
)));

$lAdmin->CheckListMode();

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

?>
	<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>">
		<?
		$filter->Begin();
		?>
		<tr>
			<td><?=Loc::getMessage('AKLYKOV_CURRENCYRATE_ID')?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage('AKLYKOV_CURRENCYRATE_CODE')?></td>
			<td><input type="text" name="find_code" size="47" value="<?echo htmlspecialcharsbx($find_code)?>"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage('AKLYKOV_CURRENCYRATE_DATE_CREATE')?></td>
			<td><input type="text" name="find_date_create" size="47" value="<?echo htmlspecialcharsbx($find_date_create)?>"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage('AKLYKOV_CURRENCYRATE_COURSE')?></td>
			<td><input type="text" name="find_course" size="47" value="<?echo htmlspecialcharsbx($find_course)?>"></td>
		</tr>
		<?
		$USER_FIELD_MANAGER->AdminListShowFilter($ufEntityId);
		$filter->Buttons(array('table_id'=>$sTableID, 'url'=>$APPLICATION->GetCurPage(), 'form' => 'find_form'));
		$filter->End();
		?>
	</form>

	<style type="text/css">
		.adm-table-setting {
			display: none;
		}
	</style>
<?

$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");