<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Merger² - Module Merger
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
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
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Merger²
 * @license    LGPL
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['merger_mode']      = array('Mode', 'The evaluation mode.');
$GLOBALS['TL_LANG']['tl_module']['merger_template']  = array('Template', 'The ');
$GLOBALS['TL_LANG']['tl_module']['merger_container'] = array('Use Container', 'Use a container and wrap it around the modules and articles of this module.');
$GLOBALS['TL_LANG']['tl_module']['merger_data']      = array('Contents', 'Choose the included contents.');

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_module']['label_content']   = 'Content';
$GLOBALS['TL_LANG']['tl_module']['label_condition'] = 'Condition';

$GLOBALS['TL_LANG']['merger2']['mode']['all']                   = 'Evaluate all';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstFalse']          = 'Evaluate up to the first "false" item.';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstTrue']           = 'Evaluate up to the first "true" item.';
$GLOBALS['TL_LANG']['merger2']['legend_article']                = 'Contents';
$GLOBALS['TL_LANG']['merger2']['article']                       = 'Article';
$GLOBALS['TL_LANG']['merger2']['inherit_articles']              = 'Article from parent page';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles']          = 'Article from parent pages';
$GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback']     = 'Article of this site <em>or</em> from parent page';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback'] = 'Article of this site <em>or</em> from parent pages';
$GLOBALS['TL_LANG']['merger2']['legend_inherit_module']         = 'Inherited modules';
$GLOBALS['TL_LANG']['merger2']['inherit_modules']               = 'Module from parent page';
$GLOBALS['TL_LANG']['merger2']['inherit_all_modules']           = 'Module from parent pages';

?>