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


/**
 * Add palettes to tl_article
 */
$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = preg_replace(
	'#(\{expert_legend:hide\}.*?);#',
	'$1,inheritable;',
	$GLOBALS['TL_DCA']['tl_article']['palettes']['default']);


/**
 * Add fields to tl_article
 */
$GLOBALS['TL_DCA']['tl_article']['fields']['inheritable'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['inheritable'],
	'exclude'                 => true,
	'default'                 => 1,
	'inputType'               => 'checkbox'
);
