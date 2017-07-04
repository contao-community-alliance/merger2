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
 * Front end modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['Merger2'] = 'ContaoCommunityAlliance\Merger2\Module\ModuleMerger2';


/**
 * Merger2 functions
 */
$GLOBALS['MERGER2_FUNCTION']['language']      = 'Bit3\Contao\Merger2\StandardFunctions::language';
$GLOBALS['MERGER2_FUNCTION']['page']          = 'Bit3\Contao\Merger2\StandardFunctions::page';
$GLOBALS['MERGER2_FUNCTION']['root']          = 'Bit3\Contao\Merger2\StandardFunctions::root';
$GLOBALS['MERGER2_FUNCTION']['pageInPath']    = 'Bit3\Contao\Merger2\StandardFunctions::pageInPath';
$GLOBALS['MERGER2_FUNCTION']['depth']         = 'Bit3\Contao\Merger2\StandardFunctions::depth';
$GLOBALS['MERGER2_FUNCTION']['articleExists'] = 'Bit3\Contao\Merger2\StandardFunctions::articleExists';
$GLOBALS['MERGER2_FUNCTION']['children']      = 'Bit3\Contao\Merger2\StandardFunctions::children';
$GLOBALS['MERGER2_FUNCTION']['platform']      = 'Bit3\Contao\Merger2\StandardFunctions::platform';
