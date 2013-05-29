<?php

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
$GLOBALS['TL_LANG']['tl_module']['merger_mode']      = array('Modus', 'Der Auswertemodus.');
$GLOBALS['TL_LANG']['tl_module']['merger_data']      = array('Inhalte', 'Bitte wählen Sie die einzufügenden Inhalte aus.');
$GLOBALS['TL_LANG']['tl_module']['merger_template']  = array('Template', 'Wählen Sie hier das Template für diesen Merger aus.');
$GLOBALS['TL_LANG']['tl_module']['merger_container'] = array('Container einfügen', 'Inhalte in Container einpacken. Der Container ist erforderlich, wenn CSS-Id/Klasse oder Abstände gesetzt werden.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_module']['merger_data_content']   = array('Inhalt');
$GLOBALS['TL_LANG']['tl_module']['merger_data_condition'] = array('Bedingung');
$GLOBALS['TL_LANG']['tl_module']['merger_data_edit'] = array('&nbsp;');

$GLOBALS['TL_LANG']['merger2']['mode']['all']                   = 'Alle auswerten';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstFalse']          = 'Bis zum Ersten, dessen Bedingung als "falsch" ausgewerted wird.';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstTrue']           = 'Bis zum Ersten, dessen Bedingung als "wahr" ausgewerted wird.';
$GLOBALS['TL_LANG']['merger2']['legend_article']                = 'Inhalte';
$GLOBALS['TL_LANG']['merger2']['article']                       = 'Artikel';
$GLOBALS['TL_LANG']['merger2']['inherit_articles']              = 'Artikel von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles']          = 'Artikel von Elternseite bis zur Wurzel erben';
$GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback']     = 'Artikel der Seite <em>oder</em> Artikel von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback'] = 'Artikel der Seite <em>oder</em> Artikel von Elternseite bis zur Wurzel erben';
$GLOBALS['TL_LANG']['merger2']['legend_inherit_module']         = 'Vererbte Module';
$GLOBALS['TL_LANG']['merger2']['inherit_modules']               = 'Module von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_modules']           = 'Module von Elternseite bis zur Wurzel erben';
