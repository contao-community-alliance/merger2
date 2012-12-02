<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Merger2
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModuleMerger2' => 'system/modules/merger2/ModuleMerger2.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'merger_default' => 'system/modules/merger2/templates',
	'mod_merger2'    => 'system/modules/merger2/templates',
));
