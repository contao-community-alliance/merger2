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
 * An input token.
 */
class InputToken
{
	const OPEN_BRACKET = 'open_bracket';

	const CLOSE_BRACKET = 'close_bracket';

	const OPEN_SQUARE_BRACKET = 'open_square_bracket';

	const CLOSE_SQUARE_BRACKET = 'close_square_bracket';

	const AND_CONJUNCTION = 'and_conjunction';

	const OR_CONJUNCTION = 'or_conjunction';

	const NOT = 'not';

	const QUOTE = 'quote';

	const STRING = 'string';

	const TRUE = 'true';

	const FALSE = 'false';

	const CALL = 'call';

	const VARIABLE = 'variable';

	const TOKEN_SEPARATOR = 'token_separator';

	const LIST_SEPARATOR = 'list_separator';

	const END_OF_STREAM = 'end_of_stream';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $value;

	function __construct($type, $value = null)
	{
		$this->type  = $type;
		$this->value = $value;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 *
	 * @SuppressWarnings(PHPMD.ShortMethodName)
	 */
	public function is($type)
	{
		return $this->type == $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
}
