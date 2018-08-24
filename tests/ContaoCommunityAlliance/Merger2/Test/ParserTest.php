<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Test;

use ContaoCommunityAlliance\Merger2\Constraint\Node\AndNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\BooleanNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\CallNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\GroupNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\IntNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\OrNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\StringNode;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\InputStream;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\Parser;
use ContaoCommunityAlliance\Merger2\Functions\FunctionCollection;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParserCall()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('foo()');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new CallNode('foo', array(), $functions),
            $node
        );
    }

    public function testParserCallOneParameter()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('foo(bar)');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new CallNode('foo', array(new StringNode('bar')), $functions),
            $node
        );
    }

    public function testParserCallTwoParameter()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('foo(bar, zap)');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new CallNode('foo', array(new StringNode('bar'), new StringNode('zap')), $functions),
            $node
        );
    }

    public function testParserCallComplexParameter()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('foo(true && false)');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new CallNode('foo', array(new AndNode(new BooleanNode(true), new BooleanNode(false))), $functions),
            $node
        );
    }

    public function testParserCallMultipleSimpleAndComplexParameter()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('foo(yes, true && false, bar(no))');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new CallNode('foo',
                array(
                    new StringNode('yes'),
                    new AndNode(new BooleanNode(true), new BooleanNode(false)),
                    new CallNode('bar', array(new StringNode('no')), $functions)
                ),
                $functions
            ),
            $node
        );
    }

    public function testParserComplexStatement()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('foo(yes) || bar(no) && (yes || no)');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new OrNode(
                new CallNode('foo', array(new StringNode('yes')), $functions),
                new AndNode(
                    new CallNode('bar', array(new StringNode('no')), $functions),
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

    public function testIssue9()
    {
        $functions = new FunctionCollection([]);
        $stream = new InputStream('page(6) | page(7) | page(9) | page(10) | depth(>1)');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new OrNode(
                new CallNode('page', array(new IntNode(6)), $functions),
                new OrNode(
                    new CallNode('page', array(new IntNode(7)), $functions),
                    new OrNode(
                        new CallNode('page', array(new IntNode(9)), $functions),
                        new OrNode(
                            new CallNode('page', array(new IntNode(10)), $functions),
                            new CallNode('depth', array(new StringNode('>1')), $functions)
                        )
                    )
                )
            ),
            $node
        );
    }
}
