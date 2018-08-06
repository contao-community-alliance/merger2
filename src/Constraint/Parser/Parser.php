<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Constraint\Parser;

use ContaoCommunityAlliance\Merger2\Constraint\Node\AndNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\BooleanNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\CallNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\FloatNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\GroupNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\IntNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\NodeInterface;
use ContaoCommunityAlliance\Merger2\Constraint\Node\NotNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\OrNode;
use ContaoCommunityAlliance\Merger2\Constraint\Node\StringNode;
use ContaoCommunityAlliance\Merger2\Functions\FunctionCollectionInterface;

/**
 * Class Parser.
 */
final class Parser
{
    /**
     * Function collection.
     *
     * @var FunctionCollectionInterface
     */
    private $functionCollection;

    /**
     * Parser constructor.
     *
     * @param FunctionCollectionInterface $functionCollection Function collection.
     */
    public function __construct(FunctionCollectionInterface $functionCollection)
    {
        $this->functionCollection = $functionCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(InputStream $stream)
    {
        return $this->parseUntil($stream, InputToken::END_OF_STREAM);
    }

    /**
     * Parse until end token is given.
     *
     * @param InputStream $stream   Input stream.
     * @param string      $endToken Type of the end token.
     * @param string      $_        Arguments.
     *
     * @return NodeInterface|null
     *
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function parseUntil(InputStream $stream, $endToken, $_ = null)
    {
        $endTokens = func_get_args();
        array_shift($endTokens);

        $node = null;

        while (true) {
            $token = $stream->next();

            if ($token->is(InputToken::TOKEN_SEPARATOR)) {
                continue;
            }

            if (in_array($token->getType(), $endTokens)) {
                $stream->undo($token);

                return $node;
            }

            if ($token->is(InputToken::TOKEN_SEPARATOR)) {
                continue;
            }

            if ($node) {
                $node = $this->parseConjunction($token, $stream, $node);
            } else {
                $node = $this->parseNode($token, $stream);
            }
        }

        return $node;
    }

    /**
     * Parse a node.
     *
     * @param InputToken  $token  Input token.
     * @param InputStream $stream Input stream.
     *
     * @return NodeInterface
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     */
    protected function parseNode(InputToken $token, InputStream $stream)
    {
        if ($token->is(InputToken::TOKEN_SEPARATOR)) {
            $token = $stream->next();
        }

        if ($token->is(InputToken::OPEN_BRACKET)) {
            $node = $this->parseUntil($stream, InputToken::CLOSE_BRACKET);
            $node = new GroupNode($node);
            $stream->next();

            return $node;
        }

        if ($token->is(InputToken::NOT)) {
            $node = $this->parseNode($stream->next(), $stream);
            $node = new NotNode($node);
            $stream->next();

            return $node;
        }

        if ($token->is(InputToken::STRING)) {
            $value = $token->getValue();

            if (is_numeric($value)) {
                $float = floatval($value);

                if ($float && intval($float) != $float) {
                    $node = new FloatNode($float);
                } else {
                    $node = new IntNode((int) $value);
                }
            } else {
                $node = new StringNode($value);
            }

            return $node;
        }

        if ($token->is(InputToken::TRUE)) {
            $node = new BooleanNode(true);

            return $node;
        }

        if ($token->is(InputToken::FALSE)) {
            $node = new BooleanNode(false);

            return $node;
        }

        if ($token->is(InputToken::CALL)) {
            $name = $token->getValue();

            $token = $stream->next();

            if (!$token->is(InputToken::OPEN_BRACKET)) {
                $this->unexpected($token, InputToken::OPEN_BRACKET);
            }

            $parameters = $this->parseList($stream, InputToken::CLOSE_BRACKET);

            return new CallNode($name, $parameters, $this->functionCollection);
        }

        $this->unexpected($token);
    }

    /**
     * Parse a conjunction.
     *
     * @param InputToken    $token  Input token.
     * @param InputStream   $stream Input stream.
     * @param NodeInterface $left   Left node.
     *
     * @return AndNode|OrNode|null
     */
    protected function parseConjunction(InputToken $token, InputStream $stream, NodeInterface $left)
    {
        if ($token->is(InputToken::TOKEN_SEPARATOR)) {
            $token = $stream->next();
        }

        $right = $this->parseUntil(
            $stream,
            InputToken::TOKEN_SEPARATOR,
            InputToken::LIST_SEPARATOR,
            InputToken::CLOSE_BRACKET,
            InputToken::END_OF_STREAM
        );

        if (!$right) {
            return null;
        }

        if ($token->is(InputToken::AND_CONJUNCTION)) {
            $node = new AndNode($left, $right);

            return $node;
        }

        if ($token->is(InputToken::OR_CONJUNCTION)) {
            $node = new OrNode($left, $right);

            return $node;
        }

        $this->unexpected($token);
    }

    /**
     * Parse a list.
     *
     * @param InputStream $stream   Input stream being parsed.
     * @param string      $endToken Type of end token.
     *
     * @return array
     */
    protected function parseList(InputStream $stream, $endToken)
    {
        $items = array();

        while (true) {
            $node = $this->parseUntil($stream, InputToken::LIST_SEPARATOR, $endToken);

            if ($node) {
                $items[] = $node;
            }

            $token = $stream->next();
            if ($token->is($endToken)) {
                break;
            }
        }

        return $items;
    }

    /**
     * Create the unexpected exception.
     *
     * @param InputToken $token    Unexpected input token.
     * @param string     $expected Optional pass an expected value.
     * @param string     $_        List of arguments.
     *
     * @return void
     *
     * @throws ParserException Is always thrown.
     *
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function unexpected(InputToken $token, $expected = null, $_ = null)
    {
        $expected = func_get_args();
        array_shift($expected);

        $message = 'Unexpected token '.strtoupper($token->getType());

        if ($token->getValue()) {
            $message .= ', with value "'.$token->getValue().'"';
        }

        if ($expected) {
            if (count($expected) > 1) {
                $message .= ', expect one of '.strtoupper(implode(', ', $expected));
            } else {
                $message .= ', expect '.strtoupper($expected[0]);
            }
        }

        throw new ParserException($message);
    }
}
