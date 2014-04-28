<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @link      http://bit3.de
 * @package   bit3/contao-merger2
 * @license   LGPL-3.0+
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
$GLOBALS['TL_LANG']['tl_module']['merger_data_disabled']  = array('<img src="system/themes/default/images/unpublished.gif" width="15" height="15" alt="Deaktiviert" title="Deaktiviert">');
$GLOBALS['TL_LANG']['tl_module']['merger_data_edit']      = array('&nbsp;');

$GLOBALS['TL_LANG']['merger2']['mode']['all']                   = 'Alle auswerten';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstFalse']          = 'Bis zum Ersten, dessen Bedingung als "falsch" ausgewerted wird.';
$GLOBALS['TL_LANG']['merger2']['mode']['upFirstTrue']           = 'Bis zum Ersten, dessen Bedingung als "wahr" ausgewerted wird.';
$GLOBALS['TL_LANG']['merger2']['legend_article']                = 'Inhalte';
$GLOBALS['TL_LANG']['merger2']['article']                       = 'Artikel';
$GLOBALS['TL_LANG']['merger2']['inherit_articles']              = 'Artikel von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles']          = 'Artikel von Elternseite bis zur Wurzel erben';
$GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback']     = 'Artikel der Seite <em>oder</em> Artikel von Elternseite erben';
$GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback'] = 'Artikel der Seite <em>oder</em> Artikel von Elternseite bis zur Wurzel erben';
