<?php

use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
	return;

Loc::loadMessages(__FILE__);

global $APPLICATION;
?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>"/>
	<input type="hidden" name="id" value="ibs.storenotebook"/>
	<input type="hidden" name="install" value="Y"/>
	<input type="hidden" name="step" value="2"/>
	<?=CAdminMessage::ShowNote(Loc::getMessage('IBS_STORENOTEBOOK_INSTALL_STEP_1_TITLE'));?>
	<p><?=Loc::getMessage('IBS_STORENOTEBOOK_INSTALL_STEP_1_REWRITE')?></p>
	<p>
		<input type="checkbox" name="rewritedata" id="rewritedata" value="Y" checked/>
		<label for="rewritedata"><?=Loc::getMessage('IBS_STORENOTEBOOK_INSTALL_STEP_1_REWRITE_TABLES')?></label>
	</p>
	<p>
		<input type="checkbox" name="loaddemo" id="loaddemo" value="Y" checked/>
		<label for="loaddemo"><?=Loc::getMessage('IBS_STORENOTEBOOK_INSTALL_STEP_1_LOAD_DEMO')?></label>
	</p>
	<input type="submit" name="" value="<?=Loc::getMessage('MOD_INSTALL')?>"/>
</form>
