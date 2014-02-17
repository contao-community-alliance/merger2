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

		$stream = new InputStream('page(impressions)');
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

		$stream = new InputStream('page("your-data-has-been-saved")');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionRootPass()
	{
		$stream = new InputStream('root(1)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('root("music-academy")');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionRootFail()
	{
		$stream = new InputStream('root(2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('root(index)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionPageInPassPass()
	{
		$stream = new InputStream('pageInPath(3)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('pageInPath(academy)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionPageInPassFail()
	{
		$stream = new InputStream('pageInPath(2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('pageInPath(index)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionDepthPass()
	{
		$stream = new InputStream('depth(2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('depth(=2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('depth(>=2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('depth(<=2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('depth(<3)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());

		$stream = new InputStream('depth(>1)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionDepthFail()
	{
		$stream = new InputStream('depth(<2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('depth(>2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('depth(<>2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$stream = new InputStream('depth(!=2)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('depth(<=1)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('depth(>=3)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionArticleExistsPass()
	{
		$stream = new InputStream('articleExists(main)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionArticleExistsFail()
	{
		$stream = new InputStream('articleExists(header)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('articleExists(left)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('articleExists(right)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());

		$stream = new InputStream('articleExists(footer)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}

	public function testPageFunctionChildrenPass()
	{
		$stream = new InputStream('children(0)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertTrue($node->evaluate());
	}

	public function testPageFunctionChildrenFail()
	{
		$stream = new InputStream('children(1)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertFalse($node->evaluate());
	}
}
