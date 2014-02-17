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

namespace Bit3\Contao\Merger2\Constraint\Parser;

use Bit3\Contao\Merger2\Constraint\Node\AndNode;
use Bit3\Contao\Merger2\Constraint\Node\BooleanNode;
use Bit3\Contao\Merger2\Constraint\Node\CallNode;
use Bit3\Contao\Merger2\Constraint\Node\GroupNode;
use Bit3\Contao\Merger2\Constraint\Node\NodeInterface;
use Bit3\Contao\Merger2\Constraint\Node\NotNode;
use Bit3\Contao\Merger2\Constraint\Node\OrNode;
use Bit3\Contao\Merger2\Constraint\Node\StringNode;
use Bit3\Contao\Merger2\Constraint\Node\VariableNode;

class Parser
{
	/**
	 * {@inheritdoc}
	 */
	public function parse(InputStream $stream)
	{
		return $this->parseUntil($stream, InputToken::END_OF_STREAM);
	}

	/**
	 * @param InputStream $stream
	 * @param string $endToken
	 * @param string $_
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
			}
			else {
				$node = $this->parseNode($token, $stream);
			}
		}

		return $node;
	}

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

		if ($token->is(InputToken::VARIABLE)) {
			$name = $token->getValue();
			$node = new VariableNode($name);
			return $node;
		}

		if ($token->is(InputToken::STRING)) {
			$value = $token->getValue();
			$node  = new StringNode($value);
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

			return new CallNode($name, $parameters);
		}

		$this->unexpected($token);
	}

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
	 * @param InputToken $token
	 * @param string     $expected
	 * @param string     $_
	 *
	 * @throws ParserException
	 *
	 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
	 * @SuppressWarnings(PHPMD.ShortVariable)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function unexpected(InputToken $token, $expected = null, $_ = null)
	{
		$expected = func_get_args();
		array_shift($expected);

		$message = 'Unexpected token ' . strtoupper($token->getType());

		if ($token->getValue()) {
			$message .= ', with value "' . $token->getValue() . '"';
		}

		if ($expected) {
			if (count($expected) > 1) {
				$message .= ', expect one of ' . strtoupper(implode(', ', $expected));
			}
			else {
				$message .= ', expect ' . strtoupper($expected[0]);
			}
		}

		throw new ParserException($message);
	}
}
