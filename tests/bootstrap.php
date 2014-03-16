<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG
 *
 * @package merger2
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license LGPL-3.0+
 */

define('TL_MODE', 'CLI');
error_reporting(E_ALL);

// search the initialize.php
$dir = __DIR__;

while (
	$dir != '.'
	&& $dir != '/'
	&& !is_file($dir . '/system/initialize.php')
	&& !is_file($dir . '/vendor/contao/core/system/initialize.php')
) {
	$dir = dirname($dir);
}

if (is_file($dir . '/system/initialize.php')) {
	require($dir . '/system/initialize.php');
}
else if (is_file($dir . '/vendor/contao/core/system/initialize.php')) {
	require($dir . '/vendor/contao/core/system/initialize.php');
}
else {
	echo 'Could not find initialize.php!';
	exit(1);
}
