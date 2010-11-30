<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Merger2
 * @license    http://opensource.org/licenses/lgpl-3.0.html
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

?>