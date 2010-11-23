<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2009-2010 Leo Feyer
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
 * @copyright  2009-2010, InfinityLabs 
 * @author     Tristan Lins <tristan.lins@infinitylabs.de>
 * @package    Merger2
 * @license    LGPL 
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['mergerMode']      = array('Modus', 'Der Auswertemodus.');
$GLOBALS['TL_LANG']['tl_module']['mergerTemplate']  = array('Template', '');
$GLOBALS['TL_LANG']['tl_module']['mergerContainer'] = array('Container einfügen', 'Inhalte in Container einpacken. Der Container ist erforderlich, wenn CSS-Id/Klasse oder Abstände gesetzt werden.');
$GLOBALS['TL_LANG']['tl_module']['mergerData']      = array('Inhalte', 'Bitte wählen Sie die einzufügenden Inhalte aus.');

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_module']['label_content'] = 'Inhalt';
$GLOBALS['TL_LANG']['tl_module']['label_condition'] = 'Bedingung';
$GLOBALS['TL_LANG']['merger2']['mode']['all']          = 'Alle auswerten';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstFalse'] = 'Bis zum Ersten, dessen Bedingung als "falsch" ausgewerted wird.';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstTrue']  = 'Bis zum Ersten, dessen Bedingung als "wahr" ausgewerted wird.';
$GLOBALS['TL_LANG']['merger2']['legend_article'] = 'Inhalte';
$GLOBALS['TL_LANG']['merger2']['article'] = 'Artikel';
$GLOBALS['TL_LANG']['merger2']['inherit_articles'] = 'Artikel von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles'] = 'Artikel von Elternseite bis zur Wurzel erben';
$GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback'] = 'Artikel der Seite <em>oder</em> Artikel von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback'] = 'Artikel der Seite <em>oder</em> Artikel von Elternseite bis zur Wurzel erben';
$GLOBALS['TL_LANG']['merger2']['legend_inherit_module'] = 'Vererbte Module';
$GLOBALS['TL_LANG']['merger2']['inherit_modules'] = 'Module von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_modules'] = 'Module von Elternseite bis zur Wurzel erben';
$GLOBALS['TL_LANG']['merger2']['legend_module'] = 'Module';

?>