<?php

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
