<?php

/**
 * Функция для печати массива
 *
 * @param string $str
 */
function d($str='') : void
{
	print_r('<pre>');
	print_r($str);
	print_r('</pre>');
}