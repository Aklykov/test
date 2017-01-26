<?php

use Salerman\Kcm\DocumentTable as DocumentTable;
use Salerman\Kcm\Company as Company;
use Salerman\Kcm\Access as Access;

// admin initialization
define("ADMIN_MODULE_NAME", "salerman.kcm");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CJSCore::Init(array("jquery"));

if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

//if ( !empty($_GET['ID']) && Access::getInstance()->checkRigth__Document($_GET['ID']) === false )
//{
//	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
//}

$is_create_form = true;
$is_update_form = false;

$isEditMode = true;

$errors = array();

$entity = DocumentTable::getEntity();
$entity_data_class = $entity->getDataClass();
$entity_table_name = DocumentTable::getTableName();

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
	$APPLICATION->SetTitle('Создание записи');
}
else
{
	$APPLICATION->SetTitle('Редактирование записи');
}

// form
$aTabs = array(
	array("DIV" => "edit1", "TAB" => 'DocumentTable', "ICON"=>"ad_contract_edit", "TITLE"=> 'DocumentTable')
);

$tabControl = new CAdminForm("hlrow_edit_DocumentTable", $aTabs);

// delete action
if ($is_update_form && isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete' && check_bitrix_sessid())
{

	// if( Access::getInstance()->checkRigth__Document($row['ID']) ) {
		$entity_data_class::delete($row['ID']);
	// }


	LocalRedirect("salerman_documents_list.php?lang=".LANGUAGE_ID);
}

// save action
if ((strlen($save)>0 || strlen($apply)>0) && $REQUEST_METHOD=="POST" && check_bitrix_sessid())
{
	$data = array();
//	$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_DocumentTable', $data);
	$data = array(
		'COMPANY_ID' => $_POST['COMPANY_ID'],
		'PARENT_DOC_ID' => $_POST['PARENT_DOC_ID'],
		'DOC_ID' => $_POST['DOC_ID'],
		'NAME' => $_POST['NAME'],
		'DATE' => \Bitrix\Main\Type\Date::createFromTimestamp(strtotime($_POST['DATE'])),
	);

	/** @param Bitrix\Main\Entity\AddResult $result */
	if ($is_update_form)
	{
		$ID = intval($_REQUEST['ID']);
		// Проверяем право на редактирование документа
		// if( Access::getInstance()->checkRigth__Document($ID) ) {
			// Если прикреплен допник. то проверяем право на родительский документ
			// if( empty($data['PARENT_DOC_ID']) || (!empty($data['PARENT_DOC_ID']) && Access::getInstance()->checkRigth__Document($data['PARENT_DOC_ID'])) ) {
				$result = $entity_data_class::update($ID, $data);
			// }
		// }
	}
	else
	{
		// if( Access::getInstance()->checkRigth__Company($data['COMPANY_ID']) ) {
			$result = $entity_data_class::add($data);
			$ID = $result->getId();
		// }
	}

	if($result->isSuccess())
	{
		if (strlen($save)>0)
		{
			LocalRedirect("salerman_documents_list.php?lang=".LANGUAGE_ID);
		}
		else
		{
			LocalRedirect("salerman_documents_edit.php?ID=".intval($ID)."&lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam());
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
		"TEXT"	=> 'Вернуться в список',
		"TITLE"	=> 'Вернуться в список',
		"LINK"	=> "salerman_documents_list.php?&lang=".LANGUAGE_ID,
		"ICON"	=> "btn_list",
	)
);

$context = new CAdminContextMenu($aMenu);


//view

if ($_REQUEST["mode"] == "list")
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
}
else
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
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
	"FORM_ACTION" => $APPLICATION->GetCurPage()."?ID=".IntVal($ID)."&lang=".LANG
));?>

<? $tabControl->BeginNextFormTab(); ?>

<? $tabControl->AddViewField("ID", "ID", !empty($row)?$row['ID']:''); ?>
<?
// ----------------- ВЫВОДИМ ПОЛЯ ФОРМЫ -----------------

// Список компаний
$arSelectCompany = Company::getSelectMyCompany();
// Список документов (Если выбранна компания)
if( empty($row['COMPANY_ID']) ) {
	$row['COMPANY_ID'] = current(array_keys($arSelectCompany));
}
$arSelectDocumentDefault = array('' => 'Нет');
$arSelectDocument = DocumentTable::getSelectParendDocIdByCompany($row['COMPANY_ID']);;
$arSelectDocument = $arSelectDocumentDefault + $arSelectDocument;

$tabControl->AddDropDownField("COMPANY_ID", "Компания", true, $arSelectCompany, $row['COMPANY_ID']);
$tabControl->AddDropDownField("PARENT_DOC_ID", "Документ (родитель)", false, $arSelectDocument, !empty($row['PARENT_DOC_ID'])?$row['PARENT_DOC_ID']:'');
$tabControl->AddEditField("DOC_ID", "Документ ИД", true, array('size' => 50), !empty($row['DOC_ID'])?$row['DOC_ID']:'');
$tabControl->AddEditField("NAME", "Название", true, array('size' => 50), !empty($row['NAME'])?$row['NAME']:'');
$tabControl->AddCalendarField("DATE", "Дата", !empty($row['DATE'])?$row['DATE']:date('d.m.Y'), true);
?>

<?
$hasSomeFields = true;
?>

<?
$disable = true;
if($isEditMode)
	$disable = false;

if ($hasSomeFields)
{
	$tabControl->Buttons(array("disabled" => $disable, "back_url"=>"highloadblock_rows_list.php?lang=".LANGUAGE_ID));
}
else
{
	$tabControl->Buttons(false);
}


$tabControl->Show();
?>
	</form>

	<script type="text/javascript">
		$(document).ready(function(){
//			updateSelectParentDocId();

			$('[name="COMPANY_ID"]').change(function(){
				updateSelectParentDocId();
			});
		});

		function updateSelectParentDocId() {
			var companyId = $('[name="COMPANY_ID"]').val();
			var data = {
				action: 'getListParendDocIdByCompany',
				companyId: companyId
			};
			$.ajax({
				url: '/ajax/document.php',
				type: 'POST',
				data: data,
				success: function( data ) {
					if( data['result'] === true ) {
						$('[name="PARENT_DOC_ID"]').empty();
						var option = '<option value="">Нет</option>';
						$('[name="PARENT_DOC_ID"]').append(option);
						for( var id in data['data'] ) {
							var name = data['data'][id];
							var option = '<option value="'+id+'">'+name+'</option>';
							$('[name="PARENT_DOC_ID"]').append(option);
						}
					} else {
						console.log( data['message'] );
					}
				},
				dataType: 'json'
			});
		}

	</script>

	<style type="text/css">
		.adm-detail-content-wrap select {
			width: 389px;
		}
	</style>

<?

if ($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");