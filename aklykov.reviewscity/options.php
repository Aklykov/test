<?
use \Bitrix\Main\Localization\Loc;

if (!$USER->IsAdmin()) return;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
IncludeModuleLangFile(__FILE__);

\Bitrix\Main\Loader::includeModule('iblock');
$arIblocks = [];
$arIblocks[0] = '------------------ Выберите ИБ ------------------';
$rsIblock = \Bitrix\Iblock\IblockTable::getList([
	'order' => ['NAME' => 'ASC'],
	'select' => ['ID', 'NAME'],
	'filter' => ['ACTIVE' => 'Y'],
]);
while ($arIblock = $rsIblock->fetch()) {
	$arIblocks[$arIblock['ID']] = $arIblock['NAME'].' ['.$arIblock['ID'].']';
}

$arDefValues = [
	'IBLOCK_ID_REVIEWS' => '',
];

$arAllOptions = array_filter([
	[
		'IBLOCK_ID_REVIEWS',
		Loc::getMessage('IBLOCK_ID_REVIEWS'),
		$arDefValues['IBLOCK_ID_REVIEWS'],
		['selectbox', $arIblocks]
	]
]);
$aTabs = [['DIV' => 'edit1', 'TAB' => Loc::getMessage('MAIN_TAB_SET'), 'ICON' => 'ib_settings', 'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET')]];
$tabControl = new CAdminTabControl('tabControl', $aTabs);

if (($_SERVER['REQUEST_METHOD'] == 'POST') && (($_POST['Update'] || $_POST['Apply'] || $_POST['RestoreDefaults']) > 0) && check_bitrix_sessid()) {
	if ($_POST['RestoreDefaults'] <> '') {
		foreach($arDefValues as $key=>$value) {
			COption::RemoveOption('aklykov.reviewscity', $key);
		}

	} else {
		foreach($arAllOptions as $arOption) {
			if (isset($arOption['section'])) continue;

			$name = $arOption[0];
			$val=$_REQUEST[$name];
			if (($arOption[3][0] == 'checkbox') && ($val != 'Y')) $val = 'N';

			COption::SetOptionString('aklykov.reviewscity', $name, $val, $arOption[1]);
		}
	}
	if (($_POST['Update'] <> '') && ($_REQUEST['back_url_settings'] <> '')) {
		LocalRedirect($_REQUEST['back_url_settings']);

	} else {
		LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . urlencode($mid) . '&lang=' . urlencode(LANGUAGE_ID) . '&back_url_settings=' . urlencode($_REQUEST['back_url_settings']) . '&' . $tabControl->ActiveTabParam());
	}
}

$tabControl->Begin();
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
	<?$tabControl->BeginNextTab();
	foreach ($arAllOptions as $arOption):
		if (isset($arOption['section'])) {
			echo <<<HTML
            <tr class="heading">
                <td colspan="2">{$arOption['section']}</td>
            </tr>
HTML;
			continue;
		}

		$val = null;
		if (!is_array($arOption)) {
			$arOption = [null, $arOption, null, ['heading']];

		} else {
			$val = COption::GetOptionString('aklykov.reviewscity', $arOption[0], $arOption[2]);
		}

		$type = $arOption[3];
		if ($type[0] == 'heading'):?>
			<tr class="heading">
			<td colspan="2"><?=$arOption[1]?></td>
			</tr><?
		else:?>
			<tr>
			<td width="40%" nowrap <?=$type[0] == 'textarea' ? 'class="adm-detail-valign-top"' : ''?>>
				<label for="<?=htmlspecialcharsbx($arOption[0])?>"><?=$arOption[1]?>:</label>
			<td width="60%"><?
				$readonly = '';
				if (in_array($arOption[0], ['access_token', 'access_token_expires', 'refresh_token'])) $readonly = 'readonly';
				if ($type[0]=='checkbox'):?>
					<input type="checkbox" id="<?=htmlspecialcharsbx($arOption[0])?>" name="<?=htmlspecialcharsbx($arOption[0])?>" value="Y"<?=$val == 'Y' ? ' checked' : ''?>><?

				elseif ($type[0] == 'text'):?>
					<input type="text" size="<?=$type[1]?>" maxlength="255" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($arOption[0])?>" <?=$readonly?>><?

				elseif ($type[0] == 'textarea'):?>
					<textarea rows="<?=$type[1]?>" cols="<?=$type[2]?>" name="<?=htmlspecialcharsbx($arOption[0])?>" <?=$readonly?>><?=htmlspecialcharsbx($val)?></textarea><?

				elseif ($type[0] == 'selectbox'):?>
					<select name="<?=htmlspecialcharsbx($arOption[0])?>"><?
					foreach ($type[1] as $key => $value) {
						?><option value='<?=$key?>'<?=$key == $val ? ' selected' : '' ?>><?=$value?></option><?
					}?>
					</select><?
				endif?>
				&nbsp;<?=$notices[$arOption[0]] ?: ''?>
			</td>
			</tr><?
		endif;
		if ($noticeBlock[$arOption[0]]):?>
			<tr>
			<td colspan="2" align="center">
				<div class="adm-info-message-wrap" align="center">
					<div class="adm-info-message"><?=$noticeBlock[$arOption[0]]?></div>
				</div>
			</td>
			</tr><?
		endif;
	endforeach;

	$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=Loc::getMessage('MAIN_SAVE')?>" title="<?=Loc::getMessage('MAIN_OPT_SAVE_TITLE')?>" class="adm-btn-save">
	<input type="submit" name="Apply" value="<?=Loc::getMessage('MAIN_OPT_APPLY')?>" title="<?=Loc::getMessage('MAIN_OPT_APPLY_TITLE')?>"><?
	if ($_REQUEST['back_url_settings'] <> ''):?>
		<input type="button" name="Cancel" value="<?=Loc::getMessage('MAIN_OPT_CANCEL')?>" title="<?=Loc::getMessage('MAIN_OPT_CANCEL_TITLE')?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST['back_url_settings']))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST['back_url_settings'])?>"><?
	endif?>
	<input type="submit" name="RestoreDefaults" title="<?=Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS')?>" onclick="return confirm('<?=AddSlashes(Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>')" value="<?=Loc::getMessage('MAIN_RESTORE_DEFAULTS')?>">
	<?=bitrix_sessid_post();?><?
	$tabControl->End();?>
</form>