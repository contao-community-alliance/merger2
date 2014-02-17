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

namespace Bit3\Contao\Merger2\Constraint\Node;

class StringNode implements NodeInterface
{
	/**
	 * @var string
	 */
	protected $value;

	function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function evaluate()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->value;
	}
}
