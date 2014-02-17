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

class BooleanNode implements NodeInterface
{
	/**
	 * @var boolean
	 */
	protected $value;

	function __construct($value)
	{
		$this->value = (bool) $value;
	}

	/**
	 * @return boolean
	 *
	 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
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
}
