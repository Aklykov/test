<?php

namespace Complex;

class TestResult
{
	/** @var bool */
	private $isSuccess;

	/** @var string */
	private $report;

	public function __construct(bool $isSuccess, string $report = '')
	{
		$this->isSuccess = $isSuccess;
		$this->report = $report;
	}

	/**
	 * Верно ли утверждение
	 *
	 * @return bool
	 */
	public function isSuccess()
	{
		return $this->isSuccess;
	}

	/**
	 * Получить текст отчета
	 *
	 * @return string
	 */
	public function getReport()
	{
		return $this->report;
	}

	/**
	 * Задать текст отчета
	 *
	 * @param string $report
	 */
	public function setReport($report='')
	{
		$this->report = $report;
	}
}