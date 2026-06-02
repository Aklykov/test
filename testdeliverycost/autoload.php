<?php
/**
 * PSR-4 Autoloader для пространства имен TestDeliveryCost
 * Поддержка путей:
 * - TestDeliveryCost\Order → /lib/Order.php
 * - TestDeliveryCost\Delivery\DeliveryStrategyInterface → /lib/Delivery/DeliveryStrategyInterface.php
 */

spl_autoload_register(function ($class) {
	// Префикс пространства имен
	$prefix = 'TestDeliveryCost\\';

	// Проверяем, что класс относится к нашему пространству имен
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return; // Не наш класс
	}

	// Преобразуем пространство имен в путь к файлу
	$namespace = substr($class, $len); // Убираем префикс
	$file = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . '.php';

	// Полный путь к файлу
	$filePath = __DIR__ . '/lib/' . $file;

	// Проверяем существование файла
	if (file_exists($filePath)) {
		require $filePath;
	} else {
		throw new \RuntimeException(
			"Class {$class} not found in path: {$filePath}"
		);
	}
});
