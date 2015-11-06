<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @link      http://bit3.de
 * @package   bit3/contao-merger2
 * @license   LGPL-3.0+
 */

require "bootstrap.php";

use Bit3\Contao\Merger2\Constraint\Parser\InputStream;
use Bit3\Contao\Merger2\Constraint\Parser\Parser;

$GLOBALS['objPage'] = (object) array(
	'language' => 'en'
);

$stream = new InputStream('language(de)');
$parser = new Parser();
$node = $parser->parse($stream);
var_dump($node->evaluate());
