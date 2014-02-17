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

class ParserTest extends \PHPUnit_Framework_TestCase
{
	public function testParserVariable()
	{
		$stream = new InputStream('$foo');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new VariableNode('foo'),
			$node
		);
	}

	public function testParserCall()
	{
		$stream = new InputStream('foo()');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new CallNode('foo', array()),
			$node
		);
	}

	public function testParserCallOneParameter()
	{
		$stream = new InputStream('foo(bar)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new CallNode('foo', array(new StringNode('bar'))),
			$node
		);
	}

	public function testParserCallTwoParameter()
	{
		$stream = new InputStream('foo(bar, zap)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new CallNode('foo', array(new StringNode('bar'), new StringNode('zap'))),
			$node
		);
	}

	public function testParserCallComplexParameter()
	{
		$stream = new InputStream('foo(true && false)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new CallNode('foo', array(new AndNode(new BooleanNode(true), new BooleanNode(false)))),
			$node
		);
	}

	public function testParserCallMultipleSimpleAndComplexParameter()
	{
		$stream = new InputStream('foo(yes, true && false, bar(no))');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new CallNode('foo',
				array(
					new StringNode('yes'),
					new AndNode(new BooleanNode(true), new BooleanNode(false)),
					new CallNode('bar', array(new StringNode('no')))
				)
			),
			$node
		);
	}

	public function testParserComplexStatement()
	{
		$stream = new InputStream('foo(yes) || bar(no) && (yes || no)');
		$parser = new Parser();
		$node   = $parser->parse($stream);

		$this->assertEquals(
			new OrNode(
				new CallNode('foo', array(new StringNode('yes'))),
				new AndNode(
					new CallNode('bar', array(new StringNode('no'))),
					new GroupNode(
						new OrNode(
							new StringNode('yes'),
							new StringNode('no')
						)
					)
				)
			),
			$node
		);
	}
}
