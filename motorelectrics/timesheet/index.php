<?php

require ($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Трудозатраты сотрудников');

$APPLICATION->IncludeComponent(
	'aoklykov:employees.hours',
	'',
	[],
	false
);

require ($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');