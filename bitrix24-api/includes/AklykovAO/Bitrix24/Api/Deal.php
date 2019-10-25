<?php

namespace AklykovAO\Bitrix24\Api;

class Deal extends Entity
{
	protected static $entity = '/crm.deal';

	/**
	 * Получить Статус
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->data['STAGE_ID'];
	}

	/**
	 * Получить Товары
	 *
	 * @return array
	 */
	public function getProductRows()
	{
		if ($this->data['ID'] > 0 && !isset($this->data['PRODUCTS_ROWS']))
		{
			$this->data['PRODUCTS_ROWS'] = DealProductRows::get($this->data['ID'])->getData();
		}

		return $this->data['PRODUCTS_ROWS'];
	}
}