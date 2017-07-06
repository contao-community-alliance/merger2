<?php

/**
 * @package    merger2
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoCommunityAlliance\Merger2\Test;

use ContaoCommunityAlliance\Merger2\Constraint\Node\AndNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\BooleanNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\CallNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\GroupNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\OrNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\StringNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\VariableNode;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\InputStream;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\Parser;
use ContaoCommunityAlliance\Merger2\Functions\FunctionCollectionCollection;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserVariable()
    {
        $functions = new FunctionCollectionCollection([]);
        $stream    = new InputStream('$foo');
        $parser    = new Parser($functions);
        $node      = $parser->parse($stream);

        $this->assertEquals(
            new VariableNode('foo'),
            $node
        );
    }

    public function testParserCall()
    {
        $functions = new FunctionCollectionCollection([]);
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
        $functions = new FunctionCollectionCollection([]);
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
        $functions = new FunctionCollectionCollection([]);
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
        $functions = new FunctionCollectionCollection([]);
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
        $functions = new FunctionCollectionCollection([]);
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
        $functions = new FunctionCollectionCollection([]);
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
        $functions = new FunctionCollectionCollection([]);
        $stream = new InputStream('page(6) | page(7) | page(9) | page(10) | depth(>1)');
        $parser = new Parser($functions);
        $node   = $parser->parse($stream);

        $this->assertEquals(
            new OrNode(
                new CallNode('page', array(new StringNode('6')), $functions),
                new OrNode(
                    new CallNode('page', array(new StringNode('7')), $functions),
                    new OrNode(
                        new CallNode('page', array(new StringNode('9')), $functions),
                        new OrNode(
                            new CallNode('page', array(new StringNode('10')), $functions),
                            new CallNode('depth', array(new StringNode('>1')), $functions)
                        )
                    )
                )
            ),
            $node
        );
    }
}
