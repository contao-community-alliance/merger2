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

use ContaoCommunityAlliance\Merger2\Constraint\Parser\InputStream;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\InputToken;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\ParserException;

class InputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testTokens()
    {
        $stream = new InputStream('$foo()NOT[]!AND&&OR||name true,false"double-quote"\'single-quote\'');

        $this->assertTrue($stream->hasMore());
        $this->assertFalse($stream->isEmpty());

        $token = $stream->next();
        $this->assertEquals(InputToken::VARIABLE, $token->getType());
        $this->assertEquals('foo', $token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::OPEN_BRACKET, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::CLOSE_BRACKET, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::NOT, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::OPEN_SQUARE_BRACKET, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::CLOSE_SQUARE_BRACKET, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::NOT, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::AND_CONJUNCTION, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::AND_CONJUNCTION, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::OR_CONJUNCTION, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::OR_CONJUNCTION, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::STRING, $token->getType());
        $this->assertEquals('name', $token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::TOKEN_SEPARATOR, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::TRUE, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::LIST_SEPARATOR, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::FALSE, $token->getType());
        $this->assertNull($token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::STRING, $token->getType());
        $this->assertEquals('double-quote', $token->getValue());

        $token = $stream->next();
        $this->assertEquals(InputToken::STRING, $token->getType());
        $this->assertEquals('single-quote', $token->getValue());

        $this->assertFalse($stream->hasMore());
        $this->assertTrue($stream->isEmpty());

        $token = $stream->next();
        $this->assertEquals(InputToken::END_OF_STREAM, $token->getType());
        $this->assertNull($token->getValue());
    }

    public function testInvalidToken()
    {
        $this->setExpectedException(
            ParserException::class,
            'Invalid token, expect a "word" character got ;'
        );
        $stream = new InputStream(';');
        $stream->next();
    }

    public function testInvalidAnd()
    {
        $this->setExpectedException(
            ParserException::class,
            'Invalid token, expect & got -'
        );
        $stream = new InputStream('&-');
        $stream->next();
    }

    public function testInvalidOr()
    {
        $this->setExpectedException(
            ParserException::class,
            'Invalid token, expect | got -'
        );
        $stream = new InputStream('|-');
        $stream->next();
    }
}
