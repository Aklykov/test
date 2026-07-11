<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('tasks');
\Bitrix\Main\Loader::includeModule('socialnetwork');

class EmployeesHoursComponent extends CBitrixComponent
{
	const GRID_ID = 'EMPLOYEE_HOURS_GRID';

	public function executeComponent()
	{
		$this->arResult['GRID_ID'] = self::GRID_ID;
		$this->arResult['COLUMNS'] = [
			[
				'id' => 'ID',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_ID'),
				'sort' => 'ID',
				'default' => true
			],
			[
				'id' => 'FIO',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_FIO'),
				'default' => true
			],
			[
				'id' => 'DEPARTMENT',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_DEPARTMENT'),
				'default' => true
			],
			[
				'id' => 'PROJECT',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_PROJECT'),
				'default' => true
			],
			[
				'id' => 'TASK',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_TASK'),
				'default' => true
			],
			[
				'id' => 'HOURS',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_HOURS'),
				'sort' => 'MINUTES',
				'default' => true
			],
			[
				'id' => 'CREATED_DATE',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_CREATED_DATE'),
				'sort' => 'CREATED_DATE',
				'default' => true
			]
		];
		$this->arResult['COLUMNS_FILTER'] = [
			[
				'id' => 'CREATED_DATE',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_CREATED_DATE'),
				'type' => 'date'
			],
			[
				'id' => 'USER_ID',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_USER_ID'),
				'type' => 'entity_selector',
				'params' => [
					'multiple' => 'Y',
					'dialogOptions' => [
						'context' => 'EMPLOYEE_HOURS',
						'entities' => [
							[
								'id' => 'user'
							]
						]
					]
				]
			],
			[
				'id' => 'TASK',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_TASK'),
				'type' => 'string',
			],
			[
				'id' => 'PROJECT',
				'name' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_COLUMNS_TITLE_PROJECT'),
				'type' => 'entity_selector',
				'params' => [
					'multiple' => 'Y',
					'dialogOptions' => [
						'context' => 'EMPLOYEE_HOURS',
						'entities' => [
							[
								'id' => 'project',
								'options' => [
									'project' => true,
								]
							]
						]
					]
				]
			]
		];

		$this->arResult['ROWS'] = [];
		$rows = $this->loadData();
		foreach ($rows as $row) {
			$this->arResult['ROWS'][] = [
				'id' => $row['ID'],
				'columns' => $row
			];
		}

		$this->includeComponentTemplate();
	}

	private function loadData(): array
	{
		// навигация
		$gridOptions = new GridOptions(self::GRID_ID);
		$navParams = $gridOptions->GetNavParams();
		$nav = new PageNavigation(self::GRID_ID);
		$nav
			->allowAllRecords(false)
			->setPageSize($navParams['nPageSize'])
			->initFromUri();
		$this->arResult['NAV_OBJECT'] = $nav;

		// сортировка
		$sorting = $gridOptions->GetSorting([
			'sort' => [
				'DATE' => 'DESC'
			]
		]);

		// фильтрация
		$filter = [];
		$filterOption = new FilterOptions(self::GRID_ID);
		$filterData = $filterOption->getFilter([]);
		if (!empty($filterData['USER_ID'])) {
			$filter['USER_ID'] = $filterData['USER_ID'];
		}
		if (!empty($filterData['CREATED_DATE_from'])) {
			$filter['>=CREATED_DATE'] = new \Bitrix\Main\Type\DateTime($filterData['CREATED_DATE_from']);
		}
		if (!empty($filterData['CREATED_DATE_to'])) {
			$filter['<=CREATED_DATE'] = new \Bitrix\Main\Type\DateTime($filterData['CREATED_DATE_to']);
		}
		if (!empty($filterData['TASK'])) {
			$filter['%TASK.TITLE'] = $filterData['TASK'];
		}
		if (!empty($filterData['PROJECT'])) {
			$filter['TASK.GROUP.ID'] = $filterData['PROJECT'];
		}

		// запрос к БД
		$rows = [];
		$items = [];
		$userIds = [];
		$rsItem = Bitrix\Tasks\Internals\Task\ElapsedTimeTable::getList([
			'select' => [
				'ID',
				'USER_ID',
				'USER_SHORT_NAME' => 'USER.SHORT_NAME',
				'CREATED_DATE',
				'MINUTES',
				'TASK_TITLE' => 'TASK.TITLE',
				'TASK_GROUP_NAME' => 'TASK.GROUP.NAME',
			],
			'filter' => $filter,
			'order' => $sorting['sort'],
			'count_total' => true,
			'offset' => $nav->getOffset(),
			'limit' => $nav->getLimit(),
		]);
		$nav->setRecordCount($rsItem->getCount());
		while ($item = $rsItem->fetch()) {
			$items[] = $item;

			if (!in_array($item['USER_ID'], $userIds)) {
				$userIds[] = $item['USER_ID'];
			}
		}

		// запрашиваем отделы сотрудников
		$userIdDepId = [];
		if (!empty($userIds)) {
			$rsUser = \Bitrix\Main\UserTable::getList([
				'select' => ['ID', 'UF_DEPARTMENT'],
				'filter' => ['ID' => $userIds],
			]);
			while ($user = $rsUser->fetch()) {
				$userIdDepId[$user['ID']] = $user['UF_DEPARTMENT'][0];
			}
		}
		$depIdDepName = [];
		if (!empty($userIdDepId)) {
			$rsDep = \Bitrix\Iblock\SectionTable::getList([
				'select' => ['ID', 'NAME', 'IBLOCK_CODE' => 'IBLOCK.CODE'],
				'filter' => [
					'ID' => $userIdDepId,
					'=IBLOCK_CODE' => 'departments',
				]
			]);
			while ($dep = $rsDep->fetch()) {
				$depIdDepName[$dep['ID']] = $dep['NAME'];
			}
		}

		// формируем массив итоговый
		foreach ($items as &$item) {
			$rows[] = [
				'ID' => $item['ID'],
				'FIO' => '<a href="/company/personal/user/'.$item['USER_ID'].'/">'.$item['USER_SHORT_NAME'].'</a>',
				'DEPARTMENT' => $depIdDepName[$userIdDepId[$item['USER_ID']]],
				'PROJECT' => $item['TASK_GROUP_NAME'],
				'TASK' => $item['TASK_TITLE'],
				'HOURS' => $this->formatMinutesToHours($item['MINUTES']),
				'CREATED_DATE' => $item['CREATED_DATE']->format('d.m.Y H:i:s'),
			];
		}

		unset($userIdDepId);
		unset($depIdDepName);
		unset($items);
		unset($userIds);

		return $rows;
	}

	private function formatMinutesToHours(int $minutes): string
	{
		$hours = intdiv($minutes, 60);

		$remaining_minutes = $minutes % 60;

		return sprintf("%02dч %02dм", $hours, $remaining_minutes);
	}
}