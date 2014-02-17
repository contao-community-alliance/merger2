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

namespace Bit3\Contao\Merger2\Test;

use Bit3\Contao\Merger2\Constraint\Node\AndNode;
use Bit3\Contao\Merger2\Constraint\Node\BooleanNode;
use Bit3\Contao\Merger2\Constraint\Node\CallNode;
use Bit3\Contao\Merger2\Constraint\Node\GroupNode;
use Bit3\Contao\Merger2\Constraint\Node\OrNode;
use Bit3\Contao\Merger2\Constraint\Node\StringNode;
use Bit3\Contao\Merger2\Constraint\Node\VariableNode;
use Bit3\Contao\Merger2\Constraint\Parser\InputStream;
use Bit3\Contao\Merger2\Constraint\Parser\Parser;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		// page ID 9 is the "Impressions" page from the music academy
		$GLOBALS['objPage'] = \PageModel::findWithDetails(9);
	}

	public function testLanguageFunctionPass()
	{
		$stream = new InputStream('language(en)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testLanguageFunctionFail()
	{
		$stream = new InputStream('language(de)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionPass()
	{
		$stream = new InputStream('page(9)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionFail()
	{
		$stream = new InputStream('page(12)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionAliasPass()
	{
		$stream = new InputStream('page(impressions)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionAliasFail()
	{
		$stream = new InputStream('page("your-data-has-been-saved")');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}
}
