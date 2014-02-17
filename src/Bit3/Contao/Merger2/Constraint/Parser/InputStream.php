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

/**
 * The parser input stream.
 */
class InputStream
{
	/**
	 * @var string
	 */
	protected $input;

	protected $stack = array();

	function __construct($input)
	{
		$this->input = $input;
	}

	/**
	 * Return the remaining length of the input.
	 *
	 * @return int
	 */
	public function length()
	{
		return mb_strlen($this->input);
	}

	public function hasMore()
	{
		return $this->length() > 0;
	}

	public function isEmpty()
	{
		return $this->length() == 0;
	}

	public function undo(InputToken $token)
	{
		array_unshift($this->stack, $token);
	}

	/**
	 * Read the next char from the input.
	 *
	 * @return InputToken
	 */
	public function next()
	{
		if ($this->stack) {
			return array_shift($this->stack);
		}

		if ($this->isEmpty()) {
			return new InputToken(InputToken::END_OF_STREAM);
		}

		$char = $this->head();

		if ($char == ' ' || $char == "\t" || $char == "\n") {
			$this->input = ltrim($this->input);
			return new InputToken(InputToken::TOKEN_SEPARATOR);
		}

		if ($char == ',') {
			$this->skip();
			return new InputToken(InputToken::LIST_SEPARATOR);
		}

		if ($char == '(') {
			$this->skip();
			return new InputToken(InputToken::OPEN_BRACKET);
		}

		if ($char == ')') {
			$this->skip();
			return new InputToken(InputToken::CLOSE_BRACKET);
		}

		if ($char == '[') {
			$this->skip();
			return new InputToken(InputToken::OPEN_SQUARE_BRACKET);
		}

		if ($char == ']') {
			$this->skip();
			return new InputToken(InputToken::CLOSE_SQUARE_BRACKET);
		}

		if ($char == '&') {
			$this->skip();
			$this->expect('&');
			return new InputToken(InputToken::AND_CONJUNCTION);
		}

		if ($char == '|') {
			$this->skip();
			$this->expect('|');
			return new InputToken(InputToken::OR_CONJUNCTION);
		}

		if ($char == '!') {
			$this->skip();

			// special sequence behavior
			if ($this->head() == '=') {
				$sequence = $char . $this->readWordSequence();
				return new InputToken(InputToken::STRING, $sequence);
			}

			return new InputToken(InputToken::NOT);
		}

		if ($char == '$') {
			$this->skip();
			$name = $this->readWordSequence();
			return new InputToken(InputToken::VARIABLE, $name);
		}

		if ($char == '"' || $char == "'") {
			$this->skip();
			$sequence = $this->readQuotedSequence();
			return new InputToken(InputToken::STRING, $sequence);
		}

		$this->expectWordCharacter($char);

		$sequence = $this->readWordSequence();

		$lowerSequence = strtolower($sequence);

		if ($lowerSequence == 'and') {
			return new InputToken(InputToken::AND_CONJUNCTION);
		}

		if ($lowerSequence == 'or') {
			return new InputToken(InputToken::OR_CONJUNCTION);
		}

		if ($lowerSequence == 'not') {
			return new InputToken(InputToken::NOT);
		}

		if ($lowerSequence == 'true') {
			return new InputToken(InputToken::TRUE);
		}

		if ($lowerSequence == 'false') {
			return new InputToken(InputToken::FALSE);
		}

		if ($this->head() == '(') {
			return new InputToken(InputToken::CALL, $sequence);
		}

		return new InputToken(InputToken::STRING, $sequence);
	}

	protected function readQuotedSequence()
	{
		$buffer = '';
		$escape = false;

		while (strlen($this->input)) {
			$char = $this->read();

			if ($escape) {
				$buffer .= $char;
				$escape = false;
			}
			else if ($char == '\\') {
				$escape = true;
			}
			else if ($char == '"' || $char == "'") {
				return $buffer;
			}
			else {
				$buffer .= $char;
			}
		}

		throw new InputStreamException('Unexpected end of quoted sequence');
	}

	protected function readWordSequence()
	{
		$buffer = '';

		while (strlen($this->input)) {
			$char = $this->head();

			if (!$this->checkWordCharacter($char)) {
				break;
			}

			$buffer .= $char;
			$this->skip();
		}

		return $buffer;
	}

	protected function checkWordCharacter($char)
	{
		return preg_match('~^[\w<>=!]$~', $char);
	}

	protected function expectWordCharacter($char)
	{
		if (!$this->checkWordCharacter($char)) {
			throw new ParserException('Invalid token, expect a "word" character got ' . $char);
		}
	}

	protected function expect($char)
	{
		$readChar = $this->read();

		if ($char != $readChar) {
			throw new ParserException('Invalid token, expect ' . $char . ' got ' . $readChar);
		}
	}

	protected function head()
	{
		return mb_substr($this->input, 0, 1);
	}

	protected function skip()
	{
		$this->input = mb_substr($this->input, 1);
	}

	protected function read()
	{
		$char = $this->head();
		$this->skip();
		return $char;
	}
}
