<?php

require_once 'config.php';
require_once 'functions.php';

// загружаем классы
spl_autoload_register(function ($class) {
	$class = str_replace('\\', '/', $class);
	include 'classes/' . $class . '.php';
});